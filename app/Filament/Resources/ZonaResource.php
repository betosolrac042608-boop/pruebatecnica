<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ZonaResource\Pages;
use App\Filament\Resources\ZonaResource\RelationManagers;
use App\Filament\Concerns\HideFromOperarioSupervisor;
use App\Models\Zona;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ZonaResource extends Resource
{
    use HideFromOperarioSupervisor;
    protected static ?string $model = Zona::class;

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';
    protected static ?string $navigationLabel = 'Zonas';
    protected static ?string $modelLabel = 'Zona';
    protected static ?string $pluralModelLabel = 'Zonas';
    protected static ?string $navigationGroup = 'Gestión de Activos';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información General')
                    ->description('Datos básicos de la zona')
                    ->schema([
                        Forms\Components\Select::make('predio_id')
                            ->label('Predio')
                            ->relationship('predio', 'nombre')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set) => $set('codigo', ''))
                            ->placeholder('Seleccione un predio')
                            ->validationMessages([
                                'required' => 'El predio es obligatorio.',
                            ]),
                        
                        Forms\Components\TextInput::make('nombre')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(150)
                            ->placeholder('Ej: Zona Norte, Pastizal 1')
                            ->validationMessages([
                                'required' => 'El nombre es obligatorio.',
                            ]),
                        
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
                                modifyRuleUsing: function ($rule, $get) {
                                    $predioId = $get('predio_id');
                                    if ($predioId) {
                                        return $rule->where('predio_id', $predioId);
                                    }
                                    return $rule;
                                }
                            )
                            ->validationMessages([
                                'required' => 'El código es obligatorio.',
                                'unique' => 'Este código ya existe en el predio seleccionado.',
                            ]),
                        
                        Forms\Components\Textarea::make('ubicacion')
                            ->label('Ubicación')
                            ->rows(2)
                            ->placeholder('Ubicación específica dentro del predio'),
                        
                        Forms\Components\Toggle::make('activo')
                            ->label('Activo')
                            ->default(true)
                            ->helperText('Indica si la zona está activa'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Descripción')
                    ->schema([
                        Forms\Components\Textarea::make('descripcion')
                            ->label('Descripción')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Información adicional sobre la zona'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('predio.nombre')
                    ->label('Predio')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('codigo')
                    ->label('Código')
                    ->searchable()
                    ->sortable()
                    ->copyable()
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
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('animales_count')
                    ->label('Animales')
                    ->counts('animales')
                    ->badge()
                    ->color('primary')
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('cultivos_count')
                    ->label('Cultivos')
                    ->counts('cultivos')
                    ->badge()
                    ->color('success')
                    ->sortable()
                    ->toggleable(),
                
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
                Tables\Filters\SelectFilter::make('predio_id')
                    ->label('Predio')
                    ->relationship('predio', 'nombre')
                    ->multiple()
                    ->preload(),
                
                Tables\Filters\TernaryFilter::make('activo')
                    ->label('Activo')
                    ->placeholder('Todas')
                    ->trueLabel('Solo activas')
                    ->falseLabel('Solo inactivas'),
                
                Tables\Filters\TrashedFilter::make()
                    ->label('Eliminadas'),
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
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No hay zonas registradas')
            ->emptyStateDescription('Comienza agregando tu primera zona.')
            ->emptyStateIcon('heroicon-o-squares-2x2');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\TareasRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListZonas::route('/'),
            'create' => Pages\CreateZona::route('/create'),
            'edit' => Pages\EditZona::route('/{record}/edit'),
        ];
    }
}
