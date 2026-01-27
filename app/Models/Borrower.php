<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Borrower extends Model
{
    //Relación con el Auxiliar
    public function assistant(): HasOne
    {
        // El segundo parámetro es la llave foránea en la tabla assistants
        return $this->hasOne(Assistant::class, 'id_assistant');
    }

    //Relación con el Docente
    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class, 'id_teacher');
    }

    // Un prestamista tiene muchos prestamos
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class, 'id', 'id');
    }

}
