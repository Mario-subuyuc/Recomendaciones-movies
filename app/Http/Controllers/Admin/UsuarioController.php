<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GuardarUsuarioRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;

class UsuarioController extends Controller
{
    public function index(): View
    {
        return view('administracion.usuarios.listado', ['users' => User::with(['roles', 'permissions'])->orderBy('id')->paginate(15)]);
    }

    public function create(): View
    {
        abort_unless(auth()->user()->puedeCrearCliente(), 403, 'El rol Cliente tiene accesos superiores a los tuyos. Solicita la creación al Administrador.');

        return $this->form(new User);
    }

    public function store(GuardarUsuarioRequest $request): RedirectResponse
    {
        abort_unless($request->user()->puedeCrearCliente(), 403, 'El rol Cliente tiene accesos superiores a los tuyos. Solicita la creación al Administrador.');
        DB::transaction(function () use ($request) {
            $user = User::create($request->safe()->only(['name', 'email', 'password']));
            $user->syncRoles($request->user()->hasRole('Administrador') ? $request->validated('roles') : ['Cliente']);
            $user->syncPermissions([]);
        });

        return to_route(auth()->user()->can('usuarios.ver') ? 'admin.usuarios.index' : 'dashboard')->with('status', 'Usuario creado correctamente.');
    }

    public function edit(User $user): View
    {
        $this->comprobarDestino($user);

        return $this->form($user->load(['roles', 'permissions']));
    }

    public function update(GuardarUsuarioRequest $request, User $user): RedirectResponse
    {
        $this->comprobarDestino($user);
        if ($request->user()->hasRole('Administrador') && $user->is($request->user()) && ! in_array('Administrador', $request->validated('roles'), true)) {
            throw ValidationException::withMessages(['roles' => 'No puedes quitarte el rol Administrador.']);
        }

        DB::transaction(function () use ($request, $user) {
            $attributes = $request->safe()->only(['name', 'email', 'password']);
            if (empty($attributes['password'])) {
                unset($attributes['password']);
            }
            if ($user->email !== $attributes['email']) {
                $attributes['email_verified_at'] = null;
            }
            $user->forceFill($attributes)->save();
            if ($request->user()->hasRole('Administrador')) {
                $user->syncRoles($request->validated('roles'));
            }
            $user->syncPermissions([]);
        });

        return to_route(auth()->user()->can('usuarios.ver') ? 'admin.usuarios.index' : 'dashboard')->with('status', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $user): RedirectResponse
    {
        $this->comprobarDestino($user);
        if ($user->is(auth()->user())) {
            throw ValidationException::withMessages(['user' => 'No puedes eliminar tu propia cuenta desde este módulo.']);
        }
        DB::transaction(fn () => $user->delete());

        return to_route(auth()->user()->can('usuarios.ver') ? 'admin.usuarios.index' : 'dashboard')->with('status', 'Usuario eliminado correctamente.');
    }

    private function form(User $user): View
    {
        return view('administracion.usuarios.formulario', [
            'user' => $user,
            'roles' => Role::where('guard_name', 'web')->with('permissions')->orderBy('name')->get(),
        ]);
    }

    private function comprobarDestino(User $user): void
    {
        abort_unless(auth()->user()->puedeGestionarUsuario($user), 403, 'Solo el Administrador puede modificar cuentas con accesos superiores a los tuyos.');
    }
}
