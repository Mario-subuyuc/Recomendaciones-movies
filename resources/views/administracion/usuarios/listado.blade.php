<x-plantilla-panel>
    <x-slot name="header"><h2 class="h3">Administración de usuarios</h2></x-slot>
        <div class="card card-body">
            <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4"><p>Gestiona las cuentas, sus roles y permisos adicionales.</p><a href="{{ route('admin.usuarios.create') }}" class="btn btn-primary">Crear usuario</a></div>
            <div class="table-responsive"><table class="table table-hover">
                <thead><tr class=""><th class="p-3">Nombre</th><th class="p-3">Correo</th><th class="p-3">Roles</th><th class="p-3">Permisos directos</th><th class="p-3">Acciones</th></tr></thead>
                <tbody>@forelse ($users as $user)
                    <tr class=""><td class="p-3">{{ $user->name }}</td><td class="p-3">{{ $user->email }}</td><td class="p-3">{{ $user->roles->pluck('name')->join(', ') ?: 'Sin rol' }}</td><td class="p-3">{{ $user->permissions->pluck('name')->join(', ') ?: 'Ninguno' }}</td>
                        <td class="p-3"><a class="btn btn-outline-primary" href="{{ route('admin.usuarios.edit', $user) }}">Editar</a>
                            @if (! $user->is(auth()->user()))
                                <form class="d-inline ms-2" method="POST" action="{{ route('admin.usuarios.destroy', $user) }}" onsubmit="return confirm('¿Eliminar este usuario? Esta acción es permanente.')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger" type="submit">Eliminar</button></form>
                            @endif
                        </td>
                    </tr>
                @empty<tr><td colspan="5" class="p-3">No hay usuarios.</td></tr>@endforelse</tbody>
            </table></div>
            <div class="mt-4">{{ $users->links('pagination::bootstrap-5') }}</div>
        </div>
</x-plantilla-panel>
