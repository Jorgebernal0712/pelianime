<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('animes', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 150);
            $table->string('estudio', 120)->nullable();
            $table->foreignId('genero_id')->constrained('generos')->restrictOnDelete();
            $table->unsignedSmallInteger('anio');
            $table->unsignedSmallInteger('episodios')->default(1);
            $table->enum('estado', ['En emisión', 'Finalizado', 'Próximamente'])->default('Finalizado');
            $table->decimal('calificacion', 3, 1)->default(0);
            $table->text('sinopsis')->nullable();
            $table->string('imagen')->nullable();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('animes');
    }
};
