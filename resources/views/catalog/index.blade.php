<x-dashboard-layout>
    <x-slot name="header"><h2 class="h3">{{ $title }}</h2><p class="text-muted">Explora y administra el catálogo.</p></x-slot>
    <div class="card card-body">
        <div class="d-flex justify-content-between align-items-center mb-4"><span>{{ $records->total() }} registros</span>@can($module.'.crear')<a class="btn btn-primary" href="{{ route($module.'.create') }}">Crear registro</a>@endcan</div>
        <div class="table-responsive"><table class="table table-hover"><thead><tr><th>ID</th><th>Título</th><th>Género</th><th>Plataforma</th><th>Año</th><th>Calificación</th><th>Acciones</th></tr></thead><tbody>
            @forelse($records as $record)<tr><td>{{ $record->getKey() }}</td><td>{{ $record->titulo }}</td><td>{{ $record->genero }}</td><td>{{ $record->plataforma }}</td><td>{{ $record->anio_lanzamiento }}</td><td>{{ $record->calificacion }}/10</td><td class="text-nowrap">
                @can($module.'.ver')<a class="btn btn-sm btn-outline-secondary" href="{{ route($module.'.show', $record->getKey()) }}">Ver</a>@endcan
                @can($module.'.editar')<a class="btn btn-sm btn-outline-primary" href="{{ route($module.'.edit', $record->getKey()) }}">Editar</a>@endcan
                @can($module.'.eliminar')<form class="d-inline" method="POST" action="{{ route($module.'.destroy', $record->getKey()) }}" onsubmit="return confirm('¿Eliminar este registro definitivamente?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Eliminar</button></form>@endcan
            </td></tr>@empty<tr><td colspan="7" class="text-center py-4">No hay registros.</td></tr>@endforelse
        </tbody></table></div>{{ $records->links('pagination::bootstrap-5') }}
    </div>
</x-dashboard-layout>
