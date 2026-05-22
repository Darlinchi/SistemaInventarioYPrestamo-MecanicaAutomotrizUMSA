<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubjectTeacher extends Model
{
    protected $table = 'subject_teacher';

    protected $fillable = ['teacher_id', 'subject_id', 'paralelo'];

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class, 'teacher_id', 'id_teacher');
    }

    public function assistants()
    {
        return $this->belongsToMany(
            \App\Models\Assistant::class,
            'assistant_subject',
            'subject_teacher_id',
            'assistant_id'
        );
    }
}
