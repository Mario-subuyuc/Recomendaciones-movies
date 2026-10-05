<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('conversaciones', function (Blueprint $table) {
            $table->id('id_conversacion');
            $table->foreignId('id_usuario')->constrained('users')->cascadeOnDelete();
            $table->string('categoria', 20);
            $table->uuid('consulta_uuid')->unique();
            $table->timestamp('fecha_creacion');
            $table->index(['id_usuario', 'fecha_creacion']);
        });
        Schema::create('mensajes', function (Blueprint $table) {
            $table->id('id_mensaje');
            $table->unsignedBigInteger('id_conversacion');
            $table->foreign('id_conversacion')->references('id_conversacion')->on('conversaciones')->cascadeOnDelete();
            $table->string('rol', 20);
            $table->text('contenido');
            $table->timestamp('fecha');
            $table->unique(['id_conversacion', 'rol']);
        });
        Schema::create('consumo_tokens', function (Blueprint $table) {
            $table->id('id_consumo');
            $table->foreignId('id_usuario')->constrained('users')->cascadeOnDelete();
            $table->unsignedBigInteger('id_conversacion')->nullable();
            $table->foreign('id_conversacion')->references('id_conversacion')->on('conversaciones')->nullOnDelete();
            $table->uuid('consulta_uuid');
            $table->string('categoria', 20);
            $table->string('etapa', 20);
            $table->unsignedInteger('tokens_entrada');
            $table->unsignedInteger('tokens_salida');
            $table->unsignedInteger('tokens');
            $table->timestamp('fecha');
            $table->unique(['consulta_uuid', 'etapa']);
            $table->index(['id_usuario', 'categoria', 'fecha']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consumo_tokens');
        Schema::dropIfExists('mensajes');
        Schema::dropIfExists('conversaciones');
    }
};
