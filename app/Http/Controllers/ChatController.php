<?php

namespace App\Http\Controllers;

use App\Exceptions\ErrorChat;
use App\Http\Requests\PreguntarChatRequest;
use App\Models\Conversacion;
use App\Services\ChatCatalogo;
use App\Services\ResumenConsumo;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class ChatController extends Controller
{
    public function index(Request $request): View
    {
        return view('chat.consultar', ['categorias' => array_filter(['peliculas' => 'Películas', 'videojuegos' => 'Videojuegos'], fn ($label, $category) => $request->user()->can($category.'.ver'), ARRAY_FILTER_USE_BOTH)]);
    }

    public function preguntar(PreguntarChatRequest $request, ChatCatalogo $chat): JsonResponse
    {
        $lock = Cache::lock('chat-usuario-'.$request->user()->id, 75);
        if (! $lock->get()) {
            return response()->json(['message' => 'Ya tienes una consulta en proceso. Espera a que termine.'], 429);
        }
        try {
            $conversation = $chat->preguntar($request->user(), $request->validated('categoria'), $request->validated('pregunta'));

            return response()->json(['respuesta' => $conversation->mensajes->firstWhere('rol', 'asistente')->contenido, 'id_conversacion' => $conversation->getKey()]);
        } catch (ErrorChat $error) {
            return response()->json(['message' => $error->getMessage()], $error->estado);
        } finally {
            $lock->release();
        }
    }

    public function historial(Request $request): View
    {
        return view('chat.historial', ['conversaciones' => Conversacion::where('id_usuario', $request->user()->id)->with('mensajes')->orderByDesc('id_conversacion')->paginate(10)]);
    }

    public function detalle(Request $request, string $conversacion): View
    {
        $record = Conversacion::where('id_usuario', $request->user()->id)->with('mensajes')->findOrFail($conversacion);

        return view('chat.detalle', ['conversacion' => $record]);
    }

    public function consumo(Request $request, ResumenConsumo $resumen): View
    {
        return view('chat.consumo', ['consumo' => $resumen->paraUsuario($request->user()->id), 'sinMedicion' => $resumen->llamadasSinMedicion($request->user()->id)]);
    }
}
