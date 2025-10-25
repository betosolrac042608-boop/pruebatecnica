<?php

namespace App\Filament\Widgets;

use App\Models\Actividad;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ActividadesRecientes extends BaseWidget
{
    protected static ?int $sort = 2;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Actividad::query()
                    ->with(['usuario', 'estado', 'tipoAccion', 'entidad'])
                    ->latest()
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('nombre_accion')
                    ->label('Acción')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('tipoAccion.nombre')
                    ->label('Tipo')
                    ->badge()
                    ->placeholder('Sin tipo')
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('entidad_type')
                    ->label('Entidad')
                    ->formatStateUsing(function ($state) {
                        return $state ? class_basename($state) : 'N/A';
                    })
                    ->badge()
                    ->color('warning'),
                
                Tables\Columns\TextColumn::make('usuario.name')
                    ->label('Responsable')
                    ->sortable()
                    ->searchable()
                    ->placeholder('Sin asignar'),
                
                Tables\Columns\TextColumn::make('estado.nombre')
                    ->label('Estado')
                    ->badge()
                    ->placeholder('Sin estado')
                    ->color(fn (?string $state): string => match ($state) {
                        'Pendiente' => 'warning',
                        'En Proceso' => 'info',
                        'Completada' => 'success',
                        'Cancelada' => 'danger',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('fecha_programada')
                    ->label('Fecha Programada')
                    ->date('d/m/Y')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('fecha_realizada')
                    ->label('Fecha Realizada')
                    ->date('d/m/Y')
                    ->placeholder('-')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc');
    }
}

