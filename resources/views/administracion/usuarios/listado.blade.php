<x-plantilla-panel>
    <x-slot name="header"><h2 class="h3">Administración de usuarios</h2></x-slot>
        <div class="card card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"><p>Consulta las cuentas y su rol asignado.</p>@can('usuarios.crear')<a href="{{ route('admin.usuarios.create') }}" class="btn btn-primary">Crear usuario</a>@endcan</div>
            <div class="table-responsive"><table class="table table-hover">
                <thead><tr class=""><th class="p-3">Nombre</th><th class="p-3">Correo</th><th class="p-3">Roles</th><th class="p-3">Acciones</th></tr></thead>
                <tbody>@forelse ($users as $user)
                    <tr class=""><td class="p-3">{{ $user->name }}</td><td class="p-3">{{ $user->email }}</td><td class="p-3">{{ $user->roles->pluck('name')->join(', ') ?: 'Sin rol' }}</td>
                        <td class="p-3">@if(auth()->user()->puedeGestionarUsuario($user)) @can('usuarios.editar')<a class="btn btn-outline-primary" href="{{ route('admin.usuarios.edit', $user) }}">Editar</a>@endcan
                            @if (! $user->is(auth()->user()) && auth()->user()->can('usuarios.eliminar'))
                                <form class="d-inline ms-2" method="POST" action="{{ route('admin.usuarios.destroy', $user) }}" onsubmit="return confirm('¿Eliminar este usuario? Esta acción es permanente.')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button></form>
                            @endif
                        @endif</td>
                    </tr>
                @empty<tr><td colspan="4" class="p-3">No hay usuarios.</td></tr>@endforelse</tbody>
            </table></div>
            <div class="mt-4">{{ $users->links('pagination::bootstrap-5') }}</div>
        </div>
</x-plantilla-panel>
