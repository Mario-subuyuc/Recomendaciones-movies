<section aria-labelledby="profile-title">
    <h3 id="profile-title" class="h5">Información personal</h3>
    <p class="text-muted small">Actualiza tu nombre y correo electrónico.</p>
    <form id="send-verification" method="POST" action="{{ route('verification.send') }}">@csrf</form>
    <form method="POST" action="{{ route('profile.update') }}">
        @csrf @method('PATCH')
        <x-campo-panel name="name" label="Nombre" :value="$user->name" required autocomplete="name" />
        <x-campo-panel name="email" label="Correo electrónico" type="email" :value="$user->email" required autocomplete="username" />
        @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
            <div class="alert alert-warning">Tu correo todavía no está verificado. <button form="send-verification" class="btn btn-sm btn-outline-primary" type="submit">Reenviar verificación</button></div>
        @endif
        <button class="btn btn-primary" type="submit">Guardar datos</button>
    </form>
</section>