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
        Schema::create('item_loan', function (Blueprint $table) {
            $table->foreignId('item_id')->constrained('items')->onDelete('cascade');
            $table->foreignId('loan_id')->constrained('loans')->onDelete('cascade');
            
            // Atributo adicional para el control de calidad
            $table->enum('estado_devolucion', [
                'Disponible', 
                'Prestado', 
                'Mantenimiento', 
                'Dañado', 
                'Baja'
            ])->default('Disponible');
            $table->primary(['item_id', 'loan_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_loan');
    }
};
