<?php

namespace App\Http\Controllers;

use App\Models\Pelicula;

class PeliculaController extends CatalogController
{
    protected string $model = Pelicula::class;

    protected string $module = 'peliculas';

    protected string $title = 'Películas';

    protected array $fields = [
        'titulo' => 'Título',
        'genero' => 'Género',
        'plataforma' => 'Plataforma',
        'anio_lanzamiento' => 'Año de lanzamiento',
        'calificacion' => 'Calificación (0–10)',
        'director' => 'Director',
        'actores' => 'Actores',
        'productora' => 'Productora',
        'duracion_minutos' => 'Duración (minutos)',
        'clasificacion' => 'Clasificación',
        'fecha_registro' => 'Fecha de registro',
    ];
}
