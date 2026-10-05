<x-plantilla-panel>
<x-slot name="header"><h2 class="h3">Rutas y permisos de módulos</h2><p class="text-muted">Rutas protegidas registradas en el catálogo del proyecto.</p></x-slot>
<div class="card card-body"><div class="d-flex gap-3 mb-4"><form method="POST" action="{{ route('admin.permisos.sincronizar') }}">@csrf<button class="btn btn-primary">Sincronizar permisos</button></form><a class="btn btn-outline-primary" href="{{ route('admin.roles.index') }}">Configurar roles</a></div>
<p class="text-muted small">{{ count($permisos) }} permisos declarados. Sincronizar crea los nuevos sin eliminar permisos ni asignarlos automáticamente a Empleado o Cliente.</p>
<div class="table-responsive"><table class="table"><thead><tr><th>Permiso</th><th>Métodos</th><th>Ruta</th><th>Nombre</th></tr></thead><tbody>@foreach($rutas as $ruta)<tr><td>{{ $ruta['permiso'] }}</td><td>{{ $ruta['metodos'] }}</td><td><code>{{ $ruta['ruta'] }}</code></td><td>{{ $ruta['nombre'] }}</td></tr>@endforeach</tbody></table></div></div>
</x-plantilla-panel>
