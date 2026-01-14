<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assistant extends Model
{
    protected $primaryKey = 'id_assistant'; // Llave personalizada
    public $incrementing = false;        // No es auto-incremental

    public function subjects()
    {
        return $this->belongsToMany(
            Subject::class,
            'assistant_subject', // Tabla pivote
            'assistant_id',      // FK en pivote que apunta a Assistant
            'subject_id'         // FK en pivote que apunta a Subject
        );
    }
}
