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
        Schema::create('loans', function (Blueprint $table) {
            $table->id();// id_prestamo

            // Quién entrega (El usuario logueado en el sistema)
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('restrict');

            // Quién recibe (Docente o Auxiliar de la tabla borrowers)
            $table->foreignId('borrower_id')
                ->constrained('borrowers')
                ->onDelete('restrict');

            // Para qué materia
            $table->foreignId('subject_id')->nullable()
                ->constrained('subjects')
                ->onDelete('restrict');

            // Datos del préstamo
            $table->date('fecha_salida');
            //$table->date('fecha_retorno')->nullable();
            $table->date('fecha_retorno_prevista')->nullable();
            $table->time('hora_inicio');
            $table->time('hora_fin_prevista');
            //$table->time('hora_fin')->nullable();
            //$table->text('observacion')->nullable();

            // Estado del préstamo (opcional pero muy útil)
            $table->enum('estado_prestamo', ['Activo', 'Devuelto', 'Vencido'])
                ->default('Activo');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};
