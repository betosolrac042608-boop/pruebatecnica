<?php

namespace App\Filament\Widgets;

use App\Models\AccionProgramada;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class ProximasAcciones extends BaseWidget
{
    protected static ?int $sort = 3;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                AccionProgramada::query()
                    ->with(['usuario', 'tipoAccion', 'entidad'])
                    ->where('completed', false)
                    ->whereDate('fecha_programada', '>=', now())
                    ->orderBy('fecha_programada')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('nombre_accion')
                    ->label('Acción Programada')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('tipoAccion.nombre')
                    ->label('Tipo')
                    ->badge()
                    ->placeholder('Sin tipo')
                    ->color('primary'),
                
                Tables\Columns\TextColumn::make('entidad_type')
                    ->label('Entidad')
                    ->formatStateUsing(function ($state) {
                        return $state ? class_basename($state) : 'N/A';
                    })
                    ->badge()
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('usuario.name')
                    ->label('Responsable')
                    ->sortable()
                    ->searchable()
                    ->placeholder('Sin asignar'),
                
                Tables\Columns\TextColumn::make('fecha_programada')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn ($record) => $record->fecha_programada->isPast() ? 'danger' : 'success'),
                
                Tables\Columns\TextColumn::make('hora_programada')
                    ->label('Hora')
                    ->time('H:i'),
                
                Tables\Columns\IconColumn::make('completed')
                    ->label('Completada')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
            ])
            ->defaultSort('fecha_programada', 'asc');
    }
}

