<?php

namespace App\Filament\Resources\AccionProgramadaResource\Pages;

use App\Filament\Resources\AccionProgramadaResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateAccionProgramada extends CreateRecord
{
    protected static string $resource = AccionProgramadaResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Acción programada')
            ->body('La acción ha sido programada exitosamente.');
    }
}

