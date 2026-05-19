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
            $table->id();
            // Relación con el Equipo (Equipment)
            $table->foreignId('equipment_id')
                ->constrained('equipment')
                ->onDelete('cascade')
                ->onUpdate('cascade'); 

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->onDelete('restrict');

            // Clasificación del mantenimiento
            $table->enum('tipo_mantenimiento', ['Preventivo', 'Correctivo'])
                ->default('Preventivo');

            // Atributos de tiempo y actividad
            $table->date('fecha_proximo_mantenimiento')->nullable();
            $table->date('fecha_mantenimiento');
            $table->date('fecha_retorno')->nullable();
            $table->date('fecha_retorno_estimado')->nullable();
            $table->time('hora_inicio')->nullable();
            $table->time('hora_fin_estimado')->nullable();
            $table->time('hora_fin')->nullable();
            $table->text('actividad')->nullable();
            $table->enum('estado_mantenimiento', ['En Proceso', 'Completado'])->default('En Proceso');
            $table->enum('estado_final_equipo', [
                'Disponible',
                'Mantenimiento',
                'Reparado',
                'Dañado',
                'Incompleto',
                'Baja'
            ])->nullable();
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
