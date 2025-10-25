<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ActividadResource\Pages;
use App\Models\Actividad;
use App\Models\Animal;
use App\Models\Cultivo;
use App\Models\Herramienta;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ActividadResource extends Resource
{
    protected static ?string $model = Actividad::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Actividades';
    protected static ?string $modelLabel = 'Actividad';
    protected static ?string $pluralModelLabel = 'Actividades';
    protected static ?string $navigationGroup = 'Gestión Operativa';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Actividad')
                    ->description('Detalles principales de la actividad')
                    ->schema([
                        Forms\Components\TextInput::make('nombre_accion')
                            ->label('Nombre de la Acción')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('Ej: Vacunación anual')
                            ->columnSpanFull()
                            ->validationMessages([
                                'required' => 'El nombre de la acción es obligatorio.',
                            ]),
                        
                        Forms\Components\Select::make('tipo_accion_id')
                            ->label('Tipo de Acción')
                            ->relationship('tipoAccion', 'nombre')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->placeholder('Seleccione el tipo')
                            ->validationMessages([
                                'required' => 'El tipo de acción es obligatorio.',
                            ]),
                        
                        Forms\Components\Select::make('estado_id')
                            ->label('Estado')
                            ->relationship('estado', 'nombre')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->default(function() {
                                return \App\Models\EstadoActividad::where('nombre', 'Pendiente')->first()?->id;
                            })
                            ->placeholder('Seleccione el estado')
                            ->validationMessages([
                                'required' => 'El estado es obligatorio.',
                            ]),
                        
                        Forms\Components\Select::make('usuario_id')
                            ->label('Responsable')
                            ->relationship('usuario', 'name')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->placeholder('Seleccione el responsable')
                            ->validationMessages([
                                'required' => 'Debe asignar un responsable.',
                            ]),
                    ])->columns(2),
                
                Forms\Components\Section::make('Entidad Relacionada')
                    ->description('Seleccione el activo relacionado con esta actividad')
                    ->schema([
                        Forms\Components\Select::make('entidad_type')
                            ->label('Tipo de Entidad')
                            ->required()
                            ->options([
                                'App\Models\Animal' => 'Animal',
                                'App\Models\Cultivo' => 'Cultivo',
                                'App\Models\Herramienta' => 'Herramienta',
                            ])
                            ->native(false)
                            ->live()
                            ->reactive()
                            ->afterStateUpdated(fn ($state, callable $set) => $set('entidad_id', null))
                            ->placeholder('Primero seleccione el tipo')
                            ->helperText('Seleccione el tipo de activo (Animal, Cultivo o Herramienta)')
                            ->validationMessages([
                                'required' => 'Debe seleccionar el tipo de entidad.',
                            ]),
                        
                        Forms\Components\Select::make('entidad_id')
                            ->label('Entidad')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->options(function (Forms\Get $get) {
                                $type = $get('entidad_type');
                                if (!$type) {
                                    return [];
                                }
                                
                                return match($type) {
                                    'App\Models\Animal' => Animal::pluck('nombre', 'id'),
                                    'App\Models\Cultivo' => Cultivo::pluck('nombre', 'id'),
                                    'App\Models\Herramienta' => Herramienta::pluck('nombre', 'id'),
                                    default => [],
                                };
                            })
                            ->placeholder('Primero seleccione el tipo')
                            ->validationMessages([
                                'required' => 'Debe seleccionar una entidad.',
                            ]),
                    ])->columns(2),
                
                Forms\Components\Section::make('Programación')
                    ->schema([
                        Forms\Components\DatePicker::make('fecha_programada')
                            ->label('Fecha Programada')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->placeholder('Seleccione la fecha')
                            ->validationMessages([
                                'required' => 'La fecha programada es obligatoria.',
                            ]),
                        
                        Forms\Components\TimePicker::make('hora_programada')
                            ->label('Hora Programada')
                            ->seconds(false)
                            ->placeholder('Seleccione la hora'),
                        
                        Forms\Components\DatePicker::make('fecha_realizada')
                            ->label('Fecha de Realización')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->maxDate(now())
                            ->placeholder('Seleccione la fecha'),
                        
                        Forms\Components\TimePicker::make('hora_realizada')
                            ->label('Hora de Realización')
                            ->seconds(false)
                            ->placeholder('Seleccione la hora'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Detalles Adicionales')
                    ->schema([
                        Forms\Components\Textarea::make('descripcion')
                            ->label('Descripción')
                            ->rows(3)
                            ->placeholder('Descripción detallada de la actividad'),
                        
                        Forms\Components\Textarea::make('notas')
                            ->label('Notas')
                            ->rows(3)
                            ->placeholder('Observaciones o notas adicionales'),
                        
                        Forms\Components\Toggle::make('from_scheduled')
                            ->label('Proviene de Acción Programada')
                            ->default(false)
                            ->disabled()
                            ->helperText('Indica si esta actividad fue generada automáticamente'),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre_accion')
                    ->label('Acción')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap(),
                
                Tables\Columns\TextColumn::make('tipoAccion.nombre')
                    ->label('Tipo')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('entidad_type')
                    ->label('Entidad')
                    ->formatStateUsing(function ($state) {
                        return match($state) {
                            'App\Models\Animal' => 'Animal',
                            'App\Models\Cultivo' => 'Cultivo',
                            'App\Models\Herramienta' => 'Herramienta',
                            default => 'N/A',
                        };
                    })
                    ->badge()
                    ->color('warning'),
                
                Tables\Columns\TextColumn::make('usuario.name')
                    ->label('Responsable')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('estado.nombre')
                    ->label('Estado')
                    ->badge()
                    ->color(fn ($record): string => match ($record->estado?->nombre) {
                        'Pendiente' => 'warning',
                        'En Proceso' => 'info',
                        'Completada' => 'success',
                        'Cancelada' => 'danger',
                        default => 'gray',
                    })
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('fecha_programada')
                    ->label('Fecha Programada')
                    ->date('d/m/Y')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('fecha_realizada')
                    ->label('Fecha Realizada')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('-')
                    ->toggleable(),
                
                Tables\Columns\IconColumn::make('from_scheduled')
                    ->label('Auto')
                    ->boolean()
                    ->trueIcon('heroicon-o-clock')
                    ->falseIcon('heroicon-o-pencil')
                    ->trueColor('info')
                    ->falseColor('gray')
                    ->tooltip(fn ($record): string => $record->from_scheduled ? 'Actividad programada automática' : 'Actividad manual')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creada')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('tipo_accion_id')
                    ->label('Tipo de Acción')
                    ->relationship('tipoAccion', 'nombre')
                    ->multiple()
                    ->preload(),
                
                Tables\Filters\SelectFilter::make('estado_id')
                    ->label('Estado')
                    ->relationship('estado', 'nombre')
                    ->multiple()
                    ->preload(),
                
                Tables\Filters\SelectFilter::make('usuario_id')
                    ->label('Responsable')
                    ->relationship('usuario', 'name')
                    ->multiple()
                    ->preload(),
                
                Tables\Filters\SelectFilter::make('entidad_type')
                    ->label('Tipo de Entidad')
                    ->options([
                        'App\Models\Animal' => 'Animal',
                        'App\Models\Cultivo' => 'Cultivo',
                        'App\Models\Herramienta' => 'Herramienta',
                    ])
                    ->multiple(),
                
                Tables\Filters\TernaryFilter::make('from_scheduled')
                    ->label('Origen')
                    ->placeholder('Todas')
                    ->trueLabel('Programadas')
                    ->falseLabel('Manuales'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Ver'),
                Tables\Actions\EditAction::make()
                    ->label('Editar')
                    ->color('warning'),
                Tables\Actions\DeleteAction::make()
                    ->label('Eliminar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('fecha_programada', 'desc')
            ->emptyStateHeading('No hay actividades registradas')
            ->emptyStateDescription('Comienza creando tu primera actividad.')
            ->emptyStateIcon('heroicon-o-clipboard-document-check');
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
            'index' => Pages\ListActividades::route('/'),
            'create' => Pages\CreateActividad::route('/create'),
            'edit' => Pages\EditActividad::route('/{record}/edit'),
        ];
    }
}

