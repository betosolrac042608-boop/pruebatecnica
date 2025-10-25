<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Actividad extends Model
{
    use HasFactory;

    protected $table = 'actividades';

    protected $fillable = [
        'tipo_accion_id',
        'nombre_accion',
        'descripcion',
        'fecha_programada',
        'hora_programada',
        'fecha_realizada',
        'hora_realizada',
        'usuario_id',
        'estado_id',
        'notas',
        'from_scheduled',
        'original_scheduled_id',
        'entidad_type',
        'entidad_id'
    ];

    protected $casts = [
        'fecha_programada' => 'date',
        'fecha_realizada' => 'date',
        'from_scheduled' => 'boolean'
    ];

    /**
     * Obtener el modelo dueño de la actividad (Animal, Cultivo o Herramienta).
     */
    public function entidad()
    {
        return $this->morphTo();
    }

    /**
     * Obtener el usuario responsable de la actividad.
     */
    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    /**
     * Obtener el tipo de acción.
     */
    public function tipoAccion()
    {
        return $this->belongsTo(TipoAccion::class, 'tipo_accion_id');
    }

    /**
     * Obtener el estado de la actividad.
     */
    public function estado()
    {
        return $this->belongsTo(EstadoActividad::class, 'estado_id');
    }
}