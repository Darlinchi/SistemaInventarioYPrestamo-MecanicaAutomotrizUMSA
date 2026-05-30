<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Student extends Model
{
    use HasFactory;

    protected $table = 'students';

    protected $primaryKey = 'id_student'; // Indicamos que la PK no es 'id'

    public $incrementing = false;        // No es autoincremental

    protected $fillable = [
        'id_student',
        'registro_universitario',
        'semestre',
    ];

    // Relación inversa: Un estudiante pertenece a un prestatario
    public function borrower(): BelongsTo
    {
        return $this->belongsTo(Borrower::class, 'id_student');
    }
}
