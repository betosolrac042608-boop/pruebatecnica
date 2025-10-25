<?php

namespace App\Filament\Resources\TipoAccionResource\Pages;

use App\Filament\Resources\TipoAccionResource;
use Filament\Actions;
use Filament\Resources\Pages\ManageRecords;

class ManageTipoAcciones extends ManageRecords
{
    protected static string $resource = TipoAccionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nuevo Tipo de Acción')
                ->icon('heroicon-o-plus'),
        ];
    }
}

