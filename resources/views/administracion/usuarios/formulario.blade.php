<x-plantilla-panel>
    <x-slot name="header"><h2 class="h3">{{ $user->exists ? 'Editar usuario' : 'Crear usuario' }}</h2></x-slot>
        <form method="POST" action="{{ $user->exists ? route('admin.usuarios.update', $user) : route('admin.usuarios.store') }}" class="card card-body">
            @csrf
            @if ($user->exists) @method('PUT') @endif
            <x-campo-panel name="name" label="Nombre" :value="$user->name" required maxlength="255" />
            <x-campo-panel name="email" label="Correo electrónico" type="email" :value="$user->email" required maxlength="255" />
            <x-campo-panel name="password" :label="$user->exists ? 'Nueva contraseña (dejar vacía para conservar la actual)' : 'Contraseña'" type="password" autocomplete="new-password" :required="! $user->exists" />
            <x-campo-panel name="password_confirmation" label="Confirmar contraseña" type="password" autocomplete="new-password" :required="! $user->exists" />
            <fieldset class="border rounded p-3 mb-4"><legend class="h5 mb-2">Roles</legend><p class="small text-muted mb-3">Selecciona al menos un rol. Sus permisos se heredan automáticamente.</p>
                @foreach ($roles as $role)
                    <label class="d-block mb-3"><input class="form-check-input me-2" type="checkbox" name="roles[]" value="{{ $role->name }}" @checked(in_array($role->name, old('roles', $errors->any() ? [] : $user->roles->pluck('name')->all())))> {{ $role->name }}
                        <span class="d-block small text-muted">{{ $role->permissions->pluck('name')->join(', ') ?: 'Sin permisos' }}</span>
                    </label>
                @endforeach
            </fieldset>

            <fieldset class="border rounded p-3 mb-4"><legend class="h5 mb-2">Permisos adicionales del usuario</legend><p class="small text-muted mb-3">Se suman a los permisos de sus roles. Desmarcarlos no quita permisos heredados. El módulo de usuarios sigue siendo exclusivo del Administrador.</p>
                @foreach ($permissionGroups as $module => $permissions)
                    <div class="mb-4"><h3 class="h6 text-capitalize mb-2">{{ $module }}</h3>
                        @foreach ($permissions as $permission)<label class="d-block mb-2"><input class="form-check-input me-2" type="checkbox" name="permissions[]" value="{{ $permission->name }}" @checked(in_array($permission->name, old('permissions', $errors->any() ? [] : $user->permissions->pluck('name')->all())))> {{ $permission->name }}</label>@endforeach
                    </div>
                @endforeach
            </fieldset>
            <div class="d-flex gap-3 mt-4"><button class="btn btn-primary" type="submit">Guardar usuario</button><a class="btn btn-outline-primary" href="{{ route('admin.usuarios.index') }}">Cancelar</a></div>
        </form>
</x-plantilla-panel>
