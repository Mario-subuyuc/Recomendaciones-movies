<?php

namespace App\Http\Controllers;

use App\Models\Videojuego;

class VideojuegoController extends CatalogoController
{
    protected string $model = Videojuego::class;

    protected string $module = 'videojuegos';

    protected string $title = 'Videojuegos';

    protected array $fields = [
        'titulo' => 'Título',
        'genero' => 'Género',
        'plataforma' => 'Plataforma',
        'anio_lanzamiento' => 'Año de lanzamiento',
        'calificacion' => 'Calificación (0–10)',
        'desarrollador' => 'Desarrollador',
        'jugadores' => 'Jugadores',
        'fecha_registro' => 'Fecha de registro',
    ];
}
