<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelicula extends Model
{
    protected $table = 'peliculas';

    protected $primaryKey = 'id_pelicula';

    protected $fillable = [
        0 => 'titulo',
        1 => 'genero',
        2 => 'plataforma',
        3 => 'anio_lanzamiento',
        4 => 'calificacion',
        5 => 'director',
        6 => 'actores',
        7 => 'productora',
        8 => 'duracion_minutos',
        9 => 'clasificacion',
        10 => 'fecha_registro',
    ];

    protected function casts(): array
    {
        return ['anio_lanzamiento' => 'integer', 'calificacion' => 'decimal:1', 'fecha_registro' => 'date', 'duracion_minutos' => 'integer'];
    }
}
