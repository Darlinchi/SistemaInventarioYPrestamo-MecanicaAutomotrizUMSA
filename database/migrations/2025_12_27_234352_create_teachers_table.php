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
        Schema::create('teachers', function (Blueprint $table) {
            // 1. IMPORTANTE: Usamos unsignedBigInteger en lugar de id() 
            // para que NO sea auto-incremental.
            $table->unsignedBigInteger('id_teacher')->primary();

            // 3. Creamos la relación con la tabla padre (borrowers)
            $table->foreign('id_teacher')
                ->references('id') // El ID de borrowers
                ->on('borrowers')
                ->onDelete('cascade');
            
            // Aquí podrías añadir campos específicos de docentes de la carrera si los hay
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('teachers');
    }
};
