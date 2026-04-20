<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**Schema::create('item_loan', function (Blueprint $table) {
            $table->foreignId('loan_id')->constrained('loans')->onDelete('cascade');
            // Campos polimórficos
            // loanable_id: guardará el ID del equipo o herramienta
            // loanable_type: guardará el modelo (App\Models\Equipment o App\Models\Tool)
            $table->morphs('loanable');

            $table->enum('estado_devolucion', [
                'Prestado',
                'Disponible',
                'Dañado',
                'Extraviado',
                'Incompleto',
                'Baja'
            ])->default('Prestado');
            $table->timestamps();
        });
     * Run the migrations.
     */
    public function up(): void
    {

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_loan');
    }
};
