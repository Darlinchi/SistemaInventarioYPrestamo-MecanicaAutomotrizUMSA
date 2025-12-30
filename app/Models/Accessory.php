<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Accessory extends Model
{
    //

    public function equipo()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }
}
