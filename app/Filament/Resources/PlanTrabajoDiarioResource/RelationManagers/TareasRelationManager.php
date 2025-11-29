<?php

namespace App\Filament\Resources\PlanTrabajoDiarioResource\RelationManagers;

use App\Models\TareaZona;
use App\Models\Zona;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TareasRelationManager extends RelationManager
{
    protected static string $relationship = 'tareas';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('zona_id')
                    ->label('Zona')
                    ->relationship('zona', 'nombre')
                    ->searchable()
                    ->required(),
                Forms\Components\Select::make('tarea_zona_id')
                    ->label('Tarea sugerida (opcional)')
                    ->relationship('tareaZona', 'nombre')
                    ->searchable()
                    ->preload()
                    ->helperText('Deja vacío para crear una tarea nueva que no esté registrada'),
                Forms\Components\Textarea::make('descripcion')
                    ->label('Descripción de la tarea')
                    ->rows(3)
                    ->required()
                    ->columnSpanFull()
                    ->helperText('Describe la tarea a realizar. Si no seleccionaste una tarea sugerida, esta será una tarea nueva.'),
                Forms\Components\DateTimePicker::make('fecha_hora_inicio')
                    ->label('Fecha y hora de inicio')
                    ->displayFormat('d/m/Y H:i')
                    ->seconds(false)
                    ->required(),
                Forms\Components\DateTimePicker::make('fecha_hora_fin')
                    ->label('Fecha y hora de fin')
                    ->displayFormat('d/m/Y H:i')
                    ->seconds(false)
                    ->required()
                    ->after('fecha_hora_inicio'),
                Forms\Components\Select::make('estado')
                    ->label('Estado')
                    ->options([
                        'pendiente' => 'Pendiente',
                        'en_progreso' => 'En progreso',
                        'completado' => 'Completado',
                    ])
                    ->default('pendiente')
                    ->required(),
                Forms\Components\Textarea::make('comentarios')
                    ->label('Comentarios')
                    ->rows(2)
                    ->columnSpanFull(),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('descripcion')
            ->columns([
                Tables\Columns\TextColumn::make('zona.nombre')
                    ->label('Zona')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tareaZona.nombre')
                    ->label('Tarea relacionada')
                    ->searchable(),
                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->limit(30),
                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->colors([
                        'danger' => 'pendiente',
                        'warning' => 'en_progreso',
                        'success' => 'completado',
                    ]),
                Tables\Columns\TextColumn::make('fecha_hora_inicio')
                    ->label('Inicio')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('fecha_hora_fin')
                    ->label('Fin')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                Tables\Columns\TextColumn::make('comentarios')
                    ->label('Comentarios')
                    ->limit(30),
            ])
            ->filters([
                SelectFilter::make('zona_id')
                    ->label('Zona')
                    ->relationship('zona', 'nombre')
                    ->multiple()
                    ->preload(),
                TernaryFilter::make('estado')
                    ->label('Estado')
                    ->placeholder('Todas'),
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
