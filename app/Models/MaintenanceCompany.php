<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class MaintenanceCompany extends Model
{
    // Definicion de los campos que se pueden llenar masivamente
    protected $fillable = [
        'id', 'nombre_empresa', 'telefono',
        'descripcion_empresa', 'direccion'
    ];

    public function maintenances(): BelongsToMany
    {
        return $this->belongsToMany(Maintenance::class, 'maintenance_maintenance_company');
    }
}
