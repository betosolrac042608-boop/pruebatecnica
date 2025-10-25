<?php

namespace App\Filament\Resources\CultivoResource\Pages;

use App\Filament\Resources\CultivoResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateCultivo extends CreateRecord
{
    protected static string $resource = CultivoResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Cultivo registrado')
            ->body('El cultivo ha sido agregado exitosamente.');
    }
}

