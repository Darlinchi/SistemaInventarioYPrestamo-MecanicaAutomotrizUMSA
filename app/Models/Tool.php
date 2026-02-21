<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tool extends Model
{
    // No se tiene ID auto-incremental propio, sino el del Item:
    protected $primaryKey = 'id';
    public $incrementing = false; // El ID no aumenta solo, lo hereda.

    // Campos que se pueden llenar
    protected $fillable = ['id', 'marca_modelo', 'estado_herramienta'];

    // El equipo pertenece a un Item
    public function item(): BelongsTo
    {
        return $this->belongsTo(Item::class, 'id', 'id');
    }
}
