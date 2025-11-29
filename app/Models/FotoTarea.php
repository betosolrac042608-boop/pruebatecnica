<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FotoTarea extends Model
{
    use HasFactory;

    protected $table = 'fotos_tareas';

    protected $fillable = [
        'plan_trabajo_zona_tarea_id',
        'tipo',
        'ruta',
        'evaluacion_gpt',
        'metadata_gpt',
        'calificacion',
        'tomada_en',
    ];

    protected $casts = [
        'metadata_gpt' => 'array',
        'tomada_en' => 'datetime',
    ];

    public function tarea()
    {
        return $this->belongsTo(PlanTrabajoZonaTarea::class, 'plan_trabajo_zona_tarea_id');
    }
}
