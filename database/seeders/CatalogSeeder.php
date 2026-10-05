<?php

namespace Database\Seeders;

use App\Models\Pelicula;
use App\Models\Videojuego;
use Illuminate\Database\Seeder;

class CatalogSeeder extends Seeder
{
    public function run(): void
    {
        // Registros ficticios para probar los formularios y el catálogo.
        foreach (range(1, 12) as $number) {
            Pelicula::updateOrCreate(['titulo' => 'Aventura cinematográfica '.$number], [
                'genero' => ['Aventura', 'Comedia', 'Drama'][$number % 3],
                'plataforma' => ['Cine', 'Streaming', 'Blu-ray'][$number % 3],
                'anio_lanzamiento' => 2010 + $number,
                'calificacion' => 6 + ($number % 5),
                'director' => 'Director de prueba '.$number,
                'actores' => 'Ana Ejemplo, Luis Demostración',
                'productora' => 'Estudio Demo',
                'duracion_minutos' => 90 + $number,
                'clasificacion' => $number % 2 ? 'PG' : 'PG-13',
                'fecha_registro' => now()->toDateString(),
            ]);
            Videojuego::updateOrCreate(['titulo' => 'Mundo virtual '.$number], [
                'genero' => ['Aventura', 'Estrategia', 'Carreras'][$number % 3],
                'plataforma' => ['PC', 'Consola', 'Móvil'][$number % 3],
                'anio_lanzamiento' => 2010 + $number,
                'calificacion' => 6 + ($number % 5),
                'desarrollador' => 'Estudio Interactivo '.$number,
                'jugadores' => $number % 4 + 1,
                'fecha_registro' => now()->toDateString(),
            ]);
        }
    }
}
