<?php

namespace App\Filament\Resources\EstadoActividadResource\Pages;

use App\Filament\Resources\EstadoActividadResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageEstadoActividades extends ManageRecords
{
    protected static string $resource = EstadoActividadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nuevo Estado')
                ->icon('heroicon-o-plus'),
        ];
    }
}

