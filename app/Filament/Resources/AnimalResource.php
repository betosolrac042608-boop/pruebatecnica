<?php

namespace App\Filament\Resources;

use App\Filament\Resources\AnimalResource\Pages;
use App\Models\Animal;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class AnimalResource extends Resource
{
    protected static ?string $model = Animal::class;

    protected static ?string $navigationIcon = 'heroicon-o-home';
    protected static ?string $navigationLabel = 'Animales';
    protected static ?string $modelLabel = 'Animal';
    protected static ?string $pluralModelLabel = 'Animales';
    protected static ?string $navigationGroup = 'Gestión de Activos';
    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información General')
                    ->description('Datos básicos del animal')
                    ->schema([
                        Forms\Components\TextInput::make('matricula')
                            ->label('Matrícula')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(50)
                            ->placeholder('Ej: AN-001')
                            ->alphaDash()
                            ->validationMessages([
                                'unique' => 'Esta matrícula ya está en uso.',
                                'required' => 'La matrícula es obligatoria.',
                            ]),
                        
                        Forms\Components\TextInput::make('nombre')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(100)
                            ->placeholder('Ej: Bessy')
                            ->validationMessages([
                                'required' => 'El nombre es obligatorio.',
                            ]),
                        
                        Forms\Components\TextInput::make('especie')
                            ->label('Especie')
                            ->required()
                            ->maxLength(50)
                            ->placeholder('Ej: Vacuno')
                            ->datalist([
                                'Vacuno',
                                'Porcino',
                                'Ovino',
                                'Caprino',
                                'Equino',
                                'Aviar',
                            ]),
                        
                        Forms\Components\TextInput::make('raza')
                            ->label('Raza')
                            ->maxLength(50)
                            ->placeholder('Ej: Holstein'),
                        
                        Forms\Components\Select::make('sexo')
                            ->label('Sexo')
                            ->required()
                            ->options([
                                'M' => 'Macho',
                                'H' => 'Hembra',
                            ])
                            ->native(false)
                            ->placeholder('Seleccione el sexo'),
                        
                        Forms\Components\Select::make('estado')
                            ->label('Estado')
                            ->required()
                            ->options([
                                'Activo' => 'Activo',
                                'Enfermo' => 'Enfermo',
                                'En Cuarentena' => 'En Cuarentena',
                                'Vendido' => 'Vendido',
                                'Fallecido' => 'Fallecido',
                            ])
                            ->native(false)
                            ->default('Activo')
                            ->placeholder('Seleccione el estado'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Datos Físicos y Ubicación')
                    ->schema([
                        Forms\Components\TextInput::make('peso')
                            ->label('Peso (kg)')
                            ->numeric()
                            ->suffix('kg')
                            ->placeholder('Ej: 450')
                            ->minValue(0)
                            ->step(0.01),
                        
                        Forms\Components\TextInput::make('ubicacion')
                            ->label('Ubicación')
                            ->maxLength(100)
                            ->placeholder('Ej: Corral Norte'),
                        
                        Forms\Components\DatePicker::make('fecha_nacimiento')
                            ->label('Fecha de Nacimiento')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->maxDate(now())
                            ->placeholder('Seleccione la fecha')
                            ->validationMessages([
                                'required' => 'La fecha de nacimiento es obligatoria.',
                            ]),
                        
                        Forms\Components\DatePicker::make('fecha_adquisicion')
                            ->label('Fecha de Adquisición')
                            ->required()
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->maxDate(now())
                            ->default(now())
                            ->placeholder('Seleccione la fecha'),
                    ])->columns(2),
                
                Forms\Components\Section::make('Seguimiento Sanitario')
                    ->schema([
                        Forms\Components\DatePicker::make('ultima_revision')
                            ->label('Última Revisión')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->maxDate(now())
                            ->placeholder('Seleccione la fecha'),
                        
                        Forms\Components\DatePicker::make('ultima_vacuna')
                            ->label('Última Vacunación')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->maxDate(now())
                            ->placeholder('Seleccione la fecha'),
                        
                        Forms\Components\Textarea::make('observaciones')
                            ->label('Observaciones')
                            ->rows(3)
                            ->columnSpanFull()
                            ->placeholder('Notas adicionales sobre el animal'),
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
                    ->color('primary')
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                
                Tables\Columns\TextColumn::make('especie')
                    ->label('Especie')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('raza')
                    ->label('Raza')
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),
                
                Tables\Columns\TextColumn::make('sexo')
                    ->label('Sexo')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'M' => 'Macho',
                        'H' => 'Hembra',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'M' => 'blue',
                        'H' => 'pink',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('peso')
                    ->label('Peso')
                    ->numeric(decimalPlaces: 2)
                    ->suffix(' kg')
                    ->sortable()
                    ->placeholder('-'),
                
                Tables\Columns\TextColumn::make('estado')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Activo' => 'success',
                        'Enfermo' => 'warning',
                        'En Cuarentena' => 'danger',
                        'Vendido' => 'info',
                        'Fallecido' => 'gray',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('ubicacion')
                    ->label('Ubicación')
                    ->searchable()
                    ->icon('heroicon-m-map-pin')
                    ->placeholder('-')
                    ->toggleable(),
                
                Tables\Columns\TextColumn::make('fecha_nacimiento')
                    ->label('Nacimiento')
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
                Tables\Filters\SelectFilter::make('especie')
                    ->label('Especie')
                    ->options([
                        'Vacuno' => 'Vacuno',
                        'Porcino' => 'Porcino',
                        'Ovino' => 'Ovino',
                        'Caprino' => 'Caprino',
                        'Equino' => 'Equino',
                        'Aviar' => 'Aviar',
                    ])
                    ->multiple()
                    ->preload(),
                
                Tables\Filters\SelectFilter::make('estado')
                    ->label('Estado')
                    ->options([
                        'Activo' => 'Activo',
                        'Enfermo' => 'Enfermo',
                        'En Cuarentena' => 'En Cuarentena',
                        'Vendido' => 'Vendido',
                        'Fallecido' => 'Fallecido',
                    ])
                    ->multiple(),
                
                Tables\Filters\SelectFilter::make('sexo')
                    ->label('Sexo')
                    ->options([
                        'M' => 'Macho',
                        'H' => 'Hembra',
                    ]),
                
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
            ->emptyStateHeading('No hay animales registrados')
            ->emptyStateDescription('Comienza agregando tu primer animal.')
            ->emptyStateIcon('heroicon-o-home');
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
            'index' => Pages\ListAnimals::route('/'),
            'create' => Pages\CreateAnimal::route('/create'),
            'edit' => Pages\EditAnimal::route('/{record}/edit'),
        ];
    }
}

