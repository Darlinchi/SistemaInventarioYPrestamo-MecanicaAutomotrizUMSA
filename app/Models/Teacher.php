<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $primaryKey = 'id_teacher'; // Llave personalizada
    public $incrementing = false;        // No es auto-incremental

    public function subjects()
    {
        return $this->belongsToMany(
            Subject::class,
            'subject_teacher', // Tabla pivote
            'teacher_id',      // FK en pivote que apunta a Teacher
            'subject_id'       // FK en pivote que apunta a Subject
        );
    }
}
