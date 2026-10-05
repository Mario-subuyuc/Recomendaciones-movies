<div class="dashboard-user">
    <div class="d-flex flex-wrap align-items-center gap-2">
        <strong>{{ auth()->user()->name }}</strong>
        @forelse (auth()->user()->roles as $role)
            <span class="badge bg-light-primary">{{ $role->name }}</span>
        @empty
            <span class="badge bg-light-secondary">Sin rol</span>
        @endforelse
    </div>
    <small class="text-muted d-block mt-1">{{ auth()->user()->email }}</small>
</div>
