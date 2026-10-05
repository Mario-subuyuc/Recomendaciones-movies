<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name') }} · Dashboard</title>

    {{-- =========================================================
         TEMA
    ========================================================== --}}
    <script>
        (() => {
            let theme = null;

            try {
                theme = localStorage.getItem('dashboard-theme');
            } catch (e) {}

            if (!theme) {
                theme = window.matchMedia('(prefers-color-scheme: dark)').matches ?
                    'dark' :
                    'light';
            }

            document.documentElement.setAttribute('data-bs-theme', theme);
        })();
    </script>

    {{-- =========================================================
         MAZER
    ========================================================== --}}
    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/compiled/css/app.css">

    <link rel="stylesheet"
        href="https://cdn.jsdelivr.net/gh/zuramai/mazer@docs/demo/assets/compiled/css/app-dark.css">

    {{-- =========================================================
         ESTILOS DEL DASHBOARD
    ========================================================== --}}
    <style>
        /* =====================================================
           VARIABLES DEL TEMA
        ====================================================== */

        :root {
            --dashboard-surface: #ffffff;
            --dashboard-surface-hover: #f8f9fa;
            --dashboard-border: #dee2e6;
            --dashboard-text: #212529;
            --dashboard-muted: #6c757d;
            --dashboard-shadow: rgba(0, 0, 0, .04);
        }

        [data-bs-theme="dark"] {
            --dashboard-surface: #1e1e2d;
            --dashboard-surface-hover: #252538;
            --dashboard-border: #343445;
            --dashboard-text: #f8f9fa;
            --dashboard-muted: #adb5bd;
            --dashboard-shadow: rgba(0, 0, 0, .20);
        }


        /* =====================================================
           GENERAL
        ====================================================== */

        .brand {
            font-size: 1.5rem;
            font-weight: 800;
        }

        .table td {
            vertical-align: middle;
        }

        .sidebar-backdrop {
            display: none;
        }

        #main {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }

        #main>.page-content {
            flex: 1;
        }


        /* =====================================================
           TOPBAR
        ====================================================== */

        .dashboard-topbar {
            display: flex;
            align-items: stretch;
            justify-content: space-between;
            gap: 1rem;
            background: transparent;
            padding: 0;
            margin-bottom: 1.5rem;
        }


        /* =====================================================
           CAJAS DEL TOPBAR
        ====================================================== */

        .dashboard-topbar-box {
            background-color: var(--dashboard-surface) !important;
            border: 1px solid var(--dashboard-border);
            border-radius: .75rem;
            padding: .85rem 1.25rem;
            box-shadow: 0 .125rem .25rem var(--dashboard-shadow);
            color: var(--dashboard-text);
        }


        /* =====================================================
           INFORMACIÓN DEL USUARIO
        ====================================================== */

        .dashboard-user-box {
            display: flex;
            align-items: center;
            gap: .85rem;
            min-width: 0;
            flex: 1;
        }

        .dashboard-user-avatar {
            width: 42px;
            height: 42px;
            min-width: 42px;
            border-radius: 50%;

            background-color: var(--bs-primary);
            color: #fff;

            display: flex;
            align-items: center;
            justify-content: center;

            font-weight: 700;
            font-size: 1rem;
        }

        .dashboard-user {
            min-width: 0;
            overflow-wrap: anywhere;
        }

        .dashboard-user-name {
            font-weight: 600;
            line-height: 1.2;
            color: var(--dashboard-text);
        }

        .dashboard-user-info {
            font-size: .8rem;
            color: var(--dashboard-muted);
            margin-top: .2rem;
        }

        .dashboard-user-heading { display: flex; flex-wrap: wrap; align-items: center; gap: .5rem .75rem; }
        .dashboard-user-name { font-size: 1rem; text-decoration: none; }
        .dashboard-user-name:hover { text-decoration: underline; }
        .dashboard-user-name:focus-visible { outline: 2px solid var(--bs-primary); outline-offset: 3px; border-radius: .2rem; }
        .dashboard-user-roles { display: flex; flex-wrap: wrap; gap: .35rem; }
        .dashboard-role { display: inline-flex; align-items: center; padding: .2rem .6rem; border: 1px solid var(--dashboard-border); border-radius: 999px; background: var(--dashboard-surface-hover); color: var(--dashboard-text); font-size: .72rem; font-weight: 500; line-height: 1.4; }
        .dashboard-user-info { line-height: 1.5; overflow-wrap: anywhere; }


        /* =====================================================
           ACCIONES DEL TOPBAR
        ====================================================== */

        .dashboard-actions-box {
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .dashboard-action {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .5rem;

            min-height: 40px;

            padding: .5rem .85rem;

            border-radius: .5rem;
            border: 1px solid var(--dashboard-border);

            background-color: var(--dashboard-surface);
            color: var(--dashboard-text);

            text-decoration: none;

            transition:
                background-color .2s ease,
                border-color .2s ease,
                color .2s ease;
        }

        .dashboard-action:hover {
            background-color: var(--dashboard-surface-hover);
            color: var(--bs-primary);
            border-color: var(--dashboard-border);
        }

        .dashboard-action.logout:hover {
            color: var(--bs-danger);
        }


        /* =====================================================
           SIDEBAR
        ====================================================== */

        #sidebar .sidebar-wrapper {
            background-color: var(--dashboard-surface);
        }

        .sidebar-section summary {
            cursor: pointer;
            list-style: none;
        }

        .sidebar-section summary::-webkit-details-marker {
            display: none;
        }

        .sidebar-section summary::after {
            content: '›';
            margin-left: auto;
            transition: transform .2s ease;
        }

        .sidebar-section[open] summary::after {
            transform: rotate(90deg);
        }

        .dashboard-submenu {
            list-style: none;
            margin: .5rem 0;
            padding-left: 1rem;
        }

        .dashboard-submenu a {
            display: block;

            padding: .65rem 1rem;

            color: var(--bs-body-color);

            border-radius: .5rem;

            text-decoration: none;
        }

        .dashboard-submenu a:hover,
        .dashboard-submenu a.active {
            background: var(--bs-primary);
            color: white;
        }


        /* =====================================================
           FOOTER
        ====================================================== */

        .dashboard-footer {
            background-color: var(--dashboard-surface);
            color: var(--dashboard-text);

            padding-left: 1.5rem;
            padding-right: 1.5rem;

            border-radius: .75rem;
        }


        /* =====================================================
           RESPONSIVE
        ====================================================== */

        @media (max-width: 768px) {

            .dashboard-topbar {
                flex-direction: column;
            }

            .dashboard-user-box,
            .dashboard-actions-box {
                width: 100%;
            }

            .dashboard-actions-box {
                justify-content: stretch;
            }

            .dashboard-action {
                flex: 1;
            }
        }


        /* =====================================================
           SIDEBAR RESPONSIVE
        ====================================================== */

        @media (max-width: 1199px) {

            #sidebar.active+.sidebar-backdrop {
                display: block;

                position: fixed;
                inset: 0;

                background: rgba(0, 0, 0, .53);

                z-index: 9;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <div id="app">
        @include('plantillas.parciales.menu-lateral')
        <button class="sidebar-backdrop border-0" type="button" aria-label="Cerrar menú" data-sidebar-close></button>
        <div id="main">
            <header class="dashboard-topbar mb-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
                <button class="btn btn-outline-primary d-xl-none" type="button" data-sidebar-open aria-controls="sidebar" aria-expanded="false">☰ Menú</button>
                @include('plantillas.parciales.usuario-barra-superior')
                <div class="d-flex flex-wrap align-items-center gap-2">
                    <button id="theme-toggle" class="btn btn-outline-secondary" type="button">Cambiar tema</button>
                    <form method="POST" action="{{ route('logout') }}">@csrf<button class="btn btn-outline-danger" type="submit">Cerrar sesión</button></form>
                </div>
            </header>
            @isset($header)<div class="page-heading">{{ $header }}</div>@endisset
            <main class="page-content">
                @if (session('status'))<div class="alert alert-success" role="status">{{ ['profile-updated' => 'Datos actualizados correctamente.', 'password-updated' => 'Contraseña actualizada correctamente.', 'verification-link-sent' => 'Enlace de verificación enviado.'][session('status')] ?? session('status') }}</div>@endif
                @if ($errors->any())<div class="alert alert-danger" role="alert">
                    <ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                </div>@endif
                {{ $slot }}
            </main>
            @include('plantillas.parciales.pie-pagina')
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
            try {
                localStorage.setItem('dashboard-theme', theme);
            } catch (e) {}
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

        function toggleSidebar(open) {
            sidebar.classList.toggle('active', open);
            opener.setAttribute('aria-expanded', String(open));
        }
        opener.addEventListener('click', () => toggleSidebar(true));
        document.querySelectorAll('[data-sidebar-close]').forEach(button => button.addEventListener('click', () => toggleSidebar(false)));
        document.addEventListener('keydown', event => {
            if (event.key === 'Escape') toggleSidebar(false);
        });
    </script>
    @stack('scripts')
</body>

</html>
