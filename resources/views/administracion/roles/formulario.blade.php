<x-plantilla-panel>
<x-slot name="header"><h2 class="h3">{{ $role->exists ? 'Editar permisos del rol' : 'Crear rol personalizado' }}</h2></x-slot>
<form class="card card-body" method="POST" action="{{ $role->exists ? route('admin.roles.update', $role) : route('admin.roles.store') }}">@csrf @if($role->exists) @method('PUT') @endif
<x-campo-panel name="name" label="Nombre del rol" :value="$role->name" :readonly="$role->exists && array_key_exists($role->name, config('roles.predefinidos'))" required maxlength="100" />
<p class="text-muted">Marca únicamente las acciones que podrán realizar sus usuarios. Gestionar roles y asignarlos sigue siendo exclusivo del Administrador.</p>
<div class="row">@foreach($groups as $module => $permissions)<fieldset class="col-md-4 mb-4"><legend class="h5 text-capitalize">{{ $module }}</legend>
@foreach($permissions as $permission)<label class="d-block mb-2"><input class="form-check-input me-2" type="checkbox" name="permissions[]" value="{{ $permission->name }}" @checked(in_array($permission->name, old('permissions', $errors->any() ? [] : $role->permissions->pluck('name')->all())))>{{ $permission->name }}</label>@endforeach
</fieldset>@endforeach</div><div><button class="btn btn-primary">Guardar rol</button><a class="btn btn-outline-secondary" href="{{ route('admin.roles.index') }}">Cancelar</a></div></form>
</x-plantilla-panel>
