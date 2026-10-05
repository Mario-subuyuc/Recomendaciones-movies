<footer class="dashboard-footer mt-5 pt-3 pb-2 border-top">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-2">
        <div>
            <span class="fw-semibold">ProyectIA</span>
            <span class="text-muted small ms-2">Panel de gestión</span>
            <p class="text-muted small mb-0 mt-1">&copy; {{ now()->year }} ProyectIA. Todos los derechos reservados.</p>
        </div>
        <nav class="d-flex gap-3 small" aria-label="Enlaces del pie de página">
            <a href="{{ route('dashboard') }}">Inicio</a>
            <a href="{{ route('profile.edit') }}">Mi perfil</a>
        </nav>
    </div>
</footer>
