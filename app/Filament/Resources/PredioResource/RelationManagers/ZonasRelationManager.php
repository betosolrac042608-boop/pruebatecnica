<?php

namespace App\Filament\Resources\PredioResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ZonasRelationManager extends RelationManager
{
    protected static string $relationship = 'zonas';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('nombre')
                    ->label('Nombre')
                    ->required()
                    ->maxLength(150)
                    ->placeholder('Ej: Zona Norte, Pastizal 1'),
                
                Forms\Components\TextInput::make('codigo')
                    ->label('Código')
                    ->required()
                    ->maxLength(50)
                    ->placeholder('Ej: ZN-001')
                    ->alphaDash()
                    ->unique(
                        table: 'zonas',
                        column: 'codigo',
                        ignoreRecord: true,
                        modifyRuleUsing: function ($rule) {
                            return $rule->where('predio_id', $this->getOwnerRecord()->id);
                        }
                    )
                    ->validationMessages([
                        'required' => 'El código es obligatorio.',
                        'unique' => 'Este código ya existe en este predio.',
                    ]),
                
                Forms\Components\Textarea::make('ubicacion')
                    ->label('Ubicación')
                    ->rows(2)
                    ->placeholder('Ubicación específica dentro del predio'),
                
                Forms\Components\Textarea::make('descripcion')
                    ->label('Descripción')
                    ->rows(3)
                    ->placeholder('Información adicional sobre la zona'),
                
                Forms\Components\Toggle::make('activo')
                    ->label('Activo')
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('nombre')
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('ubicacion')
                    ->label('Ubicación')
                    ->searchable()
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->ubicacion)
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('herramientas_count')
                    ->label('Herramientas')
                    ->counts('herramientas')
                    ->badge()
                    ->color('warning')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('animales_count')
                    ->label('Animales')
                    ->counts('animales')
                    ->badge()
                    ->color('primary')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('cultivos_count')
                    ->label('Cultivos')
                    ->counts('cultivos')
                    ->badge()
                    ->color('success')
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
                    ->trueLabel('Solo activas')
                    ->falseLabel('Solo inactivas'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                ]),
            ]);
    }
}
