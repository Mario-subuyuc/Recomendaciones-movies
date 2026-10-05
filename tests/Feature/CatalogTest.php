<?php

namespace Tests\Feature;

use App\Models\Pelicula;
use App\Models\User;
use App\Models\Videojuego;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CatalogTest extends TestCase
{
    use RefreshDatabase;

    public function test_administrator_can_manage_both_catalogs(): void
    {
        $this->seed();
        $this->actingAs(User::where('email', 'mariosubuyucfb@gmail.com')->first());
        foreach (['peliculas' => Pelicula::class, 'videojuegos' => Videojuego::class] as $module => $model) {
            $record = $model::first();
            $this->assertSame($module === 'peliculas' ? 50 : 69, $model::count());
            $this->get('/dashboard/'.$module)->assertOk();
            $this->get('/dashboard/'.$module.'/create')->assertOk();
            $this->get('/dashboard/'.$module.'/'.$record->getKey())->assertOk();
            $this->get('/dashboard/'.$module.'/'.$record->getKey().'/edit')->assertOk();
            $data = $record->only($record->getFillable());
            $data['fecha_registro'] = '2026-10-04';
            $data['titulo'] = 'Nuevo registro';
            $this->post('/dashboard/'.$module, $data)->assertSessionHasNoErrors()->assertRedirect('/dashboard/'.$module);
            $created = $model::where('titulo', 'Nuevo registro')->firstOrFail();
            $data['titulo'] = 'Editado';
            $this->put('/dashboard/'.$module.'/'.$created->getKey(), $data)->assertSessionHasNoErrors()->assertRedirect();
            $this->assertSame('Editado', $created->fresh()->titulo);
            $this->delete('/dashboard/'.$module.'/'.$created->getKey())->assertRedirect();
            $this->assertModelMissing($created);
            $data['calificacion'] = 11;
            $data['anio_lanzamiento'] = 'invalid';
            $this->post('/dashboard/'.$module, $data)->assertSessionHasErrors(['calificacion', 'anio_lanzamiento']);
            $this->get('/dashboard/'.$module.'/99999')->assertNotFound();
        }
    }

    public function test_roles_and_direct_permissions_are_enforced(): void
    {
        $this->seed();
        foreach (['peliculas' => Pelicula::class, 'videojuegos' => Videojuego::class] as $module => $model) {
            $record = $model::first();
            $url = '/dashboard/'.$module;
            $this->get($url)->assertRedirect('/login');
            $client = User::where('email', 'holamariost@gmail.com')->first();
            $client->syncRoles('Cliente');
            $this->actingAs($client);
            $this->get($url)->assertOk();
            $this->get($url.'/create')->assertForbidden();
            $this->post($url, [])->assertForbidden();
            $this->get($url.'/'.$record->getKey().'/edit')->assertForbidden();
            $this->put($url.'/'.$record->getKey(), [])->assertForbidden();
            $this->delete($url.'/'.$record->getKey())->assertForbidden();
            $client->givePermissionTo($module.'.crear');
            $this->get($url.'/create')->assertForbidden();
            $role = Role::create(['name' => 'Crear '.$module, 'guard_name' => 'web']);
            $role->syncPermissions([$module.'.ver', $module.'.crear']);
            $client->syncRoles($role);
            $this->get($url.'/create')->assertOk();
            $this->actingAs(User::where('email', 'msubuyuct@miumg.edu.gt')->first());
            $this->get($url.'/create')->assertOk();
            $this->get($url.'/'.$record->getKey().'/edit')->assertOk();
            $this->delete($url.'/'.$record->getKey())->assertForbidden();
            $this->post('/logout');
        }
    }
}
