<?php

namespace App\Filament\Resources;

use App\Filament\Resources\EstadoActividadResource\Pages;
use App\Models\EstadoActividad;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class EstadoActividadResource extends Resource
{
    protected static ?string $model = EstadoActividad::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Estados de Actividad';
    protected static ?string $modelLabel = 'Estado de Actividad';
    protected static ?string $pluralModelLabel = 'Estados de Actividad';
    protected static ?string $navigationGroup = 'Catálogos';
    protected static ?int $navigationSort = 2;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Estado')
                    ->schema([
                        Forms\Components\TextInput::make('nombre')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(50)
                            ->unique(ignoreRecord: true)
                            ->placeholder('Ej: Pendiente')
                            ->validationMessages([
                                'required' => 'El nombre es obligatorio.',
                                'unique' => 'Este estado ya existe.',
                            ]),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('nombre')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pendiente' => 'warning',
                        'En Proceso' => 'info',
                        'Completada' => 'success',
                        'Cancelada' => 'danger',
                        default => 'gray',
                    }),
                
                Tables\Columns\TextColumn::make('actividades_count')
                    ->label('Actividades')
                    ->counts('actividades')
                    ->sortable()
                    ->badge()
                    ->color('info'),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            ->emptyStateHeading('No hay estados configurados')
            ->emptyStateDescription('Comienza creando tu primer estado.')
            ->emptyStateIcon('heroicon-o-clipboard-document-list');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageEstadoActividades::route('/'),
        ];
    }
}

