<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Assistant extends Model
{
    use HasFactory;

    protected $table = 'assistants';

    protected $primaryKey = 'id_assistant'; // Llave personalizada

    public $incrementing = false;        // No es auto-incremental

    protected $fillable = [
        'id_assistant',
        'categoria',
        'fecha_inicio',
        'fecha_fin',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    public function borrower(): BelongsTo
    {
        return $this->belongsTo(Borrower::class, 'id_assistant', 'id');
    }

    // Relación correcta: assistant_subject → subject_teacher
    public function subjectTeachers()
    {
        return $this->belongsToMany(
            SubjectTeacher::class,
            'assistant_subject',
            'assistant_id',
            'subject_teacher_id'
        )->withTimestamps();
    }

    public function assistantSubjects()
    {
        return $this->belongsToMany(
            SubjectTeacher::class,
            'assistant_subject',
            'assistant_id',
            'subject_teacher_id'
        )->withTimestamps();
    }
}
