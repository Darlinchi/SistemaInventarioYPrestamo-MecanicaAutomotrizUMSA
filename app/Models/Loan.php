<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;

class Loan extends Model
{
    // Definicion de los campos que se pueden llenar masivamente
    protected $fillable = [
        'user_id', 'borrower_id', 'subject_id', 'fecha_salida', 'fecha_retorno', 'fecha_retorno_prevista',
        'hora_inicio', 'hora_fin_prevista', 'hora_fin', 'observacion', 'estado_prestamo'
    ];

    protected $appends = ['all_items'];

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

    public function equipments(): MorphToMany {
        return $this->morphedByMany(Equipment::class, 'loanable', 'item_loan')
                    ->withPivot('estado_devolucion')
                    ->withTimestamps();
    }

    public function tools(): MorphToMany  {
        return $this->morphedByMany(Tool::class, 'loanable', 'item_loan')
                    ->withPivot('estado_devolucion')
                    ->withTimestamps();
    }

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
}
