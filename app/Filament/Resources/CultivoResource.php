<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CultivoResource\Pages;
use App\Models\Cultivo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CultivoResource extends Resource
{
    protected static ?string $model = Cultivo::class;

    protected static ?string $navigationIcon = 'heroicon-o-sparkles';
    protected static ?string $navigationLabel = 'Cultivos';
    protected static ?string $modelLabel = 'Cultivo';
    protected static ?string $pluralModelLabel = 'Cultivos';
    protected static ?string $navigationGroup = 'Gestión de Activos';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información General')
                    ->description('Datos básicos del cultivo')
                    ->schema([
                        Forms\Components\TextInput::make('matricula')
                            ->label('Matrícula')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->placeholder('Ej: CUL-001')
                            ->alphaDash()
                            ->validationMessages([
                                'unique' => 'Esta matrícula ya está en uso.',
                                'required' => 'La matrícula es obligatoria.',
                            ]),
                        
                        Forms\Components\TextInput::make('nombre')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('Ej: Maíz Parcela Norte')
                            ->validationMessages([
                                'required' => 'El nombre es obligatorio.',
                            ]),
                        
                        Forms\Components\Select::make('tipo')
                            ->label('Tipo de Cultivo')
                            ->required()
                            ->options([
                                'Cereal' => 'Cereal',
                                'Leguminosa' => 'Leguminosa',
                                'Hortaliza' => 'Hortaliza',
                                'Frutal' => 'Frutal',
                                'Forraje' => 'Forraje',
                                'Industrial' => 'Industrial',
                            ])
                            ->native(false)
                            ->placeholder('Seleccione el tipo'),
                        
                        Forms\Components\TextInput::make('especie')
                            ->label('Especie')
                            ->required()
                            ->maxLength(50)
                            ->placeholder('Ej: Maíz')
                            ->datalist([
                                'Maíz',
                                'Trigo',
                                'Arroz',
                                'Frijol',
                                'Tomate',
                                'Papa',
                                'Lechuga',
                                'Zanahoria',
                            ]),
                        
                        Forms\Components\TextInput::make('area')
                            ->label('Área (hectáreas)')
                            ->required()
                            ->numeric()
                            ->suffix('ha')
                            ->placeholder('Ej: 2.5')
                            ->minValue(0)
                            ->step(0.01)
                            ->validationMessages([
                                'required' => 'El área es obligatoria.',
                            ]),
                        
                        Forms\Components\Select::make('estado')
                            ->label('Estado')
                            ->required()
                            ->options([
                                'En Preparación' => 'En Preparación',
                                'Sembrado' => 'Sembrado',
                                'En Crecimiento' => 'En Crecimiento',
                                'En Producción' => 'En Producción',
                                'Cosechado' => 'Cosechado',
                                'En Descanso' => 'En Descanso',
                            ])
                            ->native(false)
                            ->default('En Preparación')
                            ->placeholder('Seleccione el estado'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Ubicación y Fechas')
                    ->schema([
                        Forms\Components\TextInput::make('ubicacion')
                            ->label('Ubicación')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('Ej: Parcela 3, Sección Norte'),
                        
                        Forms\Components\DatePicker::make('fecha_siembra')
                            ->label('Fecha de Siembra')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->placeholder('Seleccione la fecha'),
                        
                        Forms\Components\DatePicker::make('fecha_estimada_cosecha')
                            ->label('Fecha Estimada de Cosecha')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->placeholder('Seleccione la fecha')
                            ->after('fecha_siembra'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Mantenimiento')
                    ->schema([
                        Forms\Components\DatePicker::make('ultima_fertilizacion')
                            ->label('Última Fertilización')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->maxDate(now())
                            ->placeholder('Seleccione la fecha'),
                        
                        Forms\Components\DatePicker::make('ultimo_riego')
                            ->label('Último Riego')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->maxDate(now())
                            ->placeholder('Seleccione la fecha'),
                        
                        Forms\Components\Textarea::make('observaciones')
                            ->label('Observaciones')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Notas adicionales sobre el cultivo'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('matricula')
                    ->label('Matrícula')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->badge()
                    ->color('success')
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('tipo')
                    ->label('Tipo')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('especie')
                    ->label('Especie')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('area')
                    ->label('Área')
                    ->numeric(decimalPlaces: 2)
                    ->suffix(' ha')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'En Preparación' => 'gray',
                        'Sembrado' => 'info',
                        'En Crecimiento' => 'warning',
                        'En Producción' => 'success',
                        'Cosechado' => 'primary',
                        'En Descanso' => 'secondary',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('ubicacion')
                    ->label('Ubicación')
                    ->searchable()
                    ->icon('heroicon-m-map-pin')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('fecha_siembra')
                    ->label('Fecha Siembra')
                    ->date('d/m/Y')
                    ->sortable()
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('fecha_estimada_cosecha')
                    ->label('Cosecha Estimada')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(isToggledHiddenByDefault: true),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipo')
                    ->label('Tipo')
                    ->options([
                        'Cereal' => 'Cereal',
                        'Leguminosa' => 'Leguminosa',
                        'Hortaliza' => 'Hortaliza',
                        'Frutal' => 'Frutal',
                        'Forraje' => 'Forraje',
                        'Industrial' => 'Industrial',
                    ])
                    ->multiple()
                    ->preload(),
                
                Tables\Filters\SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'En Preparación' => 'En Preparación',
                        'Sembrado' => 'Sembrado',
                        'En Crecimiento' => 'En Crecimiento',
                        'En Producción' => 'En Producción',
                        'Cosechado' => 'Cosechado',
                        'En Descanso' => 'En Descanso',
                    ])
                    ->multiple(),
                
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
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\RestoreBulkAction::make(),
                    Tables\Actions\ForceDeleteBulkAction::make(),
                ]),
            ])
            ->emptyStateHeading('No hay cultivos registrados')
            ->emptyStateDescription('Comienza agregando tu primer cultivo.')
            ->emptyStateIcon('heroicon-o-sparkles');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCultivos::route('/'),
            'create' => Pages\CreateCultivo::route('/create'),
            'edit' => Pages\EditCultivo::route('/{record}/edit'),
        ];
    }
}

