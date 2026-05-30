<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Authorization extends Model
{
    // Definicion de los campos que se pueden llenar masivamente
    protected $fillable = ['loan_id', 'motivo', 'archivo_nota'];

    /**
     * Préstamo al que corresponde esta nota. (1:1 inverso)
     */
    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class);
    }

    // ─── Helpers ───────────────────────────────────────────────────

    /**
     * URL pública del archivo escaneado guardado en storage.
     */
    public function getArchivoUrlAttribute(): ?string
    {
        return $this->archivo_nota
            ? asset('storage/'.$this->archivo_nota)
            : null;
    }
}
