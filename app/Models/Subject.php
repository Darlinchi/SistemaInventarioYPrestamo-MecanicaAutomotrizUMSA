<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    // Relacion materia/docente
    public function teachers(): BelongsTo
    {
        return $this->belongsToMany(Teacher::class);
    }

    // Relacion materia/prestamo
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class, 'id', 'id');
    }
}
