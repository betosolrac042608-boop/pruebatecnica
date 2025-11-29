<?php

namespace App\Models;

use App\Jobs\EnviarPlanTrabajoAGptJob;
use App\Models\EvaluacionPlanTrabajo;
use App\Models\PlanFotoZona;
use App\Models\PlanTrabajoZonaTarea;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlanTrabajoDiario extends Model
{
    use HasFactory;

    protected $table = 'plan_trabajo_diarios';

    protected $fillable = [
        'predio_id',
        'usuario_id',
        'fecha',
        'estado',
        'datos_gpt',
        'resumen_ia',
        'turno_inicio',
        'turno_fin',
        'comida_inicio',
        'comida_fin',
    ];

    protected $casts = [
        'fecha' => 'date',
        'datos_gpt' => 'array',
        'turno_inicio' => 'string',
        'turno_fin' => 'string',
        'comida_inicio' => 'string',
        'comida_fin' => 'string',
    ];

    public function predio()
    {
        return $this->belongsTo(Predio::class);
    }

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function tareas()
    {
        return $this->hasMany(PlanTrabajoZonaTarea::class, 'plan_trabajo_id');
    }

    public function fotos()
    {
        return $this->hasMany(PlanFotoZona::class, 'plan_trabajo_id');
    }

    public function evaluaciones()
    {
        return $this->hasMany(EvaluacionPlanTrabajo::class, 'plan_trabajo_id');
    }

    public function logs()
    {
        return $this->hasMany(PlanTrabajoLog::class, 'plan_trabajo_id');
    }

    public function conversaciones()
    {
        return $this->hasMany(PlanTrabajoConversacion::class, 'plan_trabajo_id');
    }

    protected static function booted(): void
    {
        static::created(function (PlanTrabajoDiario $planTrabajo) {
            EnviarPlanTrabajoAGptJob::dispatch($planTrabajo);
        });
    }
}
