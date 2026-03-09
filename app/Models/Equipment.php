<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Equipment extends Model
{
    protected $table = 'equipment';

    public $incrementing = true; // El ID no aumenta solo, lo hereda.

    protected $casts = [
        'fecha_adquisicion' => 'date',
    ];

    // Campos que se pueden llenar
    protected $fillable = [
        'codigo_qr', 'nombre_equipo', 'foto', 'ubicacion_equipo',
        'descripcion_equipo', 'observacion_equipo', 'estado_equipo',
        'color', 'marca', 'modelo', 'serie', 'rubro', 'fecha_adquisicion'
    ];

    // El equipo pertenece a un Item
    // public function item(): BelongsTo
    // {
    //     return $this->belongsTo(Item::class, 'id', 'id');
    // }

    public function loans()
    {
        return $this->morphToMany(Loan::class, 'loanable', 'item_loan')
                    ->withPivot('estado_devolucion')
                    ->withTimestamps();
    }

    // Un equipo tiene muchos accesorios
    // public function accessories(): HasMany
    // {
    //     return $this->hasMany(Accessory::class, 'equipment_id', 'id');
    // }

    public function accessories(): HasMany
    {
        return $this->hasMany(Accessory::class, 'equipment_id');
    }

    // Relacion de equipo con mantenimientos
    public function maintenances()
    {
        return $this->hasMany(Maintenance::class, 'equipment_id');
    }
}
