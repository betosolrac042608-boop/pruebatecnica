<?php

namespace App\Filament\Resources\PlanTrabajoDiarioResource\Pages;

use App\Filament\Resources\PlanTrabajoDiarioResource;
use App\Models\PlanTrabajoDiario;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use Carbon\Carbon;

class CreatePlanTrabajoDiario extends CreateRecord
{
    protected static string $resource = PlanTrabajoDiarioResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Validar que el usuario asignado tenga rol Operador o Supervisor
        if (isset($data['usuario_id'])) {
            $usuario = User::with('rol')->find($data['usuario_id']);
            
            if (!$usuario || !$usuario->rol) {
                Notification::make()
                    ->title('Error de validación')
                    ->body('El usuario seleccionado no tiene un rol válido asignado.')
                    ->danger()
                    ->send();
                
                $this->halt();
            }
            
            if (!in_array($usuario->rol->nombre, ['Operador', 'Supervisor'])) {
                Notification::make()
                    ->title('Error de validación')
                    ->body('El plan de trabajo solo puede asignarse a usuarios con rol Operador o Supervisor.')
                    ->danger()
                    ->send();
                
                $this->halt();
            }
        }

        // Guardar el tipo de plan antes de removerlo
        $this->tipoPlan = $data['tipo_plan'] ?? 'diario';
        $this->fechaInicioSemanal = $data['fecha_inicio_semanal'] ?? null;

        // Si es plan semanal, preparar la fecha inicial para el primer día
        if ($this->tipoPlan === 'semanal' && $this->fechaInicioSemanal) {
            $data['fecha'] = $this->fechaInicioSemanal;
        }

        // Remover campos que no van a la BD
        unset($data['tipo_plan'], $data['fecha_inicio_semanal']);

        return $data;
    }

    protected $tipoPlan = 'diario';
    protected $fechaInicioSemanal = null;

    protected function handleRecordCreation(array $data): PlanTrabajoDiario
    {
        if ($this->tipoPlan === 'semanal' && $this->fechaInicioSemanal) {
            // Crear 7 planes, uno por cada día
            $fechaInicio = Carbon::parse($this->fechaInicioSemanal);
            $planesCreados = [];
            
            for ($i = 0; $i < 7; $i++) {
                $fecha = $fechaInicio->copy()->addDays($i);
                
                $planData = $data;
                $planData['fecha'] = $fecha->format('Y-m-d');
                
                $plan = PlanTrabajoDiario::create($planData);
                $planesCreados[] = $plan;
            }
            
            Notification::make()
                ->title('Planes semanales creados')
                ->body('Se han creado ' . count($planesCreados) . ' planes de trabajo para la semana.')
                ->success()
                ->send();
            
            // Retornar el primer plan creado para la redirección
            return $planesCreados[0];
        } else {
            // Plan diario normal
            return parent::handleRecordCreation($data);
        }
    }
}
