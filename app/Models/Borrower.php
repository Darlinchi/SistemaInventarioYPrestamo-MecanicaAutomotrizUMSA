<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Borrower extends Model
{
    use HasFactory;

    protected $table = 'borrowers';

    protected $fillable = [
        'cedula_identidad',
        'nombres',
        'apellidos',
        'telefono',
        'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
    ];

    //Relación con el Docente
    public function teacher(): HasOne
    {
        return $this->hasOne(Teacher::class, 'id_teacher');
    }

    //Relación con el Auxiliar
    public function assistant(): HasOne
    {
        // El segundo parámetro es la llave foránea en la tabla assistants
        return $this->hasOne(Assistant::class, 'id_assistant');
    }

    // Relacion con el Estudiante
    public function student(): HasOne
    {
        return $this->hasOne(Student::class, 'id_student');
    }

    // Un método "Heredado" para obtener el tipo de usuario (útil para badges)
    public function getTipoUserAttribute()
    {
        if ($this->teacher()->exists()) return 'Docente';
        if ($this->assistant()->exists()) return 'Auxiliar';
        if ($this->student()->exists()) return 'Estudiante';
        return 'Externo';
    }

    // Un prestamista tiene muchos prestamos
    public function loans(): HasMany
    {
        return $this->hasMany(Loan::class, 'id', 'id');
    }

    // ─── Helper: devuelve el tipo real del borrower ────────────────
    public function getTipoAttribute(): string
    {
        if ($this->teacher()->exists())   return 'docente';
        if ($this->assistant()->exists()) return 'auxiliar';
        if ($this->student()->exists())   return 'estudiante';
        return 'desconocido';
    }

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombres} {$this->apellidos}";
    }
}
