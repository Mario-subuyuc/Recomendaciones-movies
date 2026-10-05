<?php

namespace App\Services;

use App\Exceptions\ErrorChat;
use App\Models\ConsumoToken;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class GroqService
{
    public function __construct(private ContadorPalabras $contador) {}

    public function completar(array $mensajes, int $usuario, string $categoria, string $consulta, string $etapa): string
    {
        $key = config('services.groq.api_key');
        $model = config('services.groq.model');
        if (! is_string($key) || trim($key) === '' || ! is_string($model) || trim($model) === '') {
            throw new ErrorChat('El asistente todavía no está configurado. Contacta al administrador.');
        }
        $entrada = implode("\n", array_column($mensajes, 'content'));
        if (mb_strlen($entrada) > 24000) {
            throw new ErrorChat('La consulta contiene demasiada información. Usa filtros más específicos.', 422);
        }
        try {
            // Sin reintentos automáticos: no duplicar llamadas ni consumo desconocido.
            $response = Http::withToken($key)->acceptJson()->asJson()->connectTimeout(5)
                ->timeout(config('services.groq.timeout'))->post(config('services.groq.endpoint'), [
                    'model' => $model, 'messages' => $mensajes, 'temperature' => 0,
                    'max_completion_tokens' => $etapa === 'interpretacion' ? 700 : 1500,
                ]);
        } catch (ConnectionException) {
            throw new ErrorChat('El asistente tardó demasiado o no pudo conectarse. Inténtalo de nuevo.');
        }
        if (in_array($response->status(), [401, 403], true)) {
            throw new ErrorChat('El servicio de IA no pudo autorizar la conexión. Contacta al administrador.');
        }
        if ($response->status() === 429) {
            throw new ErrorChat('El servicio de IA alcanzó su límite de solicitudes. Espera un momento.', 429);
        }
        if (! $response->successful()) {
            throw new ErrorChat('El servicio de IA no está disponible. Revisa la configuración del modelo o inténtalo más tarde.');
        }
        $real = $response->json('usage');
        $realValido = is_array($real)
            && is_int($real['prompt_tokens'] ?? null) && $real['prompt_tokens'] >= 0
            && is_int($real['completion_tokens'] ?? null) && $real['completion_tokens'] >= 0
            && is_int($real['total_tokens'] ?? null) && $real['total_tokens'] >= 0
            && $real['total_tokens'] === $real['prompt_tokens'] + $real['completion_tokens'];
        $content = $response->json('choices.0.message.content');
        $contentValido = is_string($content) && trim($content) !== '' && mb_check_encoding($content, 'UTF-8');
        // Guardar el consumo reportado aunque el contenido de la respuesta sea inválido.
        if (! $contentValido && $realValido) {
            ConsumoToken::create([
                'id_usuario' => $usuario, 'categoria' => $categoria, 'consulta_uuid' => $consulta,
                'etapa' => $etapa, 'tokens_entrada' => $this->contador->contar($entrada), 'tokens_salida' => 0,
                'tokens' => $this->contador->contar($entrada), 'fecha' => now(),
                'tokens_reales_entrada' => $real['prompt_tokens'], 'tokens_reales_salida' => $real['completion_tokens'],
                'tokens_reales' => $real['total_tokens'],
            ]);
        }
        if (! is_string($content) || trim($content) === '' || ! mb_check_encoding($content, 'UTF-8')) {
            throw new ErrorChat('El asistente devolvió una respuesta inválida. Inténtalo de nuevo.');
        }
        $in = $this->contador->contar($entrada);
        $out = $this->contador->contar($content);
        // Contar la llamada verificable incluso si su JSON o respuesta posterior no es válido.
        ConsumoToken::create([
            'id_usuario' => $usuario, 'categoria' => $categoria, 'consulta_uuid' => $consulta,
            'etapa' => $etapa, 'tokens_entrada' => $in, 'tokens_salida' => $out,
            'tokens' => $in + $out, 'fecha' => now(),
            'tokens_reales_entrada' => $realValido ? $real['prompt_tokens'] : null,
            'tokens_reales_salida' => $realValido ? $real['completion_tokens'] : null,
            'tokens_reales' => $realValido ? $real['total_tokens'] : null,
        ]);
        if ($response->json('choices.0.finish_reason') !== 'stop' || mb_strlen($content) > 12000) {
            throw new ErrorChat('La respuesta quedó incompleta. Reduce la cantidad de resultados e inténtalo de nuevo.');
        }

        return trim($content);
    }
}
