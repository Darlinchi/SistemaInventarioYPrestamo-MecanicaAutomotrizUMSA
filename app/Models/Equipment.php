<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Equipment extends Model
{
    //

    protected $primaryKey = 'id'; // Le recordamos que el ID no es autoincremental aquí
    public $incrementing = false;

    public function item()
    {
        return $this->belongsTo(Item::class, 'id');
    }

    public function accesorios()
    {
        return $this->hasMany(Accessory::class, 'equipment_id');
    }

    public function mantenimientos()
    {
        return $this->hasMany(Maintenance::class);
    }
}
