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
        Schema::create('authorizations', function (Blueprint $table) {
            $table->id();

            // 1:1 con loans — solo préstamos de estudiantes tienen nota
            $table->foreignId('loan_id')
                ->unique()              // garantiza 1:1
                ->constrained('loans')
                ->onDelete('cascade');  // si se borra el préstamo, se borra la nota

            // Datos de la nota oficial de dirección
            // $table->string('numero_nota', 50)->unique();  // Nro. oficial
            // $table->date('fecha_nota');                   // fecha que firmó dirección
            // $table->string('emitida_por', 150);           // nombre del director/autoridad
            $table->string('motivo', 200)->nullable();    // para qué proyecto/materia
            $table->string('archivo_nota', 255)->nullable(); // ruta PDF escaneado en storage

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('authorizations');
    }
};
