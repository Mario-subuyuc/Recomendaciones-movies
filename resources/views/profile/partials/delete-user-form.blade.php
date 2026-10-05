<section aria-labelledby="delete-title">
    <h3 id="delete-title" class="h5 text-danger">Eliminar cuenta</h3>
    <p class="text-muted">Esta acción elimina permanentemente tu cuenta. Guarda la información que necesites antes de continuar.</p>
    <button id="open-delete-account" class="btn btn-outline-danger" type="button">Eliminar mi cuenta</button>
    <dialog id="delete-account-dialog" class="account-dialog rounded p-4" aria-labelledby="delete-dialog-title">
        <form method="POST" action="{{ route('profile.destroy') }}">
            @csrf @method('DELETE')
            <h4 id="delete-dialog-title" class="h5">¿Eliminar tu cuenta definitivamente?</h4>
            <p class="text-muted">Introduce tu contraseña para confirmar esta acción.</p>
            <label class="form-label" for="delete_password">Contraseña</label>
            <input id="delete_password" name="password" type="password" class="form-control @error('password', 'userDeletion') is-invalid @enderror" required autocomplete="current-password">
            @error('password', 'userDeletion')<div class="invalid-feedback">{{ $message }}</div>@enderror
            <div class="d-flex justify-content-end gap-2 mt-4"><button id="cancel-delete-account" class="btn btn-outline-secondary" type="button">Cancelar</button><button class="btn btn-danger" type="submit">Eliminar cuenta</button></div>
        </form>
    </dialog>
</section>
@push('styles')
<style>.account-dialog { width: min(32rem, calc(100% - 2rem)); border: 1px solid var(--bs-border-color); background: var(--bs-body-bg); color: var(--bs-body-color); } .account-dialog::backdrop { background: #0008; }</style>
@endpush
@push('scripts')
<script>
    const deletionDialog = document.getElementById('delete-account-dialog');
    document.getElementById('open-delete-account').addEventListener('click', () => deletionDialog.showModal());
    document.getElementById('cancel-delete-account').addEventListener('click', () => deletionDialog.close());
    @if ($errors->userDeletion->isNotEmpty()) deletionDialog.showModal(); @endif
</script>
@endpush