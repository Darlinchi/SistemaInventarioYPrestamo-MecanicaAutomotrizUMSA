<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Maintenance extends Model
{
    use HasFactory;

    protected $table = 'maintenances';

    protected $fillable = [
        'equipment_id',
        'user_id',
        'tipo_mantenimiento',
        'fecha_proximo_mantenimiento',
        'fecha_mantenimiento',
        'fecha_retorno',
        'fecha_retorno_estimado',
        'hora_inicio',
        'hora_fin',
        'hora_fin_estimado',
        'actividad',
        'estado_final_equipo',
        'estado_mantenimiento',
    ];


    protected $appends = ['estado_texto'];

    public function getEstadoTextoAttribute()
    {
        // Si no hay hora_fin, el trabajo sigue en curso
        return is_null($this->hora_fin) ? 'En Proceso' : 'Completado';
    }

    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(MaintenanceCompany::class, 'maintenance_maintenance_company');
    }

    public function equipment(): BelongsTo
    {
        return $this->belongsTo(Equipment::class);
    }

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    // ─── Helpers ───────────────────────────────────────────────────
    public function isEnProceso(): bool { return $this->estado_mantenimiento === 'En Proceso'; }
    public function isCompletado(): bool{ return $this->estado_mantenimiento === 'Completado'; }
}
