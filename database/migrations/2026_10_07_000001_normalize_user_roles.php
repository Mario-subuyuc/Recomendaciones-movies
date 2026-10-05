<?php

use App\Models\User;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function () {
            foreach (config('roles.predefinidos') as $name => $permissions) {
                $role = Role::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
                foreach ($permissions as $permission) {
                    Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
                }
                $role->syncPermissions($permissions);
            }
            User::with('roles')->each(function ($user) {
                $roles = $user->roles->sortBy(fn ($role) => match ($role->name) {
                    'Administrador' => '0', 'Empleado' => '1', 'Cliente' => '2', default => '3'.$role->id
                });
                $user->syncRoles([$roles->first()?->name ?? 'Cliente']);
                $user->syncPermissions([]);
            });
        });
        app(PermissionRegistrar::class)->forgetCachedPermissions();
        Schema::table('model_has_roles', function (Blueprint $table) {
            $table->unique(['model_type', 'model_id'], 'un_rol_por_usuario');
        });
    }

    public function down(): void
    {
        Schema::table('model_has_roles', fn (Blueprint $table) => $table->dropUnique('un_rol_por_usuario'));
        // La normalización no restaura permisos individuales ni asignaciones anteriores.
    }
};
