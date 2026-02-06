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
        Schema::create('maintenances', function (Blueprint $table) {
            $table->id();// id_mantenimiento

            // Relación con el Equipo (Equipment)
            $table->foreignId('equipment_id')
                ->constrained('equipment')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            // Atributos de tiempo y actividad
            $table->date('fecha_mantenimiento');
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin')->nullable();
            $table->text('actividad');
            $table->enum('estado_mantenimiento', ['En Proceso', 'Completado'])->default('En Proceso');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenances');
    }
};
