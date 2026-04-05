<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $primaryKey = 'id_student'; // Indicamos que la PK no es 'id'
    public $incrementing = false;        // No es autoincremental

    protected $fillable = [
        'id_student',
        'registro_universitario',
        'semestre'
    ];

    // Relación inversa: Un estudiante pertenece a un prestatario
    public function borrower()
    {
        return $this->belongsTo(Borrower::class, 'id_student');
    }
}
