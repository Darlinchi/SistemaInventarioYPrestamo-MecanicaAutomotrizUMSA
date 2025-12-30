<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assistant extends Model
{
    //

    protected $primaryKey = 'id_assistant'; // Tu llave personalizada
    public $incrementing = false;        // No es auto-incremental
}
