<?php

return [
    'predefinidos' => [
        'Administrador' => ['usuarios.ver', 'usuarios.crear', 'usuarios.editar', 'usuarios.eliminar', 'peliculas.ver', 'peliculas.crear', 'peliculas.editar', 'peliculas.eliminar', 'videojuegos.ver', 'videojuegos.crear', 'videojuegos.editar', 'videojuegos.eliminar'],
        'Empleado' => ['peliculas.ver', 'peliculas.crear', 'peliculas.editar', 'videojuegos.ver', 'videojuegos.crear', 'videojuegos.editar'],
        'Cliente' => ['peliculas.ver', 'videojuegos.ver'],
    ],
];
