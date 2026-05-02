<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Subject extends Model
{
    use HasFactory;

    protected $table = 'subjects';

    protected $fillable = [
        'sigla',
        'nombre_materia',
        'semestre',
    ];

    // Relacion materia/docente
    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class);
    }

    // Relacion materia/prestamo
    public function loans(): HasMany
    {
        //return $this->hasMany(Loan::class, 'id', 'id');
        return $this->hasMany(Loan::class, 'subject_id');
    }
}
