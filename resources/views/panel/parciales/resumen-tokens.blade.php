<section aria-labelledby="tokens-title" class="mb-4">
    <h3 id="tokens-title" class="h5 mb-3">Uso de tokens académicos</h3>
    <p class="text-muted small">Una palabra procesada equivale a un token académico. Incluye instrucciones, pregunta, contexto y salida de cada llamada verificable.</p>
    <div class="row">
        @foreach(['peliculas' => 'Películas', 'videojuegos' => 'Videojuegos'] as $categoria => $etiqueta)
            <div class="col-md-4"><div class="card"><div class="card-body"><p class="text-muted mb-2">{{ $etiqueta }}</p><p class="h3 mb-0">{{ number_format($consumo[$categoria]) }}</p></div></div></div>
        @endforeach
        <div class="col-md-4"><div class="card"><div class="card-body"><p class="text-muted mb-2">Total consumido</p><p class="h3 mb-0">{{ number_format(array_sum($consumo)) }}</p></div></div></div>
    </div>
    <div class="card card-body">
        <h4 class="h6">Consumo por categoría</h4>
        @if(array_sum($consumo) === 0)<p class="text-muted text-center py-4">Todavía no tienes consumo registrado. Realiza una consulta en el asistente.</p>@endif
        <div style="height:280px"><canvas id="token-consumption-chart" data-peliculas="{{ $consumo['peliculas'] }}" data-videojuegos="{{ $consumo['videojuegos'] }}" role="img" aria-label="Consumo: películas {{ $consumo['peliculas'] }}, videojuegos {{ $consumo['videojuegos'] }}"></canvas></div>
        <p class="small text-muted mb-0 mt-3">Puede incluir llamadas de consultas que no terminaron. Aún no se ha definido un cupo de tokens; no hay un saldo disponible calculado.</p>
    </div>
</section>
@push('scripts') @vite('resources/js/consumo.js') @endpush