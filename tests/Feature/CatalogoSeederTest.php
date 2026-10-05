<?php

namespace Tests\Feature;

use App\Models\Pelicula;
use App\Models\Videojuego;
use Database\Seeders\CatalogoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CatalogoSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_import_preserves_source_data_and_existing_edits_without_duplicates(): void
    {
        $this->seed(CatalogoSeeder::class);
        $this->assertSame(50, Pelicula::count());
        $this->assertSame(69, Videojuego::count());
        $minecraft = Videojuego::where('titulo', 'Minecraft')->firstOrFail();
        $this->assertSame('1-8+', $minecraft->jugadores);
        $movie = Pelicula::firstOrFail();
        $this->assertNull($movie->plataforma);
        $this->assertNull($movie->productora);
        $this->assertNull($movie->clasificacion);
        $movie->update(['productora' => 'Edición local']);
        $this->seed(CatalogoSeeder::class);
        $this->assertSame(50, Pelicula::count());
        $this->assertSame(69, Videojuego::count());
        $this->assertSame('Edición local', $movie->fresh()->productora);
        $this->assertSame($minecraft->getKey(), Videojuego::where('titulo', 'Minecraft')->firstOrFail()->getKey());
    }
}
