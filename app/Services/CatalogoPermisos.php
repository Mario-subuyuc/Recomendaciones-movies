<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class CatalogoPermisos
{
    public function nombres(): array
    {
        $names = [];
        foreach (config('modulos') as $module => $data) {
            if (! preg_match('/^[a-z][a-z0-9_]*$/', $module)) {
                throw new \InvalidArgumentException('Nombre de módulo inválido.');
            }
            foreach ($data['acciones'] as $action) {
                if (! preg_match('/^[a-z][a-z0-9_]*$/', $action)) {
                    throw new \InvalidArgumentException('Acción inválida.');
                }
                $names[] = $module.'.'.$action;
            }
        }

        return array_values(array_unique($names));
    }

    public function sincronizar(): int
    {
        $names = $this->nombres();
        $count = DB::transaction(function () use ($names) {
            $created = 0;
            foreach ($names as $name) {
                $permission = Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
                $created += (int) $permission->wasRecentlyCreated;
            }
            foreach (config('roles.predefinidos') as $name => $defaults) {
                $role = Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
                if ($role->wasRecentlyCreated) {
                    $role->syncPermissions($defaults);
                }
            }
            Role::findByName('Administrador', 'web')->givePermissionTo($names);

            return $created;
        });
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $count;
    }
}
