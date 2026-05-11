<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id(); // Este será nuestro id_materia
            $table->string('sigla', 20)->unique();
            $table->boolean('activo')->default(true);
            $table->string('pensum', 20)->default('Actual');
            $table->string('nombre_materia', 100);
            $table->unsignedInteger('semestre')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subjects');
    }
};
