<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    protected $table = 'staff'; // Especificacion del nombre de la tabla
    protected $fillable = ['user_id', 'horario_manana', 'horario_tarde'];

    // No tiene un campo 'id' autoincremental propio
    protected $primaryKey = 'user_id';
    public $incrementing = false;
}
