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
        Schema::create('students', function (Blueprint $table) {
            // 1. Definimos la llave primaria que NO es autoincremental
            $table->unsignedBigInteger('id_student')->primary();

            // 2. Relación con la tabla padre 'borrowers'
            $table->foreign('id_student')
                ->references('id')
                ->on('borrowers')
                ->onDelete('cascade');

            // 3. Campos específicos para estudiantes de último año
            $table->string('registro_universitario', 20)->unique();
            // tal vez aqui no
            // $table->integer('semestre'); // Por defecto 10mo semestre

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
