<?php

use App\Http\Controllers\PeliculaController;
use App\Http\Controllers\VideojuegoController;

return [
    'usuarios' => ['nombre' => 'Usuarios', 'ruta' => 'admin.usuarios.index', 'acciones' => ['ver', 'crear', 'editar', 'eliminar']],
    'peliculas' => ['nombre' => 'Películas', 'ruta' => 'peliculas.index', 'controlador' => PeliculaController::class, 'acciones' => ['ver', 'crear', 'editar', 'eliminar']],
    'videojuegos' => ['nombre' => 'Videojuegos', 'ruta' => 'videojuegos.index', 'controlador' => VideojuegoController::class, 'acciones' => ['ver', 'crear', 'editar', 'eliminar']],
];
