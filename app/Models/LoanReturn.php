<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LoanReturn extends Model
{
    use HasFactory;

    protected $table = 'loan_returns';

    // Definicion de los campos que se pueden llenar masivamente
    protected $fillable = [
        'loan_id' , 'user_id', 'fecha_retorno', 'hora_fin', 'observacion'
    ];

    protected function casts(): array
    {
        return [
            'fecha_retorno' => 'date',
        ];
    }

    /**
     * Préstamo que originó esta devolución (1:1 inverso).
     * genera ← PRESTAMO
     */
    public function loan(): BelongsTo
    {
        return $this->belongsTo(Loan::class, 'loan_id');
    }

    /**
     * Usuario (encargado) que registró esta devolución.
     * USUARIO registra → DEVOLUCION
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Detalles por ítem de esta devolución. (1:N)
     * DEVOLUCION corresponde N DETALLES
     */
    public function returnDetails(): HasMany
    {
        return $this->hasMany(ReturnDetail::class, 'loan_return_id');
    }

    // ─── Helpers ───────────────────────────────────────────────────

    /**
     * Verifica si todos los ítems fueron devueltos en buen estado.
     */
    public function todosDisponibles(): bool
    {
        return $this->returnDetails()
            ->where('estado_devolucion', '!=', 'Disponible')
            ->doesntExist();
    }

    /**
     * Devuelve los ítems que llegaron dañados o extraviados.
     */
    public function itemsConProblema()
    {
        return $this->returnDetails()
            ->whereIn('estado_devolucion', ['Dañado', 'Extraviado', 'Incompleto', 'Baja'])
            ->get();
    }
}
