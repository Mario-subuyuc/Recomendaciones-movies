<x-dashboard-layout>
    <x-slot name="header"><h2 class="h3">{{ $record->titulo }}</h2><p class="text-muted">{{ $title }} · Detalle #{{ $record->getKey() }}</p></x-slot>
    <div class="card card-body"><dl class="row">
        @foreach($fields as $field => $label)<dt class="col-md-3">{{ $label }}</dt><dd class="col-md-9">{{ $field === 'fecha_registro' ? $record->fecha_registro->format('d/m/Y') : $record->$field }}</dd>@endforeach
    </dl><div><a class="btn btn-outline-secondary" href="{{ route($module.'.index') }}">Volver</a>@can($module.'.editar')<a class="btn btn-primary" href="{{ route($module.'.edit', $record->getKey()) }}">Editar</a>@endcan</div></div>
</x-dashboard-layout>
