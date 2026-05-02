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
        Schema::create('assistants', function (Blueprint $table) {
            $table->unsignedBigInteger('id_assistant')->primary();
            $table->foreign('id_assistant')
                ->references('id')->on('borrowers')
                ->onDelete('cascade');

            $table->string('registro_universitario', 20)->unique();
            // Aquí podrías añadir campos específicos de auxiliares
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assistants');
    }
};
