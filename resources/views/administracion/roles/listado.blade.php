<x-plantilla-panel>
<x-slot name="header"><h2 class="h3">Roles y accesos</h2><p class="text-muted">Los roles predefinidos son fijos. Crea roles personalizados para accesos específicos.</p></x-slot>
<div class="card card-body"><div class="mb-4"><a class="btn btn-primary" href="{{ route('admin.roles.create') }}">Crear rol personalizado</a></div>
<div class="table-responsive"><table class="table"><thead><tr><th>Rol</th><th>Tipo</th><th>Permisos</th><th>Acciones</th></tr></thead><tbody>
@foreach($roles as $role)<tr><td>{{ $role->name }}</td><td>{{ array_key_exists($role->name, config('roles.predefinidos')) ? 'Predefinido (fijo)' : 'Personalizado' }}</td><td>{{ $role->permissions->pluck('name')->join(', ') ?: 'Sin permisos' }}</td><td>
@if(!array_key_exists($role->name, config('roles.predefinidos')))<a class="btn btn-sm btn-outline-primary" href="{{ route('admin.roles.edit', $role) }}">Editar</a><form class="d-inline" method="POST" action="{{ route('admin.roles.destroy', $role) }}" onsubmit="return confirm('¿Eliminar este rol?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Eliminar</button></form>@endif
</td></tr>@endforeach</tbody></table></div></div>
</x-plantilla-panel>
