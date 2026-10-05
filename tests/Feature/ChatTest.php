<?php

namespace Tests\Feature;

use App\Models\ConsumoToken;
use App\Models\Conversacion;
use App\Models\User;
use App\Models\Videojuego;
use App\Services\ContadorPalabras;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ChatTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        config(['services.groq.api_key' => 'clave-ficticia-test', 'services.groq.model' => 'modelo-test']);
        Http::preventStrayRequests();
    }

    private function usuario(): User
    {
        return User::where('email', 'holamariost@gmail.com')->firstOrFail();
    }

    private function salida(string $content): array
    {
        return ['choices' => [['message' => ['content' => $content], 'finish_reason' => 'stop']], 'usage' => ['prompt_tokens' => 123, 'completion_tokens' => 45, 'total_tokens' => 168]];
    }

    private function filtros(string $categoria = 'videojuegos', array $filtros = [], int $cantidad = 3): string
    {
        return json_encode(['categoria' => $categoria, 'estado' => 'consulta', 'cantidad' => $cantidad, 'filtros' => $filtros]);
    }

    public function test_missing_or_invalid_provider_usage_is_unknown_and_never_estimated(): void
    {
        $first = $this->salida($this->filtros());
        unset($first['usage']);
        $second = $this->salida('Resultados del catálogo.');
        $second['usage']['total_tokens'] = 999;
        Http::fakeSequence()->push($first)->push($second);
        $this->actingAs($this->usuario())->postJson('/dashboard/chat', ['categoria' => 'videojuegos', 'pregunta' => 'Dame videojuegos'])->assertOk();
        $this->assertSame(2, ConsumoToken::count());
        $this->assertSame(2, ConsumoToken::whereNull('tokens_reales')->count());
        $this->assertGreaterThan(0, ConsumoToken::sum('tokens'));
        $this->get('/dashboard/consumo')->assertViewHas('consumo', ['peliculas' => 0, 'videojuegos' => 0]);
    }

    public function test_real_usage_survives_invalid_response_content(): void
    {
        $response = $this->salida('');
        Http::fakeSequence()->push($response);
        $this->actingAs($this->usuario())->postJson('/dashboard/chat', ['categoria' => 'peliculas', 'pregunta' => 'Dame películas'])->assertStatus(503);
        $this->assertSame(168, ConsumoToken::firstOrFail()->tokens_reales);
        $this->assertSame(0, Conversacion::count());
    }

    public function test_chat_routes_require_authentication_and_catalog_permission(): void
    {
        foreach (['chat', 'historial', 'consumo'] as $url) {
            $this->get('/dashboard/'.$url)->assertRedirect('/login');
        }
        $this->postJson('/dashboard/chat', ['categoria' => 'peliculas', 'pregunta' => 'Drama'])->assertUnauthorized();
        $this->actingAs(User::factory()->create())->get('/dashboard/chat')->assertForbidden();
        $this->actingAs($this->usuario())->get('/dashboard/chat')->assertOk();
    }

    public function test_strict_rating_filter_and_word_consumption_use_only_relevant_catalog(): void
    {
        $game = Videojuego::first();
        $game->update(['genero' => 'Disparos', 'calificacion' => 8.5]);
        $other = Videojuego::skip(1)->first();
        $other->update(['genero' => 'Disparos', 'calificacion' => 8.6]);
        $json = $this->filtros('videojuegos', [['campo' => 'genero', 'operador' => 'contiene', 'valor' => 'disparos'], ['campo' => 'calificacion', 'operador' => '>', 'valor' => 8.5]]);
        Http::fakeSequence()->push($this->salida($json))->push($this->salida('Se encontró un juego de disparos.'));
        $this->actingAs($this->usuario())->postJson('/dashboard/chat', ['categoria' => 'videojuegos', 'pregunta' => '¿Qué juegos de disparos tienen una calificación mayor a 8.5?', 'id_usuario' => 999])->assertOk();
        $conversation = Conversacion::firstOrFail();
        $this->assertSame($this->usuario()->id, $conversation->id_usuario);
        $this->assertCount(2, $conversation->mensajes);
        $calls = Http::recorded();
        $context = $calls[1][0]['messages'][1]['content'];
        $this->assertStringContainsString($other->titulo, $context);
        $this->assertStringNotContainsString($game->titulo, $context);
        $this->assertStringNotContainsString('holamariost', $context);
        $contador = new ContadorPalabras;
        foreach ([$json, 'Se encontró un juego de disparos.'] as $index => $output) {
            $input = implode("\n", array_column($calls[$index][0]['messages'], 'content'));
            $usage = ConsumoToken::orderBy('id_consumo')->skip($index)->firstOrFail();
            $this->assertSame($contador->contar($input) + $contador->contar($output), $usage->tokens);
            $this->assertSame(123, $usage->tokens_reales_entrada);
            $this->assertSame(45, $usage->tokens_reales_salida);
            $this->assertSame(168, $usage->tokens_reales);
            $this->assertSame('videojuegos', $usage->categoria);
            $this->assertSame($conversation->getKey(), $usage->id_conversacion);
        }
    }

    public function test_empty_results_are_sent_as_empty_and_queries_have_no_memory(): void
    {
        $json = $this->filtros('peliculas', [['campo' => 'titulo', 'operador' => '=', 'valor' => 'Inexistente']]);
        Http::fakeSequence()->push($this->salida($json))->push($this->salida('No hay coincidencias.'))
            ->push($this->salida($this->filtros('videojuegos')))->push($this->salida('Resultados del catálogo.'));
        $this->actingAs($this->usuario());
        $this->postJson('/dashboard/chat', ['categoria' => 'peliculas', 'pregunta' => 'Busca la película Inexistente'])->assertOk()->assertJsonPath('respuesta', 'No hay coincidencias en el catálogo para los filtros de tu pregunta.');
        $this->postJson('/dashboard/chat', ['categoria' => 'videojuegos', 'pregunta' => 'Dame 3 videojuegos'])->assertOk();
        $calls = Http::recorded();
        $this->assertStringContainsString('"registros":[]', $calls[1][0]['messages'][1]['content']);
        $this->assertStringNotContainsString('Inexistente', $calls[2][0]['messages'][1]['content']);
        $this->assertSame(2, Conversacion::count());
        $this->assertSame(2, ConsumoToken::where('categoria', 'peliculas')->count());
        $this->assertSame(2, ConsumoToken::where('categoria', 'videojuegos')->count());
    }

    public function test_invalid_input_and_contradictory_category_do_not_call_groq(): void
    {
        Http::fake();
        $this->actingAs($this->usuario());
        foreach (['', '   ', str_repeat('a', 1001)] as $question) {
            $this->postJson('/dashboard/chat', ['categoria' => 'peliculas', 'pregunta' => $question])->assertUnprocessable();
        }
        $this->postJson('/dashboard/chat', ['categoria' => 'usuarios', 'pregunta' => 'Hola'])->assertUnprocessable();
        $this->postJson('/dashboard/chat', ['categoria' => 'peliculas', 'pregunta' => 'Dame películas y videojuegos'])->assertUnprocessable();
        Http::assertNothingSent();
    }

    public function test_invalid_columns_and_operators_are_never_executed(): void
    {
        Http::fakeSequence()->push($this->salida($this->filtros('peliculas', [['campo' => 'password', 'operador' => '=', 'valor' => 'x']])));
        $this->actingAs($this->usuario())->postJson('/dashboard/chat', ['categoria' => 'peliculas', 'pregunta' => 'Dame películas'])->assertUnprocessable();
        $this->assertSame(0, Conversacion::count());
        $this->assertSame(1, ConsumoToken::count());
    }

    public function test_failed_answer_preserves_interpretation_consumption_without_history(): void
    {
        Http::fakeSequence()->push($this->salida($this->filtros('peliculas')))->push(['error' => ['message' => 'detalle privado']], 429);
        $this->actingAs($this->usuario())->postJson('/dashboard/chat', ['categoria' => 'peliculas', 'pregunta' => 'Dame películas'])->assertStatus(429)->assertDontSee('detalle privado');
        $this->assertSame(1, ConsumoToken::count());
        $this->assertNull(ConsumoToken::first()->id_conversacion);
        $this->assertSame(0, Conversacion::count());
    }

    public function test_authentication_timeout_and_invalid_responses_are_safe(): void
    {
        $this->actingAs($this->usuario());
        foreach ([401, 500] as $status) {
            Http::fake(['*' => Http::response(['error' => 'private-data'], $status)]);
            $this->postJson('/dashboard/chat', ['categoria' => 'peliculas', 'pregunta' => 'Dame películas'])->assertStatus(503)->assertDontSee('private-data');
        }
        Http::fake(['*' => Http::failedConnection()]);
        $this->postJson('/dashboard/chat', ['categoria' => 'peliculas', 'pregunta' => 'Dame películas'])->assertStatus(503);
        Http::fake(['*' => Http::response(['choices' => []])]);
        $this->postJson('/dashboard/chat', ['categoria' => 'peliculas', 'pregunta' => 'Dame películas'])->assertStatus(503);
        $this->assertSame(0, ConsumoToken::count());
        $this->assertSame(0, Conversacion::count());
    }

    public function test_history_and_consumption_are_isolated_and_foreign_detail_is_hidden(): void
    {
        Http::fakeSequence()->push($this->salida($this->filtros('peliculas')))->push($this->salida('Respuesta privada de prueba.'));
        $this->actingAs($this->usuario())->postJson('/dashboard/chat', ['categoria' => 'peliculas', 'pregunta' => 'Mi pregunta privada'])->assertOk();
        $conversation = Conversacion::firstOrFail();
        $this->get('/dashboard/historial')->assertSee('Mi pregunta privada');
        $this->get('/dashboard/consumo')->assertViewHas('consumo', fn ($data) => $data['peliculas'] > 0 && $data['videojuegos'] === 0);
        $this->actingAs(User::where('email', 'mariosubuyucfb@gmail.com')->first());
        $this->get('/dashboard/historial')->assertDontSee('Mi pregunta privada');
        $this->get('/dashboard/historial/'.$conversation->getKey())->assertNotFound();
        $this->get('/dashboard/consumo')->assertViewHas('consumo', ['peliculas' => 0, 'videojuegos' => 0]);
    }

    public function test_missing_configuration_and_rate_limit_do_not_call_the_provider(): void
    {
        config(['services.groq.api_key' => '']);
        Http::fake();
        $this->actingAs($this->usuario());
        for ($index = 0; $index < 6; $index++) {
            $this->postJson('/dashboard/chat', ['categoria' => 'peliculas', 'pregunta' => 'Dame películas'])->assertStatus(503);
        }
        $this->postJson('/dashboard/chat', ['categoria' => 'peliculas', 'pregunta' => 'Dame películas'])->assertStatus(429);
        Http::assertNothingSent();
    }

    public function test_category_permissions_and_concurrent_requests_are_checked(): void
    {
        Http::fake();
        $user = User::factory()->create();
        $role = Role::create(['name' => 'Consulta películas', 'guard_name' => 'web']);
        $role->givePermissionTo('peliculas.ver');
        $user->syncRoles($role);
        $this->actingAs($user)->postJson('/dashboard/chat', ['categoria' => 'videojuegos', 'pregunta' => 'Dame videojuegos'])->assertForbidden();
        $lock = Cache::lock('chat-usuario-'.$user->id, 75);
        $lock->get();
        $this->postJson('/dashboard/chat', ['categoria' => 'peliculas', 'pregunta' => 'Dame películas'])->assertStatus(429);
        $lock->release();
        Http::assertNothingSent();
    }
}
