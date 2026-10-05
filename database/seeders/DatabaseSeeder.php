<?php

namespace Database\Seeders;

use App\Models\User;
use App\Services\CatalogoPermisos;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Permisos
        |--------------------------------------------------------------------------
        */

        app(PermissionRegistrar::class)->forgetCachedPermissions();
        app(CatalogoPermisos::class)->sincronizar();
        Permission::where('guard_name', 'web')->where(function ($query) {
            $query->where('name', 'like', 'productos.%')->orWhere('name', 'like', 'ventas.%');
        })->delete();

        $permissions = [
            'usuarios.ver',
            'usuarios.crear',
            'usuarios.editar',
            'usuarios.eliminar',

            'peliculas.ver',
            'peliculas.crear',
            'peliculas.editar',
            'peliculas.eliminar',

            'videojuegos.ver',
            'videojuegos.crear',
            'videojuegos.editar',
            'videojuegos.eliminar',
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

        $admin->givePermissionTo(app(CatalogoPermisos::class)->nombres());

        /*
        |--------------------------------------------------------------------------
        | Permisos del Empleado
        |--------------------------------------------------------------------------
        */

        if ($empleado->wasRecentlyCreated) {
            $empleado->syncPermissions([
                'peliculas.ver',
                'peliculas.crear',
                'peliculas.editar',

                'videojuegos.ver',
                'videojuegos.crear',
                'videojuegos.editar',
            ]);
        }

        /*
        |--------------------------------------------------------------------------
        | Permisos del Cliente
        |--------------------------------------------------------------------------
        */

        if ($cliente->wasRecentlyCreated) {
            $cliente->syncPermissions([
                'peliculas.ver',
                'videojuegos.ver',
            ]);
        }

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

        $this->call(CatalogoSeeder::class);

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
