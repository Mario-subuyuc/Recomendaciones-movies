<x-plantilla-panel>
    <x-slot name="header">
        <h2 class="h3">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    @include('panel.parciales.resumen-tokens')

    {{-- Bienvenida y cuenta --}}
    <div class="row g-4 mb-4">
        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <h3 class="h4">
                        Bienvenido, {{ auth()->user()->name }}
                    </h3>

                    <p class="text-muted">
                        Tu sesión está activa. Accede a tus módulos desde el menú lateral.
                    </p>

                    <span class="badge bg-light-primary">
                        {{ auth()->user()->roles->pluck('name')->join(', ') ?: 'Sin rol asignado' }}
                    </span>

                    @role('Administrador')
                        <div class="mt-4">
                            <h3 class="h5">Usuarios</h3>

                            <p class="mt-2">
                                Crea y administra usuarios, roles y permisos adicionales.
                            </p>

                            <a class="btn btn-primary mt-3"
                               href="{{ route('admin.usuarios.index') }}">
                                Administrar usuarios
                            </a>
                        </div>
                    @endrole
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card h-100">
                <div class="card-body">
                    <h3 class="h5">Mi cuenta</h3>

                    <p class="text-muted">
                        Actualiza tus datos y tu contraseña.
                    </p>

                    <a class="btn btn-outline-primary"
                       href="{{ route('profile.edit') }}">
                        Ver perfil
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Módulos --}}
    <div class="row g-4">
        @foreach(['peliculas' => 'Películas', 'videojuegos' => 'Videojuegos'] as $module => $label)

            @can($module.'.ver')
                <div class="col-lg-6">
                    <div class="card h-100">
                        <div class="card-body">
                            <h3 class="h5">
                                {{ $label }}
                            </h3>

                            <p class="text-muted">
                                Consulta el catálogo y sus detalles.
                            </p>

                            <a class="btn btn-primary"
                               href="{{ route($module.'.index') }}">
                                Ver {{ mb_strtolower($label) }}
                            </a>
                        </div>
                    </div>
                </div>
            @endcan

        @endforeach
    </div>

</x-plantilla-panel>