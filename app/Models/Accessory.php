<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Accessory extends Model
{
    protected $fillable = ['equipment_id', 'nombre_accesorio', 'estado_accesorio'];

    // Un equipo tiene uno o muchos accesorios
    public function accessories() {
        return $this->hasMany(Accessory::class, 'equipment_id', 'id');
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }
}
