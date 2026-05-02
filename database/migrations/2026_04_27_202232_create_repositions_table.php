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
        Schema::create('repositions', function (Blueprint $table) {
            $table->id();

            // ── Origen de la reposición ─────────────────────────────────────────
            // Puede nacer de:
            //   - return_details        (equipo o herramienta dañado/extraviado)
            //   - return_detail_accessories (accesorio dañado/extraviado)
            // Usamos morfológico para cubrir ambos casos
            // originable_id + originable_type
            $table->nullableMorphs('originable');

            // Quién registra el acuerdo (encargado del sistema)
            $table->foreignId('user_id')
                ->constrained('users')
                ->onDelete('restrict');

            // Quién debe reponer (el solicitante del préstamo original)
            $table->foreignId('borrower_id')
                ->constrained('borrowers')
                ->onDelete('restrict');

            // Tipo de reposición acordada
            $table->enum('tipo_reposicion', [
                'Reparacion',   // lo manda a arreglar
                'Reemplazo',       // trae el mismo ítem repuesto
                'Desbloqueo',    // paga en dinero
            ]);

            // ── Si es Reemplazo: referencia al ítem nuevo creado en el sistema ──
            // ✅ CORREGIDO: usando nullableMorphs en vez de campos sueltos
            // nuevo_item_id + nuevo_item_type
            // Puede apuntar a: Equipment, Tool o Accessory
            $table->nullableMorphs('nuevo_item');

            // Estado del proceso de reposición
            $table->enum('estado', [
                'Pendiente',    // acuerdo creado, sin cumplir aún
                'Cumplida',     // reposición completada
                'Incumplida',   // venció el plazo sin cumplir
            ])->default('Pendiente');

            $table->date('fecha_limite')->nullable();        // plazo para cumplir
            $table->date('fecha_cumplimiento')->nullable(); // cuándo se cumplió
            $table->text('observacion')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repositions');
    }
};
