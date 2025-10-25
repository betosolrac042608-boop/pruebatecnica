<?php

namespace App\Filament\Resources\ActividadResource\Pages;

use App\Filament\Resources\ActividadResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateActividad extends CreateRecord
{
    protected static string $resource = ActividadResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Actividad creada')
            ->body('La actividad ha sido registrada exitosamente.');
    }
}

