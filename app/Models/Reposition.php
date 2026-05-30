<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Reposition extends Model
{
    use HasFactory;

    protected $table = 'repositions';

    protected $fillable = [
        'originable_id',
        'originable_type',
        'user_id',
        'borrower_id',
        'tipo_reposicion',
        'nuevo_item_id',
        'nuevo_item_type',
        'estado',
        'fecha_limite',
        'fecha_cumplimiento',
        'observacion',
    ];

    protected function casts(): array
    {
        return [
            'fecha_limite' => 'date',
            'fecha_cumplimiento' => 'date',
        ];
    }

    /**
     * El ítem/accesorio que origina la reposición.
     * Puede ser ReturnDetail o ReturnDetailAccessory.
     */
    public function originable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * El nuevo ítem registrado (solo para tipo Reemplazo).
     * Puede ser Equipment, Tool o Accessory.
     */
    public function nuevoItem(): MorphTo
    {
        return $this->morphTo('nuevo_item');
    }

    /** Encargado que registró el acuerdo */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /** Prestatario que debe reponer */
    public function borrower(): BelongsTo
    {
        return $this->belongsTo(Borrower::class);
    }

    // ─── Helpers ──────────────────────────────────────────────────
    public function isPendiente(): bool
    {
        return $this->estado === 'Pendiente';
    }

    public function isCumplida(): bool
    {
        return $this->estado === 'Cumplida';
    }

    public function isIncumplida(): bool
    {
        return $this->estado === 'Incumplida';
    }

    public function estaVencida(): bool
    {
        return $this->isPendiente()
            && $this->fecha_limite
            && $this->fecha_limite->isPast();
    }
}
