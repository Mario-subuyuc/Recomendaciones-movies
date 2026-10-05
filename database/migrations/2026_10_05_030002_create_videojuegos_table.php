<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('videojuegos', function (Blueprint $table) {
            $table->id('id_videojuego');
            $table->string('titulo');
            $table->string('genero');
            $table->string('plataforma');
            $table->unsignedSmallInteger('anio_lanzamiento');
            $table->decimal('calificacion', 3, 1);
            $table->string('desarrollador');
            $table->unsignedInteger('jugadores');
            $table->date('fecha_registro');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('videojuegos');
    }
};
