<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Herramienta extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'herramientas';

    protected $fillable = [
        'matricula',
        'nombre',
        'tipo',
        'marca',
        'modelo',
        'numero_serie',
        'estado',
        'ubicacion',
        'fecha_adquisicion',
        'valor',
        'responsable_id',
        'zona_id',
        'ultimo_mantenimiento',
        'proximo_mantenimiento',
        'observaciones'
    ];

    protected $casts = [
        'fecha_adquisicion' => 'date',
        'ultimo_mantenimiento' => 'date',
        'proximo_mantenimiento' => 'date',
        'valor' => 'decimal:2'
    ];

    /**
     * Obtener el usuario responsable de la herramienta.
     */
    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_id');
    }

    /**
     * Obtener la zona a la que pertenece la herramienta.
     */
    public function zona()
    {
        return $this->belongsTo(Zona::class);
    }

    /**
     * Obtener las actividades relacionadas con esta herramienta.
     */
    public function actividades()
    {
        return $this->morphMany(Actividad::class, 'entidad');
    }

    /**
     * Obtener las acciones programadas relacionadas con esta herramienta.
     */
    public function accionesProgramadas()
    {
        return $this->morphMany(AccionProgramada::class, 'entidad');
    }
}