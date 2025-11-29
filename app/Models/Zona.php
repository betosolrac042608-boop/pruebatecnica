<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Zona extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'zonas';

    protected $fillable = [
        'predio_id',
        'nombre',
        'codigo',
        'descripcion',
        'ubicacion',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean'
    ];

    /**
     * Obtener el predio al que pertenece la zona.
     */
    public function predio()
    {
        return $this->belongsTo(Predio::class);
    }

    /**
     * Obtener las herramientas de la zona.
     */
    public function herramientas()
    {
        return $this->hasMany(Herramienta::class);
    }

    /**
     * Obtener los animales de la zona.
     */
    public function animales()
    {
        return $this->hasMany(Animal::class);
    }

    /**
     * Obtener los cultivos de la zona.
     */
    public function cultivos()
    {
        return $this->hasMany(Cultivo::class);
    }

    /**
     * Obtener las tareas asociadas a la zona.
     */
    public function tareas()
    {
        return $this->hasMany(TareaZona::class);
    }

    /**
     * Fotos asociadas con el plan de trabajo.
     */
    public function fotosPlan()
    {
        return $this->hasMany(PlanFotoZona::class);
    }

    /**
     * Evaluaciones realizadas sobre la zona.
     */
    public function evaluacionesPlan()
    {
        return $this->hasMany(EvaluacionPlanTrabajo::class);
    }
}
