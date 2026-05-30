<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Loan extends Model
{
    use HasFactory;

    protected $table = 'loans';

    // Definicion de los campos que se pueden llenar masivamente
    protected $fillable = [
        'user_id', 'borrower_id', 'subject_id', 'fecha_salida', 'fecha_retorno_prevista',
        'hora_inicio', 'hora_fin_prevista', 'observacion', 'estado_prestamo',
    ];

    // Relacion con quien realizo el prestamo (Staff/User)
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // Relacion con quien recibio  (Docente/Auxiliar)
    public function borrower(): BelongsTo
    {
        return $this->belongsTo(Borrower::class);
        // return $this->belongsTo(Borrower::class, 'borrower_id');
    }

    // Relacion con materia asociada al prestamo
    public function subject(): BelongsTo
    {
        return $this->belongsTo(Subject::class);
        // return $this->belongsTo(Subject::class, 'subject_id');
    }

    public function authorization(): HasOne
    {
        /**
         * Relación 1:1 con la tabla authorizations.
         * El campo 'loan_id' en la tabla authorizations es la llave foránea.
         */
        return $this->hasOne(Authorization::class, 'loan_id');
    }

    /**
     * Ítems (equipos/herramientas) incluidos en este préstamo.
     * PRESTAMO incluye N:M EQUIPO/HERRAMIENTA
     */
    public function itemLoans(): HasMany
    {
        return $this->hasMany(ItemLoan::class, 'loan_id');
    }

    /**
     * Devolución asociada a este préstamo (1:1).
     * Agregación genera → DEVOLUCION
     */
    public function loanReturns(): HasOne
    {
        // Usamos HasMany porque aunque usualmente hay una,
        // permite mayor flexibilidad y evita errores de colección.
        return $this->hasOne(LoanReturn::class, 'loan_id');
    }

    // Relación con Equipos
    public function equipments(): MorphToMany
    {
        return $this->morphedByMany(Equipment::class, 'loanable', 'item_loan');
    }

    // Relación con Herramientas
    public function tools(): MorphToMany
    {
        return $this->morphedByMany(Tool::class, 'loanable', 'item_loan');
    }

    // public function equipments(): MorphToMany {
    //    return $this->morphedByMany(Equipment::class, 'loanable', 'item_loan')
    //                ->withPivot('estado_devolucion')
    //                ->withTimestamps();
    // }

    // public function tools(): MorphToMany  {
    //    return $this->morphedByMany(Tool::class, 'loanable', 'item_loan')
    //                ->withPivot('estado_devolucion')
    //                ->withTimestamps();
    // }

    public function getAllItemsAttribute()
    {
        $equipments = $this->equipments->map(function ($item) {
            return [
                'id' => $item->id,
                'nombre_mostrar' => $item->nombre_equipo, // Ajusta al nombre real de tu columna
                'codigo_qr' => $item->codigo_qr,
                'foto_equipo' => $item->foto_equipo, // <-- CRÍTICO: Asegúrate de que esté aquí
                'foto' => $item->foto_equipo,
                'tipo_personalizado' => 'Equipo',
                'es_equipo' => true,
                'accessories' => $item->accessories, // Por si los necesitas
                'estado_devolucion' => $item->pivot->estado_devolucion,
            ];
        });

        $tools = $this->tools->map(function ($item) {
            return [
                'id' => $item->id,
                'nombre_mostrar' => $item->nombre_herramienta, // Ajusta al nombre real de tu columna
                'foto_herramienta' => $item->foto_herramienta, // <-- CRÍTICO
                'foto' => $item->foto_herramienta,
                'codigo_qr' => $item->codigo_qr,
                'tipo_personalizado' => 'Herramienta',
                'es_equipo' => false,
                'estado_devolucion' => $item->pivot->estado_devolucion,
            ];
        });

        return $equipments->concat($tools);
    }

    // ─── Helpers ──────────────────────────────────────────────────

    /**
     * Construye all_items desde item_loan (para préstamos ACTIVOS).
     * Usado por LoanController@index.
     * Requiere eager-load: equipments.accessories, tools
     */
    public function buildAllItemsFromLoan(): array
    {
        $equipments = $this->equipments->map(fn ($item) => [
            'id' => $item->id,
            'nombre_mostrar' => $item->nombre_equipo,
            'codigo_qr' => $item->codigo_qr,
            'foto_equipo' => $item->foto_equipo,
            'foto' => $item->foto_equipo,
            'tipo_personalizado' => 'Equipo',
            'es_equipo' => true,
            'accessories' => $item->accessories,
            'estado_devolucion' => null, // aún no devuelto
        ]);

        $tools = $this->tools->map(fn ($item) => [
            'id' => $item->id,
            'nombre_mostrar' => $item->nombre_herramienta,
            'foto_herramienta' => $item->foto_herramienta,
            'foto' => $item->foto_herramienta,
            'codigo_qr' => $item->codigo_qr,
            'tipo_personalizado' => 'Herramienta',
            'es_equipo' => false,
            'estado_devolucion' => null,
        ]);

        return $equipments->concat($tools)->values()->toArray();
    }

    /**
     * Construye all_items desde return_details (para DEVOLUCIONES).
     * Usado por LoanReturnController@index.
     * Requiere eager-load: loanReturns.returnDetails.returnable
     */
    public function buildAllItemsFromReturn(): array
    {
        $loanReturn = $this->loanReturns;
        if (! $loanReturn || $loanReturn->returnDetails->isEmpty()) {
            return [];
        }

        return $loanReturn->returnDetails
            ->map(function ($detail) {
                $model = $detail->returnable;
                if (! $model) {
                    return null;
                }

                $esEquipo = str_contains($detail->returnable_type, 'Equipment');

                return [
                    'id' => $model->id,
                    'nombre_mostrar' => $esEquipo ? $model->nombre_equipo : $model->nombre_herramienta,
                    'codigo_qr' => $model->codigo_qr ?? null,
                    'foto' => $esEquipo ? ($model->foto_equipo ?? null) : ($model->foto_herramienta ?? null),
                    'es_equipo' => $esEquipo,
                    'estado_devolucion' => $detail->estado_devolucion,
                ];
            })
            ->filter()
            ->values()
            ->toArray();
    }
}
