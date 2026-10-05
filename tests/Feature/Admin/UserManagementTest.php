<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_seeded_accounts_can_login(): void
    {
        foreach (['mariosubuyucfb@gmail.com' => 'Administrador', 'msubuyuct@miumg.edu.gt' => 'Empleado', 'holamariost@gmail.com' => 'Cliente'] as $email => $role) {
            $this->post('/login', ['email' => $email, 'password' => '12345678'])->assertRedirect('/dashboard');
            $this->assertAuthenticatedAs(User::where('email', $email)->first());
            $this->assertTrue(auth()->user()->hasRole($role));
            $this->post('/logout');
        }
    }

    public function test_only_administrators_can_access_every_crud_action(): void
    {
        $target = User::where('email', 'holamariost@gmail.com')->first();
        $this->get('/dashboard/usuarios')->assertRedirect('/login');
        foreach (['msubuyuct@miumg.edu.gt', 'holamariost@gmail.com'] as $email) {
            $actor = User::where('email', $email)->first();
            $actor->givePermissionTo('usuarios.crear');
            $this->actingAs($actor);
            $this->get('/dashboard/usuarios')->assertForbidden();
            $this->get('/dashboard/usuarios/create')->assertForbidden();
            $this->get("/dashboard/usuarios/{$target->id}/edit")->assertForbidden();
            $this->post('/dashboard/usuarios', [])->assertForbidden();
            $this->put("/dashboard/usuarios/{$target->id}", [])->assertForbidden();
            $this->delete("/dashboard/usuarios/{$target->id}")->assertForbidden();
        }
    }

    public function test_administrator_can_manage_users_roles_and_direct_permissions(): void
    {
        $this->actingAs(User::where('email', 'mariosubuyucfb@gmail.com')->first());
        $this->get('/dashboard')->assertOk()->assertSee('Administrar usuarios');
        $this->get('/dashboard/usuarios')->assertOk();
        $this->get('/dashboard/usuarios/create')->assertOk();
        $data = ['name' => 'Nuevo', 'email' => 'nuevo@example.com', 'password' => 'password123', 'password_confirmation' => 'password123', 'roles' => ['Cliente'], 'permissions' => ['peliculas.editar']];
        $this->post('/dashboard/usuarios', $data)->assertSessionHasNoErrors()->assertRedirect('/dashboard/usuarios');
        $user = User::where('email', $data['email'])->firstOrFail();
        $this->assertTrue(Hash::check('password123', $user->password));
        $this->assertTrue($user->hasRole('Cliente'));
        $this->assertTrue($user->hasDirectPermission('peliculas.editar'));
        $this->assertTrue($user->hasPermissionTo('peliculas.ver'));
        $this->get("/dashboard/usuarios/{$user->id}/edit")->assertOk();
        $this->put("/dashboard/usuarios/{$user->id}", ['name' => 'Editado', 'email' => $user->email, 'roles' => ['Empleado'], 'password' => ''])->assertSessionHasNoErrors();
        $user->refresh()->unsetRelation('roles')->unsetRelation('permissions');
        $this->assertSame('Editado', $user->name);
        $this->assertTrue($user->hasRole('Empleado'));
        $this->assertCount(0, $user->permissions);
        $this->assertTrue(Hash::check('password123', $user->password));
        $this->delete("/dashboard/usuarios/{$user->id}")->assertRedirect('/dashboard/usuarios');
        $this->assertModelMissing($user);
    }

    public function test_invalid_assignments_and_self_lockout_are_rejected(): void
    {
        $admin = User::where('email', 'mariosubuyucfb@gmail.com')->first();
        $this->actingAs($admin);
        $this->post('/dashboard/usuarios', ['name' => 'Test', 'email' => $admin->email, 'password' => 'password123', 'password_confirmation' => 'password123', 'roles' => ['Inventado'], 'permissions' => ['inventado.ver']])->assertSessionHasErrors(['email', 'roles.0', 'permissions.0']);
        $this->put("/dashboard/usuarios/{$admin->id}", ['name' => $admin->name, 'email' => $admin->email, 'roles' => ['Cliente']])->assertSessionHasErrors('roles');
        $this->delete("/dashboard/usuarios/{$admin->id}")->assertSessionHasErrors('user');
        $this->assertModelExists($admin);
        $this->assertTrue($admin->fresh()->hasRole('Administrador'));
    }
}
