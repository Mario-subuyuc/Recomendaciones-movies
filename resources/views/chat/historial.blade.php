<x-plantilla-panel>
    <x-slot name="header"><h2 class="h3">Mi historial</h2><p class="text-muted">Solo aparecen tus consultas completadas. Cada fila corresponde a una pregunta independiente.</p></x-slot>
    <div class="card card-body"><div class="table-responsive"><table class="table"><thead><tr><th>Fecha</th><th>Categoría</th><th>Pregunta</th><th>Respuesta</th></tr></thead><tbody>
        @forelse($conversaciones as $conversacion)<tr>
            <td class="text-nowrap"><a href="{{ route('chat.detalle', $conversacion->getKey()) }}">{{ $conversacion->fecha_creacion->copy()->timezone(config('app.display_timezone'))->format('d/m/Y H:i') }}</a><small class="d-block text-muted">Hora de Guatemala</small></td>
            <td>{{ $conversacion->categoria === 'peliculas' ? 'Películas' : 'Videojuegos' }}</td>
            <td class="history-content">{{ $conversacion->mensajes->firstWhere('rol', 'usuario')?->contenido }}</td>
            <td class="history-content">{{ $conversacion->mensajes->firstWhere('rol', 'asistente')?->contenido }}</td>
        </tr>@empty<tr><td colspan="4" class="text-center text-muted py-5">Todavía no tienes consultas. <a href="{{ route('chat.index') }}">Abrir el asistente</a></td></tr>@endforelse
    </tbody></table></div>{{ $conversaciones->links('pagination::bootstrap-5') }}</div>
    @push('styles')<style>.history-content{white-space:pre-wrap;overflow-wrap:anywhere;min-width:180px;max-width:500px}</style>@endpush
</x-plantilla-panel>
