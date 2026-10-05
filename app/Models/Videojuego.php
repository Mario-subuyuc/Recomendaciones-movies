<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Videojuego extends Model
{
    protected $table = 'videojuegos';

    protected $primaryKey = 'id_videojuego';

    protected $fillable = [
        0 => 'titulo',
        1 => 'genero',
        2 => 'plataforma',
        3 => 'anio_lanzamiento',
        4 => 'calificacion',
        5 => 'desarrollador',
        6 => 'jugadores',
        7 => 'fecha_registro',
    ];

    protected function casts(): array
    {
        return ['anio_lanzamiento' => 'integer', 'calificacion' => 'decimal:1', 'fecha_registro' => 'date', 'jugadores' => 'string'];
    }
}
