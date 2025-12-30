<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $primaryKey = 'id_teacher'; // Tu llave personalizada
    public $incrementing = false;        // No es auto-incremental
}
