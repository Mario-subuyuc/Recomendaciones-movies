<div class="dashboard-topbar-box dashboard-user-box" aria-label="Cuenta actual">
    <div class="dashboard-user-avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr(trim(auth()->user()->name), 0, 1)) }}</div>
    <div class="dashboard-user">
        <div class="dashboard-user-heading">
            <a class="dashboard-user-name" href="{{ route('profile.edit') }}" aria-label="Ver perfil de {{ auth()->user()->name }}">{{ auth()->user()->name }}</a>
            <span class="dashboard-user-roles" aria-label="Roles de la cuenta">
                @forelse (auth()->user()->roles as $role)
                    <span class="dashboard-role">{{ $role->name }}</span>
                @empty
                    <span class="dashboard-role">Sin rol</span>
                @endforelse
            </span>
        </div>
        <div class="dashboard-user-info">{{ auth()->user()->email }}</div>
    </div>
</div>
