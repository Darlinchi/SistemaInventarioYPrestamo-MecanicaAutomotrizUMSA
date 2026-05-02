<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ReturnDetail extends Model
{
    use HasFactory;

    protected $table = 'return_details';

    protected $fillable = [
        'loan_return_id',
        'returnable_id',
        'returnable_type',
        'estado_devolucion',
    ];

    /**
     * Devolución a la que pertenece este detalle.
     * CORREGIDO: LReturn::class → LoanReturn::class
     */
    public function loanReturn(): BelongsTo
    {
        return $this->belongsTo(LoanReturn::class, 'loan_return_id');
    }

    /**
     * El ítem devuelto: Equipment o Tool (polimórfico).
     * returnable_type determina el modelo.
     */
    public function returnable(): MorphTo
    {
        return $this->morphTo();
    }

    public function returnDetailAccessories(): HasMany
    {
        return $this->hasMany(ReturnDetailAccessory::class, 'return_detail_id');
    }

    // ─── Helpers de estado ─────────────────────────────────────────
    public function isDisponible(): bool  { return $this->estado_devolucion === 'Disponible'; }
    public function isDañado(): bool      { return $this->estado_devolucion === 'Dañado'; }
    public function isExtraviado(): bool  { return $this->estado_devolucion === 'Extraviado'; }
    public function isIncompleto(): bool  { return $this->estado_devolucion === 'Incompleto'; }
    public function isBaja(): bool        { return $this->estado_devolucion === 'Baja'; }
    public function tieneProblema(): bool { return !$this->isDisponible(); }

    public function getNombreItemAttribute(): string
    {
        if ($this->returnable instanceof Equipment) {
            return $this->returnable->nombre_equipo;
        }
        if ($this->returnable instanceof Tool) {
            return $this->returnable->nombre_herramienta;
        }
        return 'Ítem desconocido';
    }
}
