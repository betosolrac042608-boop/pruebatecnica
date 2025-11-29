<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanTrabajoZonaTarea extends Model
{
    use HasFactory;

    protected $table = 'plan_trabajo_zona_tareas';

    protected $fillable = [
        'plan_trabajo_id',
        'zona_id',
        'tarea_zona_id',
        'descripcion',
        'estado',
        'comentarios',
        'fecha_hora_inicio',
        'fecha_hora_fin',
    ];

    protected $casts = [
        'fecha_hora_inicio' => 'datetime',
        'fecha_hora_fin' => 'datetime',
    ];

    public function plan()
    {
        return $this->belongsTo(PlanTrabajoDiario::class, 'plan_trabajo_id');
    }

    public function zona()
    {
        return $this->belongsTo(Zona::class);
    }

    public function tareaZona()
    {
        return $this->belongsTo(TareaZona::class, 'tarea_zona_id');
    }

    /**
     * Alias para tareaZona - usado en el código como tareaOriginal
     */
    public function tareaOriginal()
    {
        return $this->belongsTo(TareaZona::class, 'tarea_zona_id');
    }

    /**
     * Obtener las fotos de la tarea (antes y después)
     */
    public function fotos()
    {
        return $this->hasMany(FotoTarea::class, 'plan_trabajo_zona_tarea_id');
    }

    /**
     * Obtener la foto "antes" de la tarea
     */
    public function fotoAntes()
    {
        return $this->hasOne(FotoTarea::class, 'plan_trabajo_zona_tarea_id')->where('tipo', 'antes');
    }

    /**
     * Obtener la foto "después" de la tarea
     */
    public function fotoDespues()
    {
        return $this->hasOne(FotoTarea::class, 'plan_trabajo_zona_tarea_id')->where('tipo', 'despues');
    }
}
