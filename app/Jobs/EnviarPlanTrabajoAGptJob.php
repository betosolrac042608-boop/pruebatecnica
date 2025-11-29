<?php

namespace App\Jobs;

use App\Models\PlanTrabajoDiario;
use App\Services\PlanTrabajoService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class EnviarPlanTrabajoAGptJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    public function __construct(
        protected PlanTrabajoDiario $planTrabajo
    ) {
    }

    public function handle(PlanTrabajoService $planTrabajoService): void
    {
        Log::info('[Job] EnviarPlanTrabajoAGptJob iniciado', [
            'plan_id' => $this->planTrabajo->id,
            'job_id' => $this->job->getJobId() ?? 'N/A',
        ]);

        try {
            // Recargar el modelo desde la base de datos para asegurar que tenemos los datos más recientes
            Log::info('[Job] Cargando plan de trabajo desde BD', [
                'plan_id' => $this->planTrabajo->id,
            ]);

            $plan = PlanTrabajoDiario::with(['predio.zonas.tareas', 'usuario'])->findOrFail($this->planTrabajo->id);
            
            Log::info('[Job] Plan cargado exitosamente', [
                'plan_id' => $plan->id,
                'predio' => $plan->predio->nombre ?? 'N/A',
                'usuario' => $plan->usuario->name ?? 'N/A',
                'fecha' => $plan->fecha->format('Y-m-d'),
            ]);

            // Usar el servicio que maneja todo el proceso, incluyendo los logs
            Log::info('[Job] Delegando generación del plan al servicio');
            $planTrabajoService->generarPlan($plan);

            Log::info('[Job] Plan de trabajo generado exitosamente', [
                'plan_id' => $plan->id,
            ]);
        } catch (\Throwable $exception) {
            Log::error('[Job] Error en job de generación de plan de trabajo', [
                'plan_id' => $this->planTrabajo->id,
                'error' => $exception->getMessage(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ]);
            
            // Re-lanzar la excepción para que el job se reintente
            throw $exception;
        }
    }
}

