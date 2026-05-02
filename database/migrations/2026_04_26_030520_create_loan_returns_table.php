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
        Schema::create('loan_returns', function (Blueprint $table) {
            $table->id();

            $table->foreignId('loan_id')        // préstamo que se devuelve (1:1)
                ->unique()
                ->constrained('loans')
                ->onDelete('restrict');

            $table->foreignId('user_id')        // encargado que registra la devolución
                ->constrained('users')
                ->onDelete('restrict');

            $table->date('fecha_retorno');
            $table->time('hora_fin');
            $table->text('observacion')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_returns');
    }
};
