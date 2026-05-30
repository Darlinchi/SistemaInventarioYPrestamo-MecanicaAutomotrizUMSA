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
        Schema::create('equipment', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_qr', 100)->unique()->nullable();
            $table->string('nombre_equipo', 100);
            $table->string('foto_equipo', 255)->nullable();
            $table->string('ubicacion_equipo', 100)->nullable();
            $table->text('descripcion_equipo')->nullable();
            $table->text('observacion_equipo')->nullable();

            // Atributos específicos que nos pediste
            $table->enum('estado_equipo', [
                'Nuevo',
                'Disponible',
                'Prestado',
                'Mantenimiento',
                'Reparado',
                'Dañado',
                'Extraviado',
                'Incompleto',
                'Baja',
            ])->default('Disponible');
            $table->string('color', 50)->nullable();
            $table->string('marca', 100)->nullable();
            $table->string('modelo', 100)->nullable();
            $table->string('serie', 100)->unique()->nullable();
            $table->string('rubro', 100)->nullable();
            $table->date('fecha_adquisicion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('equipment');
    }
};
