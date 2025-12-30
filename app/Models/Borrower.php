<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Borrower extends Model
{
    //

    // En el modelo Borrower.php
    public function docente() {
        return $this->hasOne(Teacher::class, 'id_teacher');
    }

    public function auxiliar() {
        return $this->hasOne(Assistant::class, 'id_assistant');
    }
}
