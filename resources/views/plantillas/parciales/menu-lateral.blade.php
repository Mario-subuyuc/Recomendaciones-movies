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
            @if(auth()->user()->can('peliculas.ver') || auth()->user()->can('videojuegos.ver'))
                <li class="sidebar-title">Asistente IA</li>
                @foreach(['chat.index' => 'Chat del catálogo', 'chat.historial' => 'Mi historial', 'chat.consumo' => 'Mi consumo'] as $ruta => $etiqueta)
                    <li class="sidebar-item {{ request()->routeIs($ruta) || ($ruta === 'chat.historial' && request()->routeIs('chat.detalle')) ? 'active' : '' }}"><a class="sidebar-link" href="{{ route($ruta) }}"><span>{{ $etiqueta }}</span></a></li>
                @endforeach
            @endif
                @foreach(collect(config('modulos'))->except('usuarios') as $module => $definition)
                @php($label = $definition['nombre'])
                @can($module.'.ver')
                <li class="sidebar-item {{ request()->routeIs($module.'.*') ? 'active' : '' }}">
                    <a class="sidebar-link" href="{{ route($definition['ruta']) }}"><span>{{ $label }}</span></a>
                </li>
                @endcan
                @endforeach
                @if(auth()->user()->hasRole('Administrador') || auth()->user()->can('usuarios.ver') || auth()->user()->can('usuarios.crear'))
                <li class="sidebar-item">
                    <details class="sidebar-section" @if(request()->routeIs('admin.usuarios.*', 'admin.roles.*')) open @endif>
                        <summary class="sidebar-link"><span>Administración</span></summary>
                        <ul class="dashboard-submenu">
                            @can('usuarios.ver')<li><a class="{{ request()->routeIs('admin.usuarios.index', 'admin.usuarios.edit') ? 'active' : '' }}" href="{{ route('admin.usuarios.index') }}">Usuarios</a></li>@endcan
                            @can('usuarios.crear')<li><a class="{{ request()->routeIs('admin.usuarios.create') ? 'active' : '' }}" href="{{ route('admin.usuarios.create') }}">Crear usuario</a></li>@endcan
                            @role('Administrador')<li><a href="{{ route('admin.roles.index') }}">Roles y permisos</a></li><li><a href="{{ route('admin.permisos.index') }}">Rutas del proyecto</a></li>@endrole
                        </ul>
                    </details>
                </li>
                @endif
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
