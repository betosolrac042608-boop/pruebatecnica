<?php

namespace App\Filament\Resources\PlanTrabajoDiarioResource\Pages;

use App\Filament\Resources\PlanTrabajoDiarioResource;
use App\Models\User;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditPlanTrabajoDiario extends EditRecord
{
    protected static string $resource = PlanTrabajoDiarioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
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

        return $data;
    }
}
