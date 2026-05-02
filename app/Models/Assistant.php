<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Assistant extends Model
{
    use HasFactory;

    protected $table      = 'assistants';
    protected $primaryKey = 'id_assistant'; // Llave personalizada
    public $incrementing = false;        // No es auto-incremental

    protected $fillable = [
        'id_assistant',
        'registro_universitario',
    ];

    public function borrower(): BelongsTo
    {
        return $this->belongsTo(Borrower::class, 'id_assistant', 'id');
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(
            Teacher::class,
            'assistant_subject',
            'assistant_id',
            'teacher_id',
            'id_assistant',
            'id_teacher'
        )->withPivot('subject_id');
    }

    public function subjects(): BelongsToMany
    {
        return $this->belongsToMany(
            Subject::class,
            'assistant_subject', // Tabla pivote
            'assistant_id',      // FK en pivote que apunta a Assistant
            'subject_id'         // FK en pivote que apunta a Subject
        );
    }
}
