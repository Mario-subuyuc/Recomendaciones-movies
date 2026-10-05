<section aria-labelledby="password-title">
    <h3 id="password-title" class="h5">Cambiar contraseña</h3>
    <p class="text-muted small">Usa una contraseña larga y exclusiva para esta cuenta.</p>
    <form method="POST" action="{{ route('password.update') }}">
        @csrf @method('PUT')
        <x-dashboard-input name="current_password" label="Contraseña actual" type="password" required autocomplete="current-password" error-bag="updatePassword" />
        <x-dashboard-input name="password" label="Nueva contraseña" type="password" required autocomplete="new-password" error-bag="updatePassword" />
        <x-dashboard-input name="password_confirmation" label="Confirmar nueva contraseña" type="password" required autocomplete="new-password" error-bag="updatePassword" />
        <button class="btn btn-primary" type="submit">Actualizar contraseña</button>
    </form>
</section>