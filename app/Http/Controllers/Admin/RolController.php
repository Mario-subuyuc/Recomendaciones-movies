<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolController extends Controller
{
    public function index()
    {
        return view('administracion.roles.listado', ['roles' => Role::where('guard_name', 'web')->with('permissions')->orderBy('name')->get()]);
    }

    public function create()
    {
        return $this->form(new Role);
    }

    public function edit(Role $role)
    {
        $this->editable($role);

        return $this->form($role);
    }

    public function store(Request $request)
    {
        $data = $this->validar($request);
        DB::transaction(function () use ($data) {
            $role = Role::create(['name' => $data['name'], 'guard_name' => 'web']);
            $role->syncPermissions($data['permissions'] ?? []);
        });

        return to_route('admin.roles.index')->with('status', 'Rol personalizado creado.');
    }

    public function update(Request $request, Role $role)
    {
        $this->editable($role);
        $data = $this->validar($request, $role);
        DB::transaction(function () use ($role, $data) {
            $role->update(['name' => $data['name']]);
            $role->syncPermissions($data['permissions'] ?? []);
        });

        return to_route('admin.roles.index')->with('status', 'Rol actualizado. Sus usuarios reciben los nuevos permisos.');
    }

    public function destroy(Role $role)
    {
        $this->personalizado($role);

        return DB::transaction(function () use ($role) {
            $role->lockForUpdate()->findOrFail($role->id);
            if ($role->users()->exists()) {
                return back()->withErrors(['role' => 'Reasigna los usuarios antes de eliminar este rol.']);
            }
            $role->delete();

            return to_route('admin.roles.index')->with('status', 'Rol eliminado.');
        });
    }

    private function personalizado(Role $role): void
    {
        abort_if($role->guard_name !== 'web' || array_key_exists($role->name, config('roles.predefinidos')), 403);
    }

    private function editable(Role $role): void
    {
        abort_if($role->guard_name !== 'web' || $role->name === 'Administrador', 403);
    }

    private function validar(Request $request, ?Role $role = null): array
    {
        $request->merge(['name' => is_string($request->input('name')) ? trim($request->input('name')) : $request->input('name')]);
        $predefinido = $role && array_key_exists($role->name, config('roles.predefinidos'));
        if ($predefinido && $request->input('name') !== $role->name) {
            throw ValidationException::withMessages(['name' => 'El nombre de este rol predefinido se conserva.']);
        }
        if (! $predefinido && is_string($request->input('name')) && in_array(mb_strtolower($request->input('name')), array_map('mb_strtolower', array_keys(config('roles.predefinidos'))), true)) {
            throw ValidationException::withMessages(['name' => 'Este nombre está reservado para un rol predefinido.']);
        }

        return $request->validate([
            'name' => ['required', 'string', 'max:100', Rule::unique('roles', 'name')->where('guard_name', 'web')->ignore($role)],
            'permissions' => ['sometimes', 'array'],
            'permissions.*' => ['string', 'distinct', Rule::exists('permissions', 'name')->where('guard_name', 'web')],
        ]);
    }

    private function form(Role $role)
    {
        return view('administracion.roles.formulario', ['role' => $role, 'groups' => Permission::where('guard_name', 'web')->orderBy('name')->get()->groupBy(fn ($p) => explode('.', $p->name)[0])]);
    }
}
