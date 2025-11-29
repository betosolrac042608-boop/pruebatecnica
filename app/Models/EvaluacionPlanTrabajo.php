<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class EvaluacionPlanTrabajo extends Model
{
    use HasFactory;

    protected $table = 'evaluaciones_plan_trabajo';

    protected $fillable = [
        'plan_trabajo_id',
        'zona_id',
        'tarea_zona_id',
        'resultados',
        'calificacion',
        'comentarios',
    ];

    protected $casts = [
        'resultados' => 'array',
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
        return $this->belongsTo(TareaZona::class);
    }
}
