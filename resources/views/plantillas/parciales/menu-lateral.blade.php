<aside id="sidebar">
    <div class="sidebar-wrapper">
        <div class="sidebar-header d-flex justify-content-between align-items-center">
            <a class="brand" href="{{ route('dashboard') }}">{{ config('app.name') }}</a>
            <button class="btn btn-sm btn-outline-secondary d-xl-none" type="button" aria-label="Cerrar menú" data-sidebar-close>✕</button>
        </div>
        <div class="sidebar-menu">
            <ul class="menu">
                <li class="sidebar-title">Principal</li>
                <li class="sidebar-item {{ request()->routeIs('dashboard') ? 'active' : '' }}"><a class="sidebar-link" href="{{ route('dashboard') }}"><span>Dashboard</span></a></li>
                @foreach(['peliculas' => 'Películas', 'videojuegos' => 'Videojuegos'] as $module => $label)
                @can($module.'.ver')
                <li class="sidebar-item {{ request()->routeIs($module.'.*') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route($module.'.index') }}"><span>{{ $label }}</span></a>
                </li>
                @endcan
                @endforeach
                @role('Administrador')
                <li class="sidebar-item">
                    <details class="sidebar-section" @if(request()->routeIs('admin.usuarios.*')) open @endif>
                        <summary class="sidebar-link"><span>Administración</span></summary>
                        <ul class="dashboard-submenu">
                            <li><a class="{{ request()->routeIs('admin.usuarios.index', 'admin.usuarios.edit') ? 'active' : '' }}" href="{{ route('admin.usuarios.index') }}">Usuarios</a></li>
                            <li><a class="{{ request()->routeIs('admin.usuarios.create') ? 'active' : '' }}" href="{{ route('admin.usuarios.create') }}">Crear usuario</a></li>
                        </ul>
                    </details>
                </li>
                @endrole
                <li class="sidebar-item">
                    <a class="sidebar-link {{ request()->routeIs('profile.*') ? 'active' : '' }}"
                        href="{{ route('profile.edit') }}">
                        <span>Mi cuenta</span>
                    </a>
                </li>
            </ul>
            <div class="mt-4">
                <form method="POST" action="{{ route('logout') }}" class="mt-3">@csrf<button class="btn btn-outline-danger btn-sm w-100" type="submit">Cerrar sesión</button></form>
            </div>
        </div>
    </div>
</aside>