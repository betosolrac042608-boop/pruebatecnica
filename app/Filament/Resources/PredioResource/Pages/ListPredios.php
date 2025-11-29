<?php

namespace App\Filament\Resources\PredioResource\Pages;

use App\Filament\Resources\PredioResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPredios extends ListRecords
{
    protected static string $resource = PredioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
