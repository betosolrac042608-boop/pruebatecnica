<?php

namespace App\Filament\Resources\CultivoResource\Pages;

use App\Filament\Resources\CultivoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCultivos extends ListRecords
{
    protected static string $resource = CultivoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Nuevo Cultivo')
                ->icon('heroicon-o-plus'),
        ];
    }
}

