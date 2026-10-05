<section aria-labelledby="tokens-title" class="mb-4">
    <h3 id="tokens-title" class="h5 mb-3">Uso real de tokens de Groq</h3>
    <p class="text-muted small">Consumo de entrada y salida reportado por Groq para interpretar tu pregunta y generar la respuesta. Los conteos académicos anteriores se conservan, pero no se incluyen en estos totales. Las llamadas sin consumo reportado no se cuentan como cero: su consumo es desconocido.</p>
    <div class="row">
        @if($sinMedicion > 0)<p class="text-warning small">{{ $sinMedicion }} llamadas guardadas no tienen medición real, incluidos registros anteriores. El total mostrado incluye únicamente consumo confirmado.</p>@endif
        @foreach(['peliculas' => 'Películas', 'videojuegos' => 'Videojuegos'] as $categoria => $etiqueta)
            <div class="col-md-4"><div class="card"><div class="card-body"><p class="text-muted mb-2">{{ $etiqueta }}</p><p class="h3 mb-0">{{ number_format($consumo[$categoria]) }}</p></div></div></div>
        @endforeach
        <div class="col-md-4"><div class="card"><div class="card-body"><p class="text-muted mb-2">Total consumido</p><p class="h3 mb-0">{{ number_format(array_sum($consumo)) }}</p></div></div></div>
    </div>
    <div class="card card-body">
        <h4 class="h6">Consumo por categoría</h4>
        @if(array_sum($consumo) === 0)<p class="text-muted text-center py-4">Todavía no hay tokens reales reportados. Realiza una nueva consulta en el asistente.</p>@endif
        <div style="height:280px"><canvas id="token-consumption-chart" data-peliculas="{{ $consumo['peliculas'] }}" data-videojuegos="{{ $consumo['videojuegos'] }}" role="img" aria-label="Consumo: películas {{ $consumo['peliculas'] }}, videojuegos {{ $consumo['videojuegos'] }}"></canvas></div>
        <p class="small text-muted mb-0 mt-3">Puede incluir llamadas de consultas que no terminaron. Aún no se ha definido un cupo de tokens; no hay un saldo disponible calculado.</p>
    </div>
</section>
@push('scripts') @vite('resources/js/consumo.js') @endpush
