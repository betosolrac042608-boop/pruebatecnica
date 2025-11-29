<?php

namespace App\Filament\Resources\PlanTrabajoDiarioResource\RelationManagers;

use App\Models\TareaZona;
use App\Models\Zona;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\Textarea;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class EvaluacionesRelationManager extends RelationManager
{
    protected static string $relationship = 'evaluaciones';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('zona_id')
                    ->label('Zona')
                    ->relationship('zona', 'nombre')
                    ->required()
                    ->searchable(),
                Forms\Components\Select::make('tarea_zona_id')
                    ->label('Tarea asociada')
                    ->relationship('tareaZona', 'nombre')
                    ->searchable()
                    ->preload(),
                Textarea::make('resultados')
                    ->label('Resultados de la evaluación (JSON)')
                    ->rows(3)
                    ->hint('Se recomienda pegar el JSON devuelto por la IA'),
                Forms\Components\Select::make('calificacion')
                    ->label('Calificación')
                    ->options([
                        'excelente' => 'Excelente',
                        'estable' => 'Estable',
                        'requiere_mejora' => 'Requiere mejora',
                    ]),
                Textarea::make('comentarios')
                    ->label('Comentarios del responsable')
                    ->rows(2)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('zona.nombre')
                    ->label('Zona'),
                Tables\Columns\TextColumn::make('tareaZona.nombre')
                    ->label('Tarea'),
                Tables\Columns\TextColumn::make('calificacion')
                    ->label('Calificación')
                    ->badge(),
                Tables\Columns\TextColumn::make('comentarios')
                    ->label('Comentarios')
                    ->limit(30),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha')
                    ->dateTime('d/m/Y H:i'),
            ])
            ->filters([
                SelectFilter::make('zona_id')
                    ->label('Zona')
                    ->relationship('zona', 'nombre'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
