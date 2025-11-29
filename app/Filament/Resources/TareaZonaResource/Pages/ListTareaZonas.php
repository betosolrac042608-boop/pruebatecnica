<?php

namespace App\Filament\Resources\TareaZonaResource\Pages;

use App\Filament\Resources\TareaZonaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTareaZonas extends ListRecords
{
    protected static string $resource = TareaZonaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
