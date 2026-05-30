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
        Schema::create('accessories', function (Blueprint $table) {
            $table->id(); // Tu id_accesorio
            // Relación con la tabla de equipos
            $table->foreignId('equipment_id')
                ->constrained('equipment')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->string('nombre_accesorio', 100);
            $table->string('foto_accesorio', 255)->nullable();
            $table->enum('estado_accesorio', [
                'Bueno',
                'Dañado',
                'Extraviado',
            ])->default('Bueno');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('accessories');
    }
};
