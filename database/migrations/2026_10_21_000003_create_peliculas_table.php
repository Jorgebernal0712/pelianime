<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('peliculas', function (Blueprint $table) {
            $table->id();
            $table->string('titulo', 150);
            $table->string('director', 120)->nullable();
            $table->foreignId('genero_id')->constrained('generos')->restrictOnDelete();
            $table->unsignedSmallInteger('anio');
            $table->unsignedSmallInteger('duracion_min')->nullable();
            $table->decimal('calificacion', 3, 1)->default(0); // 0.0 a 10.0
            $table->text('sinopsis')->nullable();
            $table->string('poster')->nullable();          // ruta de la imagen subida
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete(); // quién la registró
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('peliculas');
    }
};
