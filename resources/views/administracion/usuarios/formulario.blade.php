<x-plantilla-panel>
    <x-slot name="header"><h2 class="h3">{{ $user->exists ? 'Editar usuario' : 'Crear usuario' }}</h2></x-slot>
        <form method="POST" action="{{ $user->exists ? route('admin.usuarios.update', $user) : route('admin.usuarios.store') }}" class="card card-body">
            @csrf
            @if ($user->exists) @method('PUT') @endif
            <x-campo-panel name="name" label="Nombre" :value="$user->name" required maxlength="255" />
            <x-campo-panel name="email" label="Correo electrónico" type="email" :value="$user->email" required maxlength="255" />
            <x-campo-panel name="password" :label="$user->exists ? 'Nueva contraseña (dejar vacía para conservar la actual)' : 'Contraseña'" type="password" autocomplete="new-password" :required="! $user->exists" />
            <x-campo-panel name="password_confirmation" label="Confirmar contraseña" type="password" autocomplete="new-password" :required="! $user->exists" />
            @role('Administrador')
            <div class="mb-4"><label class="form-label" for="rol">Rol del usuario</label>
                <select id="rol" name="roles[]" class="form-select" required><option value="">Selecciona un rol</option>
                    @foreach($roles as $role)<option value="{{ $role->name }}" @selected($role->name === (old('roles', $user->roles->pluck('name')->all())[0] ?? ''))>{{ $role->name }}</option>@endforeach
                </select><p class="small text-muted mt-2">Un único rol define todos los accesos. Configura los roles personalizados desde Roles.</p>
            </div>
            @else
                <p class="text-muted">{{ $user->exists ? 'El rol actual se conserva. Solo el Administrador puede cambiarlo.' : 'La nueva cuenta se creará con el rol Cliente.' }}</p>
            @endrole
            <div class="d-flex gap-3 mt-4"><button class="btn btn-primary" type="submit">Guardar usuario</button><a class="btn btn-outline-primary" href="{{ route(auth()->user()->can('usuarios.ver') ? 'admin.usuarios.index' : 'dashboard') }}">Cancelar</a></div>
        </form>
</x-plantilla-panel>
