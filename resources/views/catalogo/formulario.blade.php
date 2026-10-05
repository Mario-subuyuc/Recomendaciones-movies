<x-plantilla-panel>
    <x-slot name="header"><h2 class="h3">{{ $title }} · {{ $record->exists ? 'Editar' : 'Crear' }}</h2></x-slot>
    <form class="card card-body" method="POST" action="{{ $record->exists ? route($module.'.update', $record->getKey()) : route($module.'.store') }}">
        @csrf @if($record->exists) @method('PUT') @endif
        <div class="row">
            @foreach($fields as $field => $label)
                <div class="col-md-6">
                    @if($field === 'actores')
                        <div class="mb-3"><label class="form-label" for="actores">{{ $label }}</label><textarea class="form-control" id="actores" name="actores" rows="3" required maxlength="5000">{{ old('actores', $record->actores) }}</textarea></div>
                    @else
                        @php($numeric = in_array($field, ['anio_lanzamiento', 'calificacion', 'duracion_minutos', 'jugadores']))
                        <x-campo-panel :name="$field" :label="$label" :type="$field === 'fecha_registro' ? 'date' : ($numeric ? 'number' : 'text')" :value="$field === 'fecha_registro' ? ($record->fecha_registro?->format('Y-m-d') ?? now()->format('Y-m-d')) : $record->$field" :step="$field === 'calificacion' ? '0.1' : ($numeric ? '1' : null)" :min="$field === 'calificacion' ? 0 : ($numeric ? ($field === 'anio_lanzamiento' ? 1888 : 1) : null)" :max="$field === 'calificacion' ? 10 : ($field === 'anio_lanzamiento' ? 2100 : null)" required />
                    @endif
                </div>
            @endforeach
        </div>
        <div class="d-flex gap-2"><button class="btn btn-primary">Guardar</button>@can($module.'.ver')<a class="btn btn-outline-secondary" href="{{ route($module.'.index') }}">Volver al catálogo</a>@else<a class="btn btn-outline-secondary" href="{{ route('dashboard') }}">Volver al dashboard</a>@endcan</div>
    </form>
</x-plantilla-panel>
