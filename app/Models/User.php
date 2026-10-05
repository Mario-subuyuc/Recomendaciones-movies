<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Services\CatalogoPermisos;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    public function hasPermissionTo($permission, $guardName = null): bool
    {
        $permission = $this->filterPermission($permission, $guardName);
        if ($permission->guard_name !== 'web') {
            return false;
        }
        $roles = $this->loadMissing('roles')->roles;
        if ($roles->count() !== 1) {
            return false;
        }
        $name = $roles->first()->name;
        if ($name === 'Administrador') {
            return in_array($permission->name, app(CatalogoPermisos::class)->nombres(), true);
        }

        return $this->hasPermissionViaRole($permission);
    }

    use HasFactory, HasRoles, Notifiable;

    public function puedeGestionarUsuario(User $destino): bool
    {
        if ($this->hasRole('Administrador') || $destino->is($this)) {
            return true;
        }
        if (! $destino->hasRole('Cliente')) {
            return false;
        }

        return $destino->getPermissionsViaRoles()->every(fn ($permission) => $this->can($permission->name));
    }

    public function puedeCrearCliente(): bool
    {
        return $this->hasRole('Administrador') || Role::findByName('Cliente', 'web')->permissions->every(fn ($permission) => $this->can($permission->name));
    }

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function conversaciones(): HasMany
    {
        return $this->hasMany(Conversacion::class, 'id_usuario');
    }

    public function consumos(): HasMany
    {
        return $this->hasMany(ConsumoToken::class, 'id_usuario');
    }

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
