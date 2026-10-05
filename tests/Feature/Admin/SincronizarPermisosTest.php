<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class SincronizarPermisosTest extends TestCase
{
    use RefreshDatabase;

    public function test_new_module_is_synced_without_overwriting_existing_access(): void
    {
        $this->seed();
        Role::findByName('Cliente')->syncPermissions(['usuarios.ver']);
        config(['modulos.reportes' => ['nombre' => 'Reportes', 'ruta' => 'reportes.index', 'acciones' => ['ver', 'exportar']]]);
        $this->artisan('permisos:sincronizar')->assertSuccessful();
        $this->artisan('permisos:sincronizar')->assertSuccessful();
        $this->assertSame(1, Permission::where('name', 'reportes.ver')->count());
        $this->assertTrue(Role::findByName('Cliente')->hasPermissionTo('usuarios.ver'));
        $this->assertFalse(Role::findByName('Cliente')->hasPermissionTo('reportes.ver'));
        $this->assertTrue(User::where('email', 'mariosubuyucfb@gmail.com')->first()->can('reportes.ver'));
        $this->assertTrue(User::where('email', 'holamariost@gmail.com')->first()->can('usuarios.ver'));
    }

    public function test_catalog_and_sync_are_administrator_only(): void
    {
        $this->seed();
        $this->actingAs(User::where('email', 'mariosubuyucfb@gmail.com')->first());
        $this->get('/dashboard/permisos')->assertOk()->assertSee('peliculas.ver')->assertSee('/dashboard/peliculas');
        $this->post('/dashboard/permisos/sincronizar')->assertSessionHasNoErrors();
        $this->actingAs(User::where('email', 'holamariost@gmail.com')->first());
        $this->get('/dashboard/permisos')->assertForbidden();
        $this->post('/dashboard/permisos/sincronizar')->assertForbidden();
    }

    public function test_delegated_user_management_cannot_create_or_edit_a_more_privileged_client(): void
    {
        $this->seed();
        $role = Role::create(['name' => 'Gestor usuarios', 'guard_name' => 'web']);
        $role->syncPermissions(['usuarios.crear', 'usuarios.editar', 'usuarios.eliminar']);
        $actor = User::factory()->create();
        $actor->syncRoles($role);
        $target = User::where('email', 'holamariost@gmail.com')->first();
        $this->actingAs($actor);
        $this->get('/dashboard/usuarios/create')->assertForbidden();
        $this->post('/dashboard/usuarios', ['name' => 'Nuevo', 'email' => 'nuevo@example.com', 'password' => 'password123', 'password_confirmation' => 'password123'])->assertForbidden();
        $this->put('/dashboard/usuarios/'.$target->id, ['name' => 'Otro', 'email' => $target->email])->assertForbidden();
        $this->delete('/dashboard/usuarios/'.$target->id)->assertForbidden();
    }
}
