<?php

namespace App\Filament\Resources\PlanTrabajoDiarioResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LogsRelationManager extends RelationManager
{
    protected static string $relationship = 'logs';

    protected function canCreate(): bool
    {
        return false;
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('status')
                    ->label('Estado')
                    ->colors([
                        'success' => 'completado',
                        'danger' => 'error',
                        'secondary' => 'pendiente',
                    ]),
                Tables\Columns\TextColumn::make('request_payload')
                    ->label('Solicitud')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn ($state) => isset($state['predio']['nombre']) ? $state['predio']['nombre'] . ' / ' . count($state['tareas_por_zona']) . ' zonas' : 'N/A'),
                Tables\Columns\TextColumn::make('response_payload')
                    ->label('Respuesta GPT')
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->formatStateUsing(fn ($state) => isset($state['resumen']['mensajes']) ? implode(' | ', (array) $state['resumen']['mensajes']) : '-'),
                Tables\Columns\TextColumn::make('error')
                    ->label('Error')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->headerActions([])
            ->actions([])
            ->bulkActions([]);
    }
}
