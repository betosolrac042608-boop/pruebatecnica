<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AccionProgramadaResource\Pages;
use App\Filament\Concerns\HideFromOperarioSupervisor;
use App\Models\AccionProgramada;
use App\Models\Animal;
use App\Models\Cultivo;
use App\Models\Herramienta;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AccionProgramadaResource extends Resource
{
    use HideFromOperarioSupervisor;
    protected static ?string $model = AccionProgramada::class;

    protected static ?string $navigationIcon = 'heroicon-o-calendar-days';
    protected static ?string $navigationLabel = 'Acciones Programadas';
    protected static ?string $modelLabel = 'Acción Programada';
    protected static ?string $pluralModelLabel = 'Acciones Programadas';
    protected static ?string $navigationGroup = 'Gestión Operativa';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información de la Acción')
                    ->description('Detalles de la acción a programar')
                    ->schema([
                        Forms\Components\TextInput::make('nombre_accion')
                            ->label('Nombre de la Acción')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('Ej: Riego programado semanal')
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
                    ->description('Seleccione el activo relacionado con esta acción')
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
                            ->placeholder('Seleccione el tipo')
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
                            ->minDate(now())
                            ->placeholder('Seleccione la fecha')
                            ->validationMessages([
                                'required' => 'La fecha programada es obligatoria.',
                                'after_or_equal' => 'La fecha debe ser actual o futura.',
                            ]),
                        
                        Forms\Components\TimePicker::make('hora_programada')
                            ->label('Hora Programada')
                            ->required()
                            ->seconds(false)
                            ->placeholder('Seleccione la hora')
                            ->validationMessages([
                                'required' => 'La hora programada es obligatoria.',
                            ]),
                        
                        Forms\Components\Textarea::make('notas')
                            ->label('Notas')
                            ->rows(4)
                            ->columnSpanFull()
                            ->placeholder('Instrucciones o notas adicionales para la acción programada'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Estado')
                    ->schema([
                        Forms\Components\Toggle::make('completed')
                            ->label('Completada')
                            ->default(false)
                            ->live()
                            ->helperText('Marque si la acción ya fue completada'),
                        
                        Forms\Components\DateTimePicker::make('completed_at')
                            ->label('Fecha de Completación')
                            ->native(false)
                            ->displayFormat('d/m/Y H:i')
                            ->visible(fn (Forms\Get $get): bool => $get('completed'))
                            ->disabled()
                            ->helperText('Se registra automáticamente al marcar como completada'),
                    ])->columns(2)
                    ->hidden(fn (string $operation): bool => $operation === 'create'),
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
                    ->color('primary')
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
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('usuario.name')
                    ->label('Responsable')
                    ->sortable()
                    ->searchable(),
                
                Tables\Columns\TextColumn::make('fecha_programada')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable()
                    ->color(fn ($record) => $record->fecha_programada->isPast() && !$record->completed ? 'danger' : 'success'),
                
                Tables\Columns\TextColumn::make('hora_programada')
                    ->label('Hora')
                    ->time('H:i')
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('completed')
                    ->label('Estado')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-clock')
                    ->trueColor('success')
                    ->falseColor('warning')
                    ->tooltip(fn ($record): string => $record->completed ? 'Completada' : 'Pendiente'),
                
                Tables\Columns\TextColumn::make('completed_at')
                    ->label('Completada el')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->placeholder('-')
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
                
                Tables\Filters\TernaryFilter::make('completed')
                    ->label('Estado')
                    ->placeholder('Todas')
                    ->trueLabel('Completadas')
                    ->falseLabel('Pendientes'),
                
                Tables\Filters\Filter::make('vencidas')
                    ->label('Vencidas')
                    ->query(fn ($query) => $query->where('completed', false)->whereDate('fecha_programada', '<', now()))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Ver'),
                Tables\Actions\EditAction::make()
                    ->label('Editar')
                    ->color('warning'),
                Tables\Actions\Action::make('completar')
                    ->label('Completar')
                    ->icon('heroicon-o-check-circle')
                    ->color('success')
                    ->requiresConfirmation()
                    ->hidden(fn ($record) => $record->completed)
                    ->action(function ($record) {
                        $record->update([
                            'completed' => true,
                            'completed_at' => now(),
                        ]);
                    }),
                Tables\Actions\DeleteAction::make()
                    ->label('Eliminar'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    Tables\Actions\BulkAction::make('marcarCompletadas')
                        ->label('Marcar como completadas')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records) {
                            $records->each->update([
                                'completed' => true,
                                'completed_at' => now(),
                            ]);
                        }),
                ]),
            ])
            ->defaultSort('fecha_programada', 'asc')
            ->emptyStateHeading('No hay acciones programadas')
            ->emptyStateDescription('Comienza programando tu primera acción.')
            ->emptyStateIcon('heroicon-o-calendar-days');
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
            'index' => Pages\ListAccionProgramadas::route('/'),
            'create' => Pages\CreateAccionProgramada::route('/create'),
            'edit' => Pages\EditAccionProgramada::route('/{record}/edit'),
        ];
    }
}

