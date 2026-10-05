<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ConsumoToken extends Model
{
    protected $table = 'consumo_tokens';

    protected $primaryKey = 'id_consumo';

    public $timestamps = false;

    protected $fillable = ['id_usuario', 'id_conversacion', 'consulta_uuid', 'categoria', 'etapa', 'tokens_entrada', 'tokens_salida', 'tokens', 'fecha'];

    protected function casts(): array
    {
        return ['fecha' => 'datetime', 'tokens' => 'integer'];
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function conversacion(): BelongsTo
    {
        return $this->belongsTo(Conversacion::class, 'id_conversacion', 'id_conversacion');
    }
}
