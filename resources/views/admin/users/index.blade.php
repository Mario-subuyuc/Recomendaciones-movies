<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">Administración de usuarios</h2></x-slot>
    <div class="py-12"><div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white dark:bg-gray-800 p-6 shadow-sm sm:rounded-lg text-gray-900 dark:text-gray-100">
            @if (session('status'))<p role="status" class="mb-4 text-green-600">{{ session('status') }}</p>@endif
            @foreach ($errors->all() as $error)<p role="alert" class="mb-4 text-red-600">{{ $error }}</p>@endforeach
            <div class="flex justify-between items-center mb-6"><p>Gestiona las cuentas, sus roles y permisos adicionales.</p><a href="{{ route('admin.usuarios.create') }}" class="underline font-semibold">Crear usuario</a></div>
            <div class="overflow-x-auto"><table class="w-full text-left">
                <thead><tr class="border-b"><th class="p-3">Nombre</th><th class="p-3">Correo</th><th class="p-3">Roles</th><th class="p-3">Permisos directos</th><th class="p-3">Acciones</th></tr></thead>
                <tbody>@forelse ($users as $user)
                    <tr class="border-b"><td class="p-3">{{ $user->name }}</td><td class="p-3">{{ $user->email }}</td><td class="p-3">{{ $user->roles->pluck('name')->join(', ') ?: 'Sin rol' }}</td><td class="p-3">{{ $user->permissions->pluck('name')->join(', ') ?: 'Ninguno' }}</td>
                        <td class="p-3"><a class="underline" href="{{ route('admin.usuarios.edit', $user) }}">Editar</a>
                            @if (! $user->is(auth()->user()))
                                <form class="inline-block ml-3" method="POST" action="{{ route('admin.usuarios.destroy', $user) }}" onsubmit="return confirm('¿Eliminar este usuario? Esta acción es permanente.')">@csrf @method('DELETE')<button class="text-red-600 underline" type="submit">Eliminar</button></form>
                            @endif
                        </td>
                    </tr>
                @empty<tr><td colspan="5" class="p-3">No hay usuarios.</td></tr>@endforelse</tbody>
            </table></div>
            <div class="mt-6">{{ $users->links() }}</div>
        </div>
    </div></div>
</x-app-layout>
