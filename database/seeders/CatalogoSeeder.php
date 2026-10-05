<?php

namespace Database\Seeders;

use App\Models\Pelicula;
use App\Models\Videojuego;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogoSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            foreach (['peliculas' => Pelicula::class, 'videojuegos' => Videojuego::class] as $file => $model) {
                $records = json_decode(file_get_contents(database_path('data/'.$file.'.json')), true, 512, JSON_THROW_ON_ERROR);
                foreach ($records as $record) {
                    $record['fecha_registro'] = now(config('app.display_timezone'))->toDateString();
                    if ($file === 'peliculas') {
                        $record += ['plataforma' => null, 'productora' => null, 'clasificacion' => null];
                    }
                    // Repetir la carga conserva las ediciones e IDs de títulos ya registrados.
                    $model::firstOrCreate(['titulo' => $record['titulo']], $record);
                }
            }
        });
    }
}
