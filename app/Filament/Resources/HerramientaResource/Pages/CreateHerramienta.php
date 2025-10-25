<?php

namespace App\Filament\Resources\HerramientaResource\Pages;

use App\Filament\Resources\HerramientaResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;

class CreateHerramienta extends CreateRecord
{
    protected static string $resource = HerramientaResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title('Herramienta registrada')
            ->body('La herramienta ha sido agregada exitosamente.');
    }
}

