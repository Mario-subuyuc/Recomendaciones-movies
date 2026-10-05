<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RolesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    private function admin(): User
    {
        return User::where('email', 'mariosubuyucfb@gmail.com')->first();
    }

    private function cliente(): User
    {
        return User::where('email', 'holamariost@gmail.com')->first();
    }

    public function test_custom_read_only_role_accesses_users_without_other_actions(): void
    {
        $this->actingAs($this->admin());
        $this->post('/dashboard/roles', ['name' => 'Consultar usuarios', 'permissions' => ['usuarios.ver']])->assertSessionHasNoErrors();
        $role = Role::findByName('Consultar usuarios');
        $user = $this->cliente();
        $this->put('/dashboard/usuarios/'.$user->id, ['name' => $user->name, 'email' => $user->email, 'roles' => [$role->name]])->assertSessionHasNoErrors();
        $this->actingAs($user->fresh());
        $this->get('/dashboard/usuarios')->assertOk()->assertDontSee('Crear usuario')->assertDontSee('>Editar</a>', false);
        $this->get('/dashboard/usuarios/create')->assertForbidden();
        $this->get('/dashboard/usuarios/'.$this->admin()->id.'/edit')->assertForbidden();
        $this->delete('/dashboard/usuarios/'.$this->admin()->id)->assertForbidden();
        $this->get('/dashboard/roles')->assertForbidden();
        $this->get('/dashboard/peliculas')->assertForbidden();
        $this->get('/dashboard/chat')->assertForbidden();
    }

    public function test_fixed_roles_ignore_extra_permissions_and_cannot_be_changed(): void
    {
        $client = $this->cliente();
        $client->givePermissionTo('usuarios.ver');
        $role = Role::findByName('Cliente');
        $this->actingAs($client)->get('/dashboard/usuarios')->assertForbidden();
        $this->actingAs($this->admin());
        $this->post('/dashboard/roles', ['name' => 'administrador', 'permissions' => ['usuarios.ver']])->assertSessionHasErrors('name');
        $this->get('/dashboard/roles/'.$role->id.'/edit')->assertOk();
        $this->put('/dashboard/roles/'.$role->id, ['name' => 'Otro', 'permissions' => ['usuarios.ver']])->assertSessionHasErrors('name');
        $this->put('/dashboard/roles/'.$role->id, ['name' => 'Cliente', 'permissions' => ['usuarios.ver']])->assertSessionHasNoErrors();
        $this->actingAs($client->fresh())->get('/dashboard/usuarios')->assertOk();
        $this->get('/dashboard/peliculas')->assertForbidden();
        $this->actingAs($this->admin());
        $adminRole = Role::findByName('Administrador');
        $this->get('/dashboard/roles/'.$adminRole->id.'/edit')->assertForbidden();
        $this->delete('/dashboard/roles/'.$role->id)->assertForbidden();
        $this->put('/dashboard/usuarios/'.$client->id, ['name' => $client->name, 'email' => $client->email, 'roles' => ['Cliente', 'Empleado']])->assertSessionHasErrors('roles');
        $this->put('/dashboard/usuarios/'.$client->id, ['name' => $client->name, 'email' => $client->email, 'roles' => ['Cliente'], 'permissions' => ['usuarios.ver']])->assertSessionHasErrors('permissions');
    }

    public function test_database_rejects_a_second_role_for_the_same_user(): void
    {
        $user = $this->cliente();
        $role = Role::findByName('Empleado');
        $this->expectException(QueryException::class);
        DB::table('model_has_roles')->insert(['role_id' => $role->id, 'model_type' => User::class, 'model_id' => $user->id]);
    }

    public function test_custom_roles_can_be_updated_but_assigned_roles_cannot_be_deleted(): void
    {
        $this->actingAs($this->admin());
        $this->get('/dashboard/roles/create')->assertOk();
        $this->post('/dashboard/roles', ['name' => 'Catálogo', 'permissions' => ['peliculas.ver']])->assertSessionHasNoErrors();
        $role = Role::findByName('Catálogo');
        $this->get('/dashboard/roles/'.$role->id.'/edit')->assertOk();
        $this->cliente()->syncRoles($role);
        $this->delete('/dashboard/roles/'.$role->id)->assertSessionHasErrors('role');
        $this->put('/dashboard/roles/'.$role->id, ['name' => 'Solo videojuegos', 'permissions' => ['videojuegos.ver']])->assertSessionHasNoErrors();
        $this->actingAs($this->cliente());
        $this->get('/dashboard/peliculas')->assertForbidden();
        $this->get('/dashboard/videojuegos')->assertOk();
        $this->actingAs($this->admin());
        $this->cliente()->syncRoles('Cliente');
        $this->delete('/dashboard/roles/'.$role->id)->assertSessionHasNoErrors();
        $this->assertModelMissing($role);
    }

    public function test_user_editor_cannot_escalate_privileges_or_modify_an_administrator(): void
    {
        $role = Role::create(['name' => 'Gestor limitado', 'guard_name' => 'web']);
        $role->syncPermissions(['usuarios.ver', 'usuarios.crear', 'usuarios.editar', 'usuarios.eliminar', 'peliculas.ver', 'videojuegos.ver']);
        $actor = User::factory()->create();
        $actor->syncRoles($role);
        $this->actingAs($actor);
        $admin = $this->admin();
        $this->get('/dashboard/usuarios/'.$admin->id.'/edit')->assertForbidden();
        $this->put('/dashboard/usuarios/'.$admin->id, ['name' => 'Hack', 'email' => 'hack@example.com'])->assertForbidden();
        $this->delete('/dashboard/usuarios/'.$admin->id)->assertForbidden();
        $this->put('/dashboard/usuarios/'.$actor->id, ['name' => $actor->name, 'email' => $actor->email, 'roles' => ['Administrador']])->assertSessionHasErrors('roles');
        $this->post('/dashboard/usuarios', ['name' => 'Nuevo', 'email' => 'nuevo@example.com', 'password' => 'password123', 'password_confirmation' => 'password123'])->assertSessionHasNoErrors();
        $new = User::where('email', 'nuevo@example.com')->firstOrFail();
        $this->assertTrue($new->hasRole('Cliente'));
        $this->put('/dashboard/usuarios/'.$new->id, ['name' => 'Editado', 'email' => $new->email])->assertSessionHasNoErrors();
        $this->assertSame('Editado', $new->fresh()->name);
        $this->assertTrue($new->fresh()->hasRole('Cliente'));
        $this->delete('/dashboard/usuarios/'.$new->id)->assertSessionHasNoErrors();
    }
}
