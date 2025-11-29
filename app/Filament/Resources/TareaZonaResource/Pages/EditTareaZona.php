<?php

namespace App\Filament\Resources\TareaZonaResource\Pages;

use App\Filament\Resources\TareaZonaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTareaZona extends EditRecord
{
    protected static string $resource = TareaZonaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
