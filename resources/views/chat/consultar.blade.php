<x-plantilla-panel>
    <x-slot name="header"><h2 class="h3">Asistente del catálogo</h2><p class="text-muted">Pregunta por películas o videojuegos. Cada consulta es independiente.</p></x-slot>
    <section class="card card-body chat-shell">
        <form id="chat-form" action="{{ route('chat.preguntar') }}" method="POST">
            @csrf
            <div class="d-flex flex-wrap justify-content-between gap-3 mb-3">
                <div><label for="chat-category" class="form-label">Categoría</label><select id="chat-category" name="categoria" class="form-select">@foreach($categorias as $valor => $etiqueta)<option value="{{ $valor }}">{{ $etiqueta }}</option>@endforeach</select></div>
                <a class="align-self-center" href="{{ route('chat.historial') }}">Ver mi historial</a>
            </div>
            <div id="chat-messages" class="chat-messages" role="log" aria-label="Preguntas y respuestas" aria-live="polite">
                <div id="chat-empty" class="text-center text-muted py-5"><h3 class="h5">¿Qué quieres encontrar?</h3><p>Prueba: «¿Cuáles son 3 películas de drama?»<br>O selecciona Videojuegos y pregunta por juegos de aventura.</p><p class="small">Solo se consultan los datos guardados en el catálogo.</p></div>
            </div>
            <div id="chat-error" class="alert alert-danger mt-3" role="alert" hidden></div>
            <p id="chat-progress" class="text-muted small mt-3" role="status" hidden>Consultando el catálogo y preparando la respuesta…</p>
            <label for="chat-question" class="form-label mt-3">Tu pregunta</label>
            <textarea id="chat-question" name="pregunta" class="form-control" rows="3" maxlength="1000" required placeholder="¿Qué juegos de disparos tienen una calificación mayor a 8.5?"></textarea>
            <div class="d-flex justify-content-between align-items-center gap-3 mt-3"><span class="text-muted small">Hasta 10 resultados por consulta.</span><button id="chat-submit" class="btn btn-primary" type="submit">Enviar pregunta</button></div>
        </form>
    </section>
    @push('styles')<style>.chat-shell{max-width:960px;margin-inline:auto}.chat-messages{min-height:280px;max-height:55vh;overflow:auto;padding:1rem;background:var(--dashboard-surface-hover);border-radius:.75rem}.chat-message{max-width:90%;padding:1rem;border-radius:.75rem;margin-bottom:1rem;white-space:pre-wrap;overflow-wrap:anywhere}.chat-message-user{margin-left:auto;border:1px solid var(--dashboard-border);background:var(--dashboard-surface)}.chat-message-assistant{margin-right:auto;border-left:3px solid var(--bs-primary)}.chat-message strong{display:block;margin-bottom:.5rem}.chat-message p{margin:0}</style>@endpush
    @push('scripts') @vite('resources/js/chat.js') @endpush
</x-plantilla-panel>
