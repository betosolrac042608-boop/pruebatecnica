<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TipoAccionResource\Pages;
use App\Models\TipoAccion;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TipoAccionResource extends Resource
{
    protected static ?string $model = TipoAccion::class;

    protected static ?string $navigationIcon = 'heroicon-o-tag';
    protected static ?string $navigationLabel = 'Tipos de Acción';
    protected static ?string $modelLabel = 'Tipo de Acción';
    protected static ?string $pluralModelLabel = 'Tipos de Acción';
    protected static ?string $navigationGroup = 'Catálogos';
    protected static ?int $navigationSort = 3;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información del Tipo de Acción')
                    ->schema([
                        Forms\Components\TextInput::make('nombre')
                            ->label('Nombre')
                            ->required()
                            ->maxLength(100)
                            ->unique(ignoreRecord: true)
                            ->placeholder('Ej: Vacunación')
                            ->validationMessages([
                                'required' => 'El nombre es obligatorio.',
                                'unique' => 'Este tipo de acción ya existe.',
                            ]),
                        
                        Forms\Components\Textarea::make('descripcion')
                            ->label('Descripción')
                            ->rows(4)
                            ->maxLength(255)
                            ->placeholder('Descripción del tipo de acción')
                            ->columnSpanFull(),
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
                    ->color('primary'),
                
                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->searchable()
                    ->wrap()
                    ->placeholder('Sin descripción'),
                
                Tables\Columns\TextColumn::make('actividades_count')
                    ->label('Actividades')
                    ->counts('actividades')
                    ->sortable()
                    ->badge()
                    ->color('info'),
                
                Tables\Columns\TextColumn::make('accionesProgramadas_count')
                    ->label('Acciones Programadas')
                    ->counts('accionesProgramadas')
                    ->sortable()
                    ->badge()
                    ->color('warning'),
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
            ->emptyStateHeading('No hay tipos de acción configurados')
            ->emptyStateDescription('Comienza creando tu primer tipo de acción.')
            ->emptyStateIcon('heroicon-o-tag');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ManageTipoAcciones::route('/'),
        ];
    }
}

