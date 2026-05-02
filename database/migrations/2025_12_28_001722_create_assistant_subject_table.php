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
        Schema::create('assistant_subject', function (Blueprint $table) {

            $table->unsignedBigInteger('assistant_id');
            $table->unsignedBigInteger('teacher_id');
            $table->unsignedBigInteger('subject_id');

            $table->foreign('assistant_id')
                ->references('id_assistant') // <-- Muy importante
                ->on('assistants')
                ->onDelete('cascade');

            // Referencia a 'id_teacher' en la tabla 'teachers'
            $table->foreign('teacher_id')
                ->references('id_teacher')
                ->on('teachers')
                ->onDelete('cascade');

            // Referencia a 'id' en la tabla 'subjects' (aquí si es 'id' estándar)
            $table->foreign('subject_id')
                ->references('id')
                ->on('subjects')
                ->onDelete('cascade');

            $table->primary(['assistant_id', 'subject_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('assistant_subject');
    }
};
