<x-app-layout>
    <x-slot name="header"><h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200">{{ $user->exists ? 'Editar usuario' : 'Crear usuario' }}</h2></x-slot>
    <div class="py-12"><div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
        <form method="POST" action="{{ $user->exists ? route('admin.usuarios.update', $user) : route('admin.usuarios.store') }}" class="bg-white dark:bg-gray-800 p-6 shadow-sm sm:rounded-lg space-y-6 text-gray-900 dark:text-gray-100">
            @csrf
            @if ($user->exists) @method('PUT') @endif
            @if ($errors->any())<div role="alert" class="text-red-600"><ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
            <div><x-input-label for="name" value="Nombre"/><x-text-input id="name" name="name" class="block mt-1 w-full" :value="old('name', $user->name)" required maxlength="255" /></div>
            <div><x-input-label for="email" value="Correo electrónico"/><x-text-input id="email" type="email" name="email" class="block mt-1 w-full" :value="old('email', $user->email)" required maxlength="255" /></div>
            <div><x-input-label for="password" :value="$user->exists ? 'Nueva contraseña (dejar vacía para conservar la actual)' : 'Contraseña'"/><x-text-input id="password" type="password" name="password" class="block mt-1 w-full" autocomplete="new-password" :required="! $user->exists" /></div>
            <div><x-input-label for="password_confirmation" value="Confirmar contraseña"/><x-text-input id="password_confirmation" type="password" name="password_confirmation" class="block mt-1 w-full" autocomplete="new-password" :required="! $user->exists" /></div>
            <fieldset><legend class="font-semibold mb-2">Roles</legend><p class="text-sm mb-3">Selecciona al menos un rol. Sus permisos se heredan automáticamente.</p>
                @foreach ($roles as $role)
                    <label class="block mb-3"><input type="checkbox" name="roles[]" value="{{ $role->name }}" @checked(in_array($role->name, old('roles', $user->roles->pluck('name')->all())))> {{ $role->name }}
                        <span class="block text-sm text-gray-500">{{ $role->permissions->pluck('name')->join(', ') ?: 'Sin permisos' }}</span>
                    </label>
                @endforeach
            </fieldset>
            <fieldset><legend class="font-semibold mb-2">Permisos adicionales del usuario</legend><p class="text-sm mb-3">Se suman a los permisos de sus roles. Desmarcarlos no quita permisos heredados. El módulo de usuarios sigue siendo exclusivo del Administrador.</p>
                @foreach ($permissionGroups as $module => $permissions)
                    <div class="mb-4"><h3 class="font-semibold capitalize mb-2">{{ $module }}</h3>
                        @foreach ($permissions as $permission)<label class="block"><input type="checkbox" name="permissions[]" value="{{ $permission->name }}" @checked(in_array($permission->name, old('permissions', $errors->any() ? [] : $user->permissions->pluck('name')->all())))> {{ $permission->name }}</label>@endforeach
                    </div>
                @endforeach
            </fieldset>
            <div class="flex items-center gap-4"><x-primary-button>Guardar usuario</x-primary-button><a class="underline" href="{{ route('admin.usuarios.index') }}">Cancelar</a></div>
        </form>
    </div></div>
</x-app-layout>
