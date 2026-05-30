<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipment extends Model
{
    use HasFactory;

    protected $table = 'equipment';

    // public $incrementing = true; // El ID no aumenta solo, lo hereda.

    protected $casts = [
        'fecha_adquisicion' => 'date',
    ];

    // Campos que se pueden llenar
    protected $fillable = [
        'codigo_qr', 'nombre_equipo', 'foto_equipo', 'ubicacion_equipo',
        'descripcion_equipo', 'observacion_equipo', 'estado_equipo',
        'color', 'marca', 'modelo', 'serie', 'rubro', 'fecha_adquisicion',
    ];

    public function accessories(): HasMany
    {
        return $this->hasMany(Accessory::class, 'equipment_id');
    }

    // Relacion de equipo con mantenimientos
    public function maintenances(): HasMany
    {
        return $this->hasMany(Maintenance::class, 'equipment_id');
    }

    public function loans()
    {
        // return $this->morphToMany(Loan::class, 'loanable', 'item_loan')
        //            ->withPivot('estado_devolucion')
        //            ->withTimestamps();
        return $this->morphMany(Loan::class, 'loanable', 'item_loan');
    }

    /**
     * Ítems de devolución donde aparece este equipo.
     * Relación polimórfica inversa: Equipment como returnable
     */
    public function returnDetails()
    {
        return $this->morphMany(ReturnDetail::class, 'returnable');
    }

    // ─── Helper: verifica si está disponible ──────────────────────
    public function isDisponible(): bool
    {
        return $this->estado_equipo === 'Disponible';
    }
}
