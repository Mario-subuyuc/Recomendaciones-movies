<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('videojuegos', fn (Blueprint $table) => $table->string('jugadores', 50)->change());
        Schema::table('peliculas', function (Blueprint $table) {
            foreach (['plataforma', 'productora', 'clasificacion'] as $field) {
                $table->string($field)->nullable()->change();
            }
        });
    }

    public function down(): void
    {
        // No reducir tipos: se perderían rangos de jugadores y campos sin datos.
    }
};
