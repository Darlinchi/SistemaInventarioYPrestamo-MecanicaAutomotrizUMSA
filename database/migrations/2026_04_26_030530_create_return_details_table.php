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
        Schema::create('return_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_return_id')
                ->constrained('loan_returns')
                ->onDelete('cascade');

            // Polimórfico: Equipment o Tool (mismo que en loan_items)
            $table->morphs('returnable'); // returnable_id + returnable_type
            // $table->unsignedInteger('cantidad')->default(1);
            $table->enum('estado_devolucion', [
                'Disponible',
                'Dañado',
                'Extraviado',
                'Incompleto',
                'Baja',
            ])->default('Disponible');

            // $table->text('observacion_devolucion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('return_details');
    }
};
