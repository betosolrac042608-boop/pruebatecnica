<?php

namespace App\Filament\Resources\CultivoResource\Pages;

use App\Filament\Resources\CultivoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditCultivo extends EditRecord
{
    protected static string $resource = CultivoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Eliminar'),
            Actions\RestoreAction::make()
                ->label('Restaurar'),
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
            ->title('Cultivo actualizado')
            ->body('Los cambios han sido guardados correctamente.');
    }
}

