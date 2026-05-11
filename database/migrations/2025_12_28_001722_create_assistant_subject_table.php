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
            $table->id();
            $table->unsignedBigInteger('assistant_id');
            $table->unsignedBigInteger('subject_teacher_id');

            $table->foreign('assistant_id')
                ->references('id_assistant') // <-- Muy importante
                ->on('assistants')
                ->onDelete('cascade');

            $table->foreign('subject_teacher_id')->references('id')->on('subject_teacher')->onDelete('cascade');

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
