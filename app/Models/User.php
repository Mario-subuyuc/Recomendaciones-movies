<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    public function hasPermissionTo($permission, $guardName = null): bool
    {
        $permission = $this->filterPermission($permission, $guardName);
        if ($permission->guard_name !== 'web') return false;
        $roles = $this->loadMissing('roles')->roles;
        if ($roles->count() !== 1) {
            return false;
        }
        $name = $roles->first()->name;
        $fixed = config('roles.predefinidos');
        if (array_key_exists($name, $fixed)) {
            return in_array($permission->name, $fixed[$name], true);
        }

        return $this->hasPermissionViaRole($permission);
    }

    use HasFactory, HasRoles, Notifiable;

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
