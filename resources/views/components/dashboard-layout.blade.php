<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} · Dashboard</title>
    <script>
        (() => {
            let theme;
            try { theme = localStorage.getItem('dashboard-theme'); } catch (e) {}
            document.documentElement.setAttribute('data-bs-theme', theme || (matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'));
        })();
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/compiled/css/app.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/compiled/css/app-dark.css">
    <style>
        .brand { font-size: 1.5rem; font-weight: 800; }
        .dashboard-topbar { background-color: var(--dashboard-surface); padding: 1rem 1.5rem; border-radius: .75rem; }
        .dashboard-user { min-width: 0; overflow-wrap: anywhere; }
        .table td { vertical-align: middle; }
        .sidebar-backdrop { display: none; }
        #main { min-height: 100vh; display: flex; flex-direction: column; }
        #main > .page-content { flex: 1; }
        :root { --dashboard-surface: #fff; }
        [data-bs-theme="dark"] { --dashboard-surface: #1e1e2d; }
        #sidebar .sidebar-wrapper, .dashboard-footer { background-color: var(--dashboard-surface); }
        .dashboard-footer { padding-left: 1.5rem; padding-right: 1.5rem; border-radius: .75rem; }
        .sidebar-section summary { cursor: pointer; list-style: none; }
        .sidebar-section summary::-webkit-details-marker { display: none; }
        .sidebar-section summary::after { content: '›'; margin-left: auto; transition: transform .2s; }
        .sidebar-section[open] summary::after { transform: rotate(90deg); }
        .dashboard-submenu { list-style: none; margin: .5rem 0; padding-left: 1rem; }
        .dashboard-submenu a { display: block; padding: .65rem 1rem; color: var(--bs-body-color); border-radius: .5rem; }
        .dashboard-submenu a:hover, .dashboard-submenu a.active { background: var(--bs-primary); color: white; }
        @media (max-width:1199px) { #sidebar.active + .sidebar-backdrop { display:block; position:fixed; inset:0; background:#0008; z-index:9; } }
    </style>
    @stack('styles')
</head>
<body>
<div id="app">
    @include('layouts.partials.dashboard-sidebar')
    <button class="sidebar-backdrop border-0" type="button" aria-label="Cerrar menú" data-sidebar-close></button>
    <div id="main">
        <header class="dashboard-topbar mb-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
            <button class="btn btn-outline-primary d-xl-none" type="button" data-sidebar-open aria-controls="sidebar" aria-expanded="false">☰ Menú</button>
            @include('layouts.partials.dashboard-topbar-user')
            <div class="d-flex flex-wrap align-items-center gap-2">
                <button id="theme-toggle" class="btn btn-outline-secondary" type="button">Cambiar tema</button>
                <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-outline-danger" type="submit">Cerrar sesión</button></form>
            </div>
        </header>
        @isset($header)<div class="page-heading">{{ $header }}</div>@endisset
        <main class="page-content">
            @if (session('status'))<div class="alert alert-success" role="status">{{ ['profile-updated' => 'Datos actualizados correctamente.', 'password-updated' => 'Contraseña actualizada correctamente.', 'verification-link-sent' => 'Enlace de verificación enviado.'][session('status')] ?? session('status') }}</div>@endif
            @if ($errors->any())<div class="alert alert-danger" role="alert"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            {{ $slot }}
        </main>
        @include('layouts.partials.dashboard-footer')
    </div>
</div>
<script>
    const themeButton = document.getElementById('theme-toggle');
    function themeLabel() {
        const dark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
        themeButton.textContent = dark ? '☀ Modo claro' : '☾ Modo oscuro';
        themeButton.setAttribute('aria-pressed', String(dark));
    }
    themeLabel();
    themeButton.addEventListener('click', () => {
        const theme = document.documentElement.getAttribute('data-bs-theme') === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-bs-theme', theme);
        try { localStorage.setItem('dashboard-theme', theme); } catch (e) {}
        themeLabel();
    });
    const sidebar = document.getElementById('sidebar');
    document.querySelectorAll('.sidebar-section').forEach(section => {
        section.addEventListener('toggle', () => {
            if (section.open) document.querySelectorAll('.sidebar-section').forEach(other => {
                if (other !== section) other.open = false;
            });
        });
    });
    const opener = document.querySelector('[data-sidebar-open]');
    function toggleSidebar(open) { sidebar.classList.toggle('active', open); opener.setAttribute('aria-expanded', String(open)); }
    opener.addEventListener('click', () => toggleSidebar(true));
    document.querySelectorAll('[data-sidebar-close]').forEach(button => button.addEventListener('click', () => toggleSidebar(false)));
    document.addEventListener('keydown', event => { if (event.key === 'Escape') toggleSidebar(false); });
</script>
@stack('scripts')
</body>
</html>
