<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Animal extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'animales';

    protected $fillable = [
        'matricula',
        'nombre',
        'especie',
        'raza',
        'fecha_nacimiento',
        'fecha_adquisicion',
        'sexo',
        'peso',
        'valor_estimado',
        'estado',
        'ubicacion',
        'zona_id',
        'ultima_revision',
        'ultima_vacuna',
        'observaciones'
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'fecha_adquisicion' => 'date',
        'ultima_revision' => 'date',
        'ultima_vacuna' => 'date',
        'peso' => 'decimal:2',
        'valor_estimado' => 'decimal:2'
    ];

    /**
     * Obtener la zona a la que pertenece el animal.
     */
    public function zona()
    {
        return $this->belongsTo(Zona::class);
    }

    /**
     * Obtener las actividades relacionadas con este animal.
     */
    public function actividades()
    {
        return $this->morphMany(Actividad::class, 'entidad');
    }

    /**
     * Obtener las acciones programadas relacionadas con este animal.
     */
    public function accionesProgramadas()
    {
        return $this->morphMany(AccionProgramada::class, 'entidad');
    }
}