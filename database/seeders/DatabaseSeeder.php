<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Permisos
        |--------------------------------------------------------------------------
        */

        $permissions = [
            'usuarios.ver',
            'usuarios.crear',
            'usuarios.editar',
            'usuarios.eliminar',

            'productos.ver',
            'productos.crear',
            'productos.editar',
            'productos.eliminar',

            'ventas.ver',
            'ventas.crear',
            'ventas.editar',
            'ventas.eliminar',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */

        $admin = Role::firstOrCreate([
            'name' => 'Administrador',
            'guard_name' => 'web',
        ]);

        $empleado = Role::firstOrCreate([
            'name' => 'Empleado',
            'guard_name' => 'web',
        ]);

        $cliente = Role::firstOrCreate([
            'name' => 'Cliente',
            'guard_name' => 'web',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Permisos del Administrador
        |--------------------------------------------------------------------------
        */

        $admin->syncPermissions(
            Permission::all()
        );

        /*
        |--------------------------------------------------------------------------
        | Permisos del Empleado
        |--------------------------------------------------------------------------
        */

        $empleado->syncPermissions([
            'productos.ver',
            'productos.crear',
            'productos.editar',

            'ventas.ver',
            'ventas.crear',
            'ventas.editar',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Permisos del Cliente
        |--------------------------------------------------------------------------
        */

        $cliente->syncPermissions([
            'productos.ver',
            'ventas.crear',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Usuario Administrador
        |--------------------------------------------------------------------------
        */

        $user = User::updateOrCreate(
            [
                'email' => 'mariosubuyucfb@gmail.com',
            ],
            [
                'name' => 'mario',
                'password' => Hash::make('12345678'),
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Asignar Administrador
        |--------------------------------------------------------------------------
        */

        $user->syncRoles([
            'Administrador',
        ]);

        foreach ([
            ['name' => 'laureano', 'email' => 'msubuyuct@miumg.edu.gt', 'role' => 'Empleado'],
            ['name' => 'user1', 'email' => 'holamariost@gmail.com', 'role' => 'Cliente'],
        ] as $account) {
            $accountUser = User::updateOrCreate(
                ['email' => $account['email']],
                ['name' => $account['name'], 'password' => Hash::make('12345678')]
            );
            $accountUser->syncRoles([$account['role']]);
        }
    }
}
