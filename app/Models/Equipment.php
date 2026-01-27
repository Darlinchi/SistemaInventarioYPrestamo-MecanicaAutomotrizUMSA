<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Equipment extends Model
{
    // No se tiene ID auto-incremental propio, sino el del Item:
    protected $primaryKey = 'id';
    public $incrementing = false; // El ID no aumenta solo, lo hereda.

    // Campos que se pueden llenar
    protected $fillable = ['id', 'codigo_qr', 'marca', 'modelo', 'serie', 'ubicacion','color', 'rubro', 'fecha_adquisicion', 'observacion_equipo'];

    // El equipo pertenece a un Item
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'id', 'id');
    }

    // Un equipo tiene muchos accesorios
    public function accessories(): HasMany
    {
        return $this->hasMany(Accessory::class, 'equipment_id', 'id');
    }

    // Relacion de equipo con mantenimientos
    public function maintenances(): HasMany
    {
        return $this->hasMany(Maintenance::class);
    }
}
