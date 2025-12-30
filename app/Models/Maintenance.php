<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    //

    public function equipo()
    {
        return $this->belongsTo(Equipment::class, 'equipment_id');
    }

    public function empresa()
    {
        return $this->belongsTo(MaintenanceCompany::class, 'maintenance_company_id');
    }
}
