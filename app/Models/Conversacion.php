<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Conversacion extends Model
{
    protected $table = 'conversaciones';

    protected $primaryKey = 'id_conversacion';

    public $timestamps = false;

    protected $fillable = ['id_usuario', 'categoria', 'consulta_uuid', 'fecha_creacion'];

    protected function casts(): array
    {
        return ['fecha_creacion' => 'datetime'];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function mensajes(): HasMany
    {
        return $this->hasMany(Mensaje::class, 'id_conversacion', 'id_conversacion')->orderBy('id_mensaje');
    }
}
