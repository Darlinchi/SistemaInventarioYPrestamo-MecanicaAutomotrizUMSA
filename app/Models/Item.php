<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Item extends Model
{
    // Definicion de los campos que se pueden llenar masivamente
    protected $fillable = ['nombre_item', 'descripcion_item', 'estado', 'foto'];

    // Un Item puede ser un Equipo
    public function equipment(): HasOne
    {
        // La relación es 1 a 1
        return $this->hasOne(Equipment::class, 'id', 'id');
    }

    // Acceder a accesorios desde el item pasando por Equipo
    public function accessories() {
        return $this->hasManyThrough(
            Accessory::class,
            Equipment::class,
            'id',           // Clave foranea en tabla equipment que apunta a items
            'equipment_id', // Clave foranea en tabla accessories que apunta a equipment
            'id',           // Clave local en tabla items
            'id'            // Clave local en tabla equipment
        );
    }

    // Relación N:M con Prestamos
    public function loans(): BelongsToMany
    {
        return $this->belongsToMany(Loan::class, 'item_loan')
                    ->withPivot('estado_devolucion')
                    ->withTimestamps();
    }
}
