<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Teacher extends Model
{
    use HasFactory;

    protected $table    = 'teachers';
    protected $fillable = ['id_teacher'];
    protected $primaryKey = 'id_teacher'; // Llave personalizada
    public $incrementing = false;        // No es auto-incremental

    public function borrower(): BelongsTo
    {
        return $this->belongsTo(Borrower::class, 'id_teacher', 'id');
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(
            Subject::class,
            'subject_teacher',
            'teacher_id',
            'subject_id'
        )->withPivot('paralelo')->withTimestamps();
    }

    public function subjectTeachers(): HasMany
    {
        return $this->hasMany(SubjectTeacher::class, 'teacher_id', 'id_teacher');
    }

    public function assistants()
    {
        return $this->belongsToMany(
            Assistant::class,
            'assistant_subject',
            'teacher_id',
            'assistant_id',
            'id_teacher',
            'id_assistant'
        )->withPivot('subject_id');
    }
}
