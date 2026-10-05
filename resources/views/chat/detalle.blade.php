<x-plantilla-panel>
    <x-slot name="header"><h2 class="h3">Detalle de mi consulta</h2><p class="text-muted">{{ $conversacion->fecha_creacion->copy()->timezone(config('app.display_timezone'))->format('d/m/Y H:i') }} (Guatemala) · {{ $conversacion->categoria === 'peliculas' ? 'Películas' : 'Videojuegos' }}</p></x-slot>
    @foreach($conversacion->mensajes as $mensaje)<article class="card card-body"><h3 class="h5">{{ $mensaje->rol === 'usuario' ? 'Tu pregunta' : 'Respuesta del asistente' }}</h3><p style="white-space:pre-wrap;overflow-wrap:anywhere">{{ $mensaje->contenido }}</p></article>@endforeach
    <a class="btn btn-outline-primary" href="{{ route('chat.historial') }}">Volver al historial</a>
</x-plantilla-panel>
