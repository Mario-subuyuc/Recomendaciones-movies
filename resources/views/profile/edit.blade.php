<x-dashboard-layout>
    <x-slot name="header"><h2 class="h3">Mi perfil</h2><p class="text-muted">Gestiona tus datos y la seguridad de tu cuenta.</p></x-slot>
    <div class="row g-4">
        <div class="col-lg-6"><div class="card"><div class="card-body">@include('profile.partials.update-profile-information-form')</div></div></div>
        <div class="col-lg-6"><div class="card"><div class="card-body">@include('profile.partials.update-password-form')</div></div></div>
        <div class="col-12"><div class="card border border-danger"><div class="card-body">@include('profile.partials.delete-user-form')</div></div></div>
    </div>
</x-dashboard-layout>