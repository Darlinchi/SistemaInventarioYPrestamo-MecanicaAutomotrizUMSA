<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ReturnDetailAccessory extends Model
{
    // Forzamos el nombre de la tabla de tu migración[cite: 4]
    protected $table = 'return_detail_accessories';

    protected $fillable = [
        'return_detail_id',
        'accessory_id',
        'estado_accesorio'
    ];

    /**
     * Relación con el detalle de la devolución[cite: 4]
     */
    public function returnDetail(): BelongsTo
    {
        return $this->belongsTo(ReturnDetail::class, 'return_detail_id');
    }

    /**
     * Relación para obtener los datos del accesorio (nombre, foto)[cite: 8]
     */
    public function accessory(): BelongsTo
    {
        return $this->belongsTo(Accessory::class, 'accessory_id');
    }
}
