<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tool extends Model
{
    public $incrementing = true;

    // Campos que se pueden llenar
    protected $fillable = [
        'codigo_qr', 'nombre_herramienta', 'foto', 'ubicacion_herramienta',
        'descripcion_herramienta', 'observacion_herramienta', 'marca_modelo', 'estado_herramienta'
    ];

    public function loans()
    {
        return $this->morphToMany(Loan::class, 'loanable', 'item_loan')
                    ->withPivot('estado_devolucion')
                    ->withTimestamps();
    }
}
