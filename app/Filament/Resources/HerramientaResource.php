<?php

namespace App\Filament\Resources;

use App\Filament\Resources\HerramientaResource\Pages;
use App\Models\Herramienta;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class HerramientaResource extends Resource
{
    protected static ?string $model = Herramienta::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';
    protected static ?string $navigationLabel = 'Herramientas';
    protected static ?string $modelLabel = 'Herramienta';
    protected static ?string $pluralModelLabel = 'Herramientas';
    protected static ?string $navigationGroup = 'Gestión de Activos';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información General')
                    ->description('Datos básicos de la herramienta o equipo')
                    ->schema([
                        Forms\Components\TextInput::make('matricula')
                            ->label('Matrícula')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->placeholder('Ej: HER-001')
                            ->alphaDash()
                            ->validationMessages([
                                'unique' => 'Esta matrícula ya está en uso.',
                                'required' => 'La matrícula es obligatoria.',
                            ]),
                        
                        Forms\Components\TextInput::make('nombre')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('Ej: Tractor John Deere')
                            ->validationMessages([
                                'required' => 'El nombre es obligatorio.',
                            ]),
                        
                        Forms\Components\Select::make('tipo')
                            ->label('Tipo')
                            ->required()
                            ->options([
                                'Maquinaria Pesada' => 'Maquinaria Pesada',
                                'Herramienta Manual' => 'Herramienta Manual',
                                'Equipo de Riego' => 'Equipo de Riego',
                                'Equipo Eléctrico' => 'Equipo Eléctrico',
                                'Vehículo' => 'Vehículo',
                                'Implemento' => 'Implemento',
                            ])
                            ->native(false)
                            ->placeholder('Seleccione el tipo'),
                        
                        Forms\Components\Select::make('estado')
                            ->label('Estado')
                            ->required()
                            ->options([
                                'Funcional' => 'Funcional',
                                'En Mantenimiento' => 'En Mantenimiento',
                                'Averiado' => 'Averiado',
                                'Fuera de Servicio' => 'Fuera de Servicio',
                            ])
                            ->native(false)
                            ->default('Funcional')
                            ->placeholder('Seleccione el estado'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Detalles Técnicos')
                    ->schema([
                        Forms\Components\TextInput::make('marca')
                            ->label('Marca')
                            ->maxLength(50)
                            ->placeholder('Ej: John Deere'),
                        
                        Forms\Components\TextInput::make('modelo')
                            ->label('Modelo')
                            ->maxLength(50)
                            ->placeholder('Ej: 5075E'),
                        
                        Forms\Components\TextInput::make('numero_serie')
                            ->label('Número de Serie')
                            ->maxLength(100)
                            ->placeholder('Ej: ABC123456')
                            ->unique(ignoreRecord: true),
                        
                        Forms\Components\TextInput::make('ubicacion')
                            ->label('Ubicación')
                            ->maxLength(100)
                            ->placeholder('Ej: Bodega Principal'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Información Financiera y Responsable')
                    ->schema([
                        Forms\Components\TextInput::make('valor')
                            ->label('Valor de Adquisición')
                            ->numeric()
                            ->prefix('$')
                            ->placeholder('Ej: 50000')
                            ->minValue(0)
                            ->step(0.01),
                        
                        Forms\Components\DatePicker::make('fecha_adquisicion')
                            ->label('Fecha de Adquisición')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->maxDate(now())
                            ->placeholder('Seleccione la fecha'),
                        
                        Forms\Components\Select::make('responsable_id')
                            ->label('Responsable')
                            ->relationship('responsable', 'name')
                            ->searchable()
                            ->preload()
                            ->placeholder('Seleccione un responsable')
                            ->helperText('Usuario encargado del equipo'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Mantenimiento')
                    ->schema([
                        Forms\Components\DatePicker::make('ultimo_mantenimiento')
                            ->label('Último Mantenimiento')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->maxDate(now())
                            ->placeholder('Seleccione la fecha'),
                        
                        Forms\Components\DatePicker::make('proximo_mantenimiento')
                            ->label('Próximo Mantenimiento')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->after('ultimo_mantenimiento')
                            ->placeholder('Seleccione la fecha')
                            ->helperText('Fecha estimada para el próximo mantenimiento'),
                        
                        Forms\Components\Textarea::make('observaciones')
                            ->label('Observaciones')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Notas adicionales sobre la herramienta'),
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
                    ->color('warning')
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
                
                Tables\Columns\TextColumn::make('marca')
                    ->label('Marca')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),
                
                Tables\Columns\TextColumn::make('modelo')
                    ->label('Modelo')
                    ->searchable()
                    ->placeholder('-')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Funcional' => 'success',
                        'En Mantenimiento' => 'warning',
                        'Averiado' => 'danger',
                        'Fuera de Servicio' => 'gray',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('responsable.name')
                    ->label('Responsable')
                    ->sortable()
                    ->searchable()
                    ->placeholder('Sin asignar')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('ubicacion')
                    ->label('Ubicación')
                    ->searchable()
                    ->icon('heroicon-m-map-pin')
                    ->placeholder('-')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('proximo_mantenimiento')
                    ->label('Próx. Mantenimiento')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('-')
                    ->color(fn ($record) => $record->proximo_mantenimiento && $record->proximo_mantenimiento->isPast() ? 'danger' : 'success')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('valor')
                    ->label('Valor')
                    ->money('USD')
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
                        'Maquinaria Pesada' => 'Maquinaria Pesada',
                        'Herramienta Manual' => 'Herramienta Manual',
                        'Equipo de Riego' => 'Equipo de Riego',
                        'Equipo Eléctrico' => 'Equipo Eléctrico',
                        'Vehículo' => 'Vehículo',
                        'Implemento' => 'Implemento',
                    ])
                    ->multiple()
                    ->preload(),
                
                Tables\Filters\SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'Funcional' => 'Funcional',
                        'En Mantenimiento' => 'En Mantenimiento',
                        'Averiado' => 'Averiado',
                        'Fuera de Servicio' => 'Fuera de Servicio',
                    ])
                    ->multiple(),
                
                Tables\Filters\SelectFilter::make('responsable_id')
                    ->label('Responsable')
                    ->relationship('responsable', 'name')
                    ->multiple()
                    ->preload(),
                
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
            ->emptyStateHeading('No hay herramientas registradas')
            ->emptyStateDescription('Comienza agregando tu primera herramienta.')
            ->emptyStateIcon('heroicon-o-wrench-screwdriver');
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
            'index' => Pages\ListHerramientas::route('/'),
            'create' => Pages\CreateHerramienta::route('/create'),
            'edit' => Pages\EditHerramienta::route('/{record}/edit'),
        ];
    }
}

