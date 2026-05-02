<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Tool extends Model
{

    use HasFactory;

    protected $table = 'tools';

    //public $incrementing = true;

    // Campos que se pueden llenar
    protected $fillable = [
        'codigo_qr', 'nombre_herramienta', 'foto_herramienta', 'ubicacion_herramienta', 'cantidad_piezas',
        'descripcion_herramienta', 'observacion_herramienta', 'marca_modelo', 'estado_herramienta'
    ];

    public function loans()
    {
        //return $this->morphToMany(Loan::class, 'loanable', 'item_loan')
        //            ->withPivot('estado_devolucion')
        //            ->withTimestamps();
        return $this->morphMany(Loan::class, 'loanable', 'item_loan');
    }

    /**
     * Ítems de devolución donde aparece esta herramienta.
     * Relación polimórfica inversa: Tool como returnable
     */
    public function returnDetails()
    {
        return $this->morphMany(ReturnDetail::class, 'returnable');
    }

    public function isDisponible(): bool
    {
        return $this->estado_herramienta === 'Disponible';
    }
}
