<?php

namespace App\Filament\Resources\ZonaResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TareasRelationManager extends RelationManager
{
    protected static string $relationship = 'tareas';
    protected static ?string $recordTitleAttribute = 'nombre';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('clave')
                    ->label('Clave')
                    ->required()
                    ->maxLength(50),
                Forms\Components\TextInput::make('nombre')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(150),
                Forms\Components\Textarea::make('descripcion')
                    ->label('Descripción')
                    ->rows(2)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('objetivo')
                    ->label('Objetivo')
                    ->rows(2)
                    ->columnSpanFull(),
                Forms\Components\Textarea::make('tareas_sugeridas')
                    ->label('Tareas sugeridas')
                    ->rows(2)
                    ->columnSpanFull(),
                Forms\Components\TextInput::make('frecuencia')
                    ->label('Frecuencia')
                    ->maxLength(150),
                Forms\Components\TextInput::make('tiempo_minutos')
                    ->label('Tiempo (minutos)')
                    ->numeric(),
                Forms\Components\Toggle::make('activo')
                    ->label('Activo')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->renderSummary()
            ->columns([
                Tables\Columns\TextColumn::make('clave')
                    ->label('Clave')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('frecuencia')
                    ->label('Frecuencia')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('tiempo_minutos')
                    ->label('Tiempo (minutos)')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('activo')
                    ->label('Activo')
                    ->placeholder('Todas')
                    ->trueLabel('Activas')
                    ->falseLabel('Inactivas'),
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
