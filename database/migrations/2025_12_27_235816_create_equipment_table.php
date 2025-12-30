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
            // El id de esta tabla es al mismo tiempo la llave foránea de la tabla items
            $table->foreignId('id')
                ->primary()
                ->constrained('items')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            // Atributos específicos que nos pediste
            $table->string('codigo_qr', 100)->unique()->nullable();
            $table->string('ubicacion', 100)->nullable();
            $table->string('color', 50)->nullable();
            $table->string('marca', 100)->nullable();
            $table->string('modelo', 100)->nullable();
            $table->string('serie', 100)->unique()->nullable();
            $table->string('rubro', 100)->nullable();
            $table->date('fecha_adquisicion')->nullable();
            $table->text('observacion_equipo')->nullable();
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
