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
        Schema::create('items', function (Blueprint $table) {
            $table->id();// Representa tu id_item
            $table->string('nombre_item', 100);
            $table->string('foto', 255)->nullable();
            
            // Usamos el ENUM tal cual lo diseñaste
            $table->enum('estado', [
                'Disponible', 
                'Prestado', 
                'Mantenimiento', 
                'Dañado', 
                'Baja'
            ])->default('Disponible');
            
            $table->text('descripcion_item')->nullable();
            $table->timestamps(); // Esto añade created_at y updated_at automáticamente
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
