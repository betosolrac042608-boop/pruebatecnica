<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PredioResource\Pages;
use App\Filament\Resources\PredioResource\RelationManagers;
use App\Filament\Concerns\HideFromOperarioSupervisor;
use App\Models\Predio;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PredioResource extends Resource
{
    use HideFromOperarioSupervisor;
    protected static ?string $model = Predio::class;

    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationLabel = 'Predios';
    protected static ?string $modelLabel = 'Predio';
    protected static ?string $pluralModelLabel = 'Predios';
    protected static ?string $navigationGroup = 'Gestión de Activos';
    protected static ?int $navigationSort = 0;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información General')
                    ->description('Datos básicos del predio o propiedad')
                    ->schema([
                        Forms\Components\TextInput::make('nombre')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(150)
                            ->placeholder('Ej: Finca San José')
                            ->validationMessages([
                                'required' => 'El nombre es obligatorio.',
                            ]),
                        
                        Forms\Components\TextInput::make('codigo')
                            ->label('Código')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->placeholder('Ej: PRED-001')
                            ->alphaDash()
                            ->validationMessages([
                                'unique' => 'Este código ya está en uso.',
                                'required' => 'El código es obligatorio.',
                            ]),
                        
                        Forms\Components\Textarea::make('direccion')
                            ->label('Dirección')
                            ->rows(2)
                            ->placeholder('Dirección completa del predio'),
                        
                        Forms\Components\TextInput::make('area_total')
                            ->label('Área Total (hectáreas)')
                            ->numeric()
                            ->suffix('ha')
                            ->placeholder('Ej: 50.5')
                            ->minValue(0)
                            ->step(0.01),
                        
                        Forms\Components\Select::make('responsable_id')
                            ->label('Responsable')
                            ->relationship('responsable', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Seleccione un responsable')
                            ->helperText('Usuario encargado del predio'),
                        
                        Forms\Components\Toggle::make('activo')
                            ->label('Activo')
                            ->default(true)
                            ->helperText('Indica si el predio está activo'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Descripción')
                    ->schema([
                        Forms\Components\Textarea::make('descripcion')
                            ->label('Descripción')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Información adicional sobre el predio'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->badge()
                    ->color('primary')
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('direccion')
                    ->label('Dirección')
                    ->searchable()
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->direccion)
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('area_total')
                    ->label('Área Total')
                    ->numeric(decimalPlaces: 2)
                    ->suffix(' ha')
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('responsable.name')
                    ->label('Responsable')
                    ->sortable()
                    ->searchable()
                    ->placeholder('Sin asignar')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('zonas_count')
                    ->label('Zonas')
                    ->counts('zonas')
                    ->badge()
                    ->color('info')
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('activo')
                    ->label('Activo')
                    ->boolean()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('responsable_id')
                    ->label('Responsable')
                    ->relationship('responsable', 'name')
                    ->multiple()
                    ->preload(),
                
                Tables\Filters\TernaryFilter::make('activo')
                    ->label('Activo')
                    ->placeholder('Todos')
                    ->trueLabel('Solo activos')
                    ->falseLabel('Solo inactivos'),
                
                Tables\Filters\TrashedFilter::make()
                    ->label('Eliminados'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Ver'),
                Tables\Actions\EditAction::make()
                    ->label('Editar')
                    ->color('warning'),
                Tables\Actions\DeleteAction::make()
                    ->label('Eliminar'),
                Tables\Actions\RestoreAction::make()
                    ->label('Restaurar'),
            ])
            ->defaultSort('created_at', 'desc')
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No hay predios registrados')
            ->emptyStateDescription('Comienza agregando tu primer predio.')
            ->emptyStateIcon('heroicon-o-map');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ZonasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPredios::route('/'),
            'create' => Pages\CreatePredio::route('/create'),
            'view' => Pages\ViewPredio::route('/{record}'),
            'edit' => Pages\EditPredio::route('/{record}/edit'),
        ];
    }
}
