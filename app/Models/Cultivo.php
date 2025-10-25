<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cultivo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cultivos';

    protected $fillable = [
        'matricula',
        'nombre',
        'tipo',
        'especie',
        'area',
        'estado',
        'ubicacion',
        'fecha_siembra',
        'fecha_estimada_cosecha',
        'ultima_fertilizacion',
        'ultimo_riego',
        'observaciones'
    ];

    protected $casts = [
        'fecha_siembra' => 'date',
        'fecha_estimada_cosecha' => 'date',
        'ultima_fertilizacion' => 'date',
        'ultimo_riego' => 'date',
        'area' => 'decimal:2'
    ];

    /**
     * Obtener las actividades relacionadas con este cultivo.
     */
    public function actividades()
    {
        return $this->morphMany(Actividad::class, 'entidad');
    }

    /**
     * Obtener las acciones programadas relacionadas con este cultivo.
     */
    public function accionesProgramadas()
    {
        return $this->morphMany(AccionProgramada::class, 'entidad');
    }
}