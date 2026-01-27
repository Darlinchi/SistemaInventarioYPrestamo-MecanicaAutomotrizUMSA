<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Loan extends Model
{
    // Definicion de los campos que se pueden llenar masivamente
    protected $fillable = [
        'user_id', 'borrower_id', 'subject_id',
        'fecha_prestamo', 'hora_inicio', 'hora_fin',
        'observacion', 'estado_prestamo'
    ];

    // Relacion con quien realizo el prestamo (Staff/User)
    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    // Relacion con quien recibio  (Docente/Auxiliar)
    public function borrower(): BelongsTo {
        return $this->belongsTo(Borrower::class);
        //return $this->belongsTo(Borrower::class, 'borrower_id');
    }

    // Relacion con materia asociada al prestamo
    public function subject(): BelongsTo {
        return $this->belongsTo(Subject::class);
        //return $this->belongsTo(Subject::class, 'subject_id');
    }

    // Relacion con items prestados (Muchos a muchos)
    public function items(): BelongsToMany {
        return $this->belongsToMany(Item::class, 'item_loan')
                    ->withPivot('estado_devolucion')
                    ->withTimestamps();
    }

}
