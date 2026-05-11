<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssistantSubject extends Model
{
    protected $table = 'assistant_subject';

    protected $fillable = ['assistant_id', 'subject_teacher_id'];

    public function subjectTeacher()
    {
        return $this->belongsTo(SubjectTeacher::class, 'subject_teacher_id');
    }
}
