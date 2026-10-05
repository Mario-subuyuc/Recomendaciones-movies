<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('consumo_tokens', function (Blueprint $table) {
            $table->unsignedBigInteger('tokens_reales_entrada')->nullable();
            $table->unsignedBigInteger('tokens_reales_salida')->nullable();
            $table->unsignedBigInteger('tokens_reales')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('consumo_tokens', function (Blueprint $table) {
            $table->dropColumn(['tokens_reales_entrada', 'tokens_reales_salida', 'tokens_reales']);
        });
    }
};
