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
        Schema::create('tools', function (Blueprint $table) {
            $table->id();
            $table->string('codigo_qr', 100)->unique()->nullable();
            $table->string('nombre_herramienta', 100);
            $table->string('foto_herramienta', 255)->nullable();
            $table->string('ubicacion_herramienta', 100)->nullable();
            $table->text('descripcion_herramienta')->nullable();
            $table->text('observacion_herramienta')->nullable();

            $table->string('marca_modelo', 200)->nullable();
            $table->integer('cantidad_piezas')->default(1);
            $table->enum('estado_herramienta', [
                'Nuevo',
                'Disponible',
                'Prestado',
                'Dañado',
                'Extraviado',
                'Baja',
            ])->default('Disponible');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tools');
    }
};
