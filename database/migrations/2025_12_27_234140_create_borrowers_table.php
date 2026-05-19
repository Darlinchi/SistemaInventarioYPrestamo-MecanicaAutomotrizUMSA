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
        Schema::create('borrowers', function (Blueprint $table) {
            $table->id(); // Este será el id_prestario
            $table->string('cedula_identidad', 20)->unique();
            $table->string('nombres', 100);
            $table->string('apellidoPaterno', 100)->nullable();
            $table->string('apellidoMaterno', 100)->nullable();
            $table->string('celular', 20)->nullable();
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('borrowers');
    }
};
