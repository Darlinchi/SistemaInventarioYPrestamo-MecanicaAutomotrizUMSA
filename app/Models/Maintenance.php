<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    //

    protected $fillable = [
        'equipment_id',
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

    public function companies()
    {
        return $this->belongsToMany(MaintenanceCompany::class, 'maintenance_maintenance_company');
    }

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
}
