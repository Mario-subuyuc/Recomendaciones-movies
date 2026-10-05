<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\GuardarUsuarioRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UsuarioController extends Controller
{
    public function index(): View
    {
        return view('administracion.usuarios.listado', ['users' => User::with(['roles', 'permissions'])->orderBy('id')->paginate(15)]);
    }

    public function create(): View
    {
        return $this->form(new User);
    }

    public function store(GuardarUsuarioRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request) {
            $user = User::create($request->safe()->only(['name', 'email', 'password']));
            $user->syncRoles($request->validated('roles'));
            $user->syncPermissions($request->validated('permissions', []));
        });

        return to_route('admin.usuarios.index')->with('status', 'Usuario creado correctamente.');
    }

    public function edit(User $user): View
    {
        return $this->form($user->load(['roles', 'permissions']));
    }

    public function update(GuardarUsuarioRequest $request, User $user): RedirectResponse
    {
        if ($user->is($request->user()) && ! in_array('Administrador', $request->validated('roles'), true)) {
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
            $user->syncRoles($request->validated('roles'));
            $user->syncPermissions($request->validated('permissions', []));
        });

        return to_route('admin.usuarios.index')->with('status', 'Usuario actualizado correctamente.');
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->is(auth()->user())) {
            throw ValidationException::withMessages(['user' => 'No puedes eliminar tu propia cuenta desde este módulo.']);
        }
        DB::transaction(fn () => $user->delete());

        return to_route('admin.usuarios.index')->with('status', 'Usuario eliminado correctamente.');
    }

    private function form(User $user): View
    {
        return view('administracion.usuarios.formulario', [
            'user' => $user,
            'roles' => Role::where('guard_name', 'web')->with('permissions')->orderBy('name')->get(),
            'permissionGroups' => Permission::where('guard_name', 'web')->orderBy('name')->get()->groupBy(fn ($permission) => explode('.', $permission->name)[0]),
        ]);
    }
}
