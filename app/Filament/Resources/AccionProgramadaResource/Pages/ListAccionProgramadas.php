<?php

namespace App\Filament\Resources\AccionProgramadaResource\Pages;

use App\Filament\Resources\AccionProgramadaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListAccionProgramadas extends ListRecords
{
    protected static string $resource = AccionProgramadaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nueva Acción Programada')
                ->icon('heroicon-o-plus'),
        ];
    }
}

