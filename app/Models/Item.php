<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    //

    public function equipo()
    {
        // Un Item "puede" tener detalles de equipo
        return $this->hasOne(Equipment::class, 'id');
    }
}
