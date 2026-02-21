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
        Schema::create('tools', function (Blueprint $table) {
            // El id de esta tabla es al mismo tiempo la llave foránea de la tabla items
            $table->foreignId('id')
                ->primary()
                ->constrained('items')
                ->onDelete('cascade')
                ->onUpdate('cascade');

            $table->string('marca_modelo', 200)->nullable();
            $table->enum('estado_herramienta', [
                'Nuevo',
                'Disponible',
                'Prestado',
                'Dañado',
                'Extraviado',
                'Baja'
            ])->default('Disponible');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tools');
    }
};
