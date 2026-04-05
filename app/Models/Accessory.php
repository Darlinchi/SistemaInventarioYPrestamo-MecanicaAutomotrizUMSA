<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Accessory extends Model
{
    protected $fillable = [
        'equipment_id',
        'nombre_accesorio',
        'foto_accesorio',
        'estado_accesorio',
    ];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
}
