<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Equipment extends Model
{
    //

    // IMPORTANTE: Como no se usa en este caso un ID auto-incremental propio, sino el del Item:
    protected $primaryKey = 'id'; // Le recordamos que el ID no es autoincremental aquí
    public $incrementing = false; // El ID no aumenta solo, lo hereda.

    // Definicion de los campos que se pueden llenar masivamente
    protected $fillable = ['id', 'codigo_qr', 'marca', 'modelo', 'nro_serie', 'observacion', 'color', 'fecha_adquisicion', 'rubro', 'ubicacion'];

    // Relación inversa: El equipo pertenece a un Item
    public function item(): BelongsTo
    {
        // belongsTo conecta al hijo con el padre.
        return $this->belongsTo(Item::class, 'id');
    }

    // Relación directa: Un equipo tiene muchos accesorios
    public function accessories(): HasMany
    {
        return $this->hasMany(Accessory::class, 'equipment_id', 'id');
    }

    public function maintenances()
    {
        return $this->hasMany(Maintenance::class);
    }
}
