<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Item extends Model
{
    //
    // Definicion de los campos que se pueden llenar masivamente
    protected $fillable = ['nombre_item', 'descripcion_item', 'estado', 'foto'];

    // Relación: Un Item puede ser un Equipo  (Especialización)
    public function equipment(): HasOne
    {
        // hasOne indica que la relación es 1 a 1.
        // Laravel buscará en la tabla 'equipment' una columna 'id' que coincida con esta.
        return $this->hasOne(Equipment::class, 'id');
    }

    // Relación "A traves de": Acceder a accesorios desde el item pasando por Equipo
    public function accessories() {
        return $this->hasManyThrough(
            Accessory::class,
            Equipment::class,
            'id',           // Clave foránea en tabla equipment que apunta a items
            'equipment_id', // Clave foránea en tabla accessories que apunta a equipment
            'id',           // Clave local en tabla items
            'id'            // Clave local en tabla equipment
        );
    }

    // Relación N:M con Préstamos (La tabla pivote que llamamos item_loan)
    public function loans(): BelongsToMany
    {
        return $this->belongsToMany(Loan::class, 'item_loan')
                    ->withPivot('estado_devolucion')
                    ->withTimestamps();
    }
}
