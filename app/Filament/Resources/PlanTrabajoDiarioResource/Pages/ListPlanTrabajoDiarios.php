<?php

namespace App\Filament\Resources\PlanTrabajoDiarioResource\Pages;

use App\Filament\Resources\PlanTrabajoDiarioResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPlanTrabajoDiarios extends ListRecords
{
    protected static string $resource = PlanTrabajoDiarioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
