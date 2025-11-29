<?php

namespace App\Filament\Resources\PredioResource\Pages;

use App\Filament\Resources\PredioResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPredio extends EditRecord
{
    protected static string $resource = PredioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
