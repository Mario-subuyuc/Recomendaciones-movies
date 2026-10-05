<section aria-labelledby="tokens-title" class="mb-4">
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mb-3">
        <h3 id="tokens-title" class="h5 mb-0">Uso de tokens de IA</h3>
        <span class="badge bg-light-secondary">Pendiente de conexión</span>
    </div>
    <div class="row">
        @foreach(['Tokens disponibles', 'Tokens consumidos', 'Límite de tokens'] as $label)
            <div class="col-md-4"><div class="card"><div class="card-body">
                <p class="text-muted mb-2">{{ $label }}</p>
                <p class="h3 mb-1" aria-label="Sin datos">—</p>
                <small class="text-muted">Sin datos todavía</small>
            </div></div></div>
        @endforeach
    </div>
    <div class="row">
        <div class="col-lg-8">
            <div class="card"><div class="card-body">
                <h4 class="h6">Consumo de tokens en el tiempo</h4>
                <div class="token-chart-slot border rounded d-flex align-items-center justify-content-center text-center p-4">
                    <p class="text-muted mb-0">Aquí aparecerá el historial de consumo al conectar la aplicación de IA.</p>
                    <canvas id="token-usage-chart" hidden aria-label="Historial de consumo de tokens" role="img"></canvas>
                </div>
            </div></div>
        </div>
        <div class="col-lg-4">
            <div class="card"><div class="card-body">
                <h4 class="h6">Balance de tokens</h4>
                <div class="token-chart-slot border rounded d-flex align-items-center justify-content-center text-center p-4">
                    <p class="text-muted mb-0">Aquí podrás comparar los tokens consumidos con los restantes.</p>
                    <canvas id="token-balance-chart" hidden aria-label="Tokens consumidos y restantes" role="img"></canvas>
                </div>
            </div></div>
        </div>
    </div>
</section>
@push('styles')
<style>.token-chart-slot { min-height: 280px; }</style>
@endpush
