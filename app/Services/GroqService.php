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
        $content = $response->json('choices.0.message.content');
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
        ]);
        if ($response->json('choices.0.finish_reason') !== 'stop' || mb_strlen($content) > 12000) {
            throw new ErrorChat('La respuesta quedó incompleta. Reduce la cantidad de resultados e inténtalo de nuevo.');
        }

        return trim($content);
    }
}
