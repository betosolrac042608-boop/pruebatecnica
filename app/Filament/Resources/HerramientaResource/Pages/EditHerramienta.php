<?php

namespace App\Filament\Resources\HerramientaResource\Pages;

use App\Filament\Resources\HerramientaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditHerramienta extends EditRecord
{
    protected static string $resource = HerramientaResource::class;

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
            ->title('Herramienta actualizada')
            ->body('Los cambios han sido guardados correctamente.');
    }
}

