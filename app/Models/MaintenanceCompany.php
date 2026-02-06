<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MaintenanceCompany extends Model
{
    // Definicion de los campos que se pueden llenar masivamente
    protected $fillable = [
        'id', 'nombre_empresa', 'telefono',
        'descripcion_empresa', 'direccion'
    ];

    public function maintenances()
    {
        return $this->belongsToMany(Maintenance::class, 'maintenance_maintenance_company');
    }
}
