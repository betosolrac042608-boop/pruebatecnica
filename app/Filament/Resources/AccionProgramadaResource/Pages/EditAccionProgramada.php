<?php

namespace App\Filament\Resources\AccionProgramadaResource\Pages;

use App\Filament\Resources\AccionProgramadaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditAccionProgramada extends EditRecord
{
    protected static string $resource = AccionProgramadaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Eliminar'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Acción actualizada')
            ->body('Los cambios han sido guardados correctamente.');
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        if ($data['completed'] && !$this->record->completed) {
            $data['completed_at'] = now();
        } elseif (!$data['completed']) {
            $data['completed_at'] = null;
        }
        
        return $data;
    }
}

