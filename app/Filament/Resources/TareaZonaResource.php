<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TareaZonaResource\Pages;
use App\Filament\Resources\TareaZonaResource\RelationManagers;
use App\Filament\Concerns\HideFromOperarioSupervisor;
use App\Models\TareaZona;
use App\Models\Zona;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;

class TareaZonaResource extends Resource
{
    use HideFromOperarioSupervisor;
    protected static ?string $model = TareaZona::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationLabel = 'Tareas por Zona';
    protected static ?string $modelLabel = 'Tarea por Zona';
    protected static ?string $pluralModelLabel = 'Tareas por Zona';
    protected static ?string $navigationGroup = 'Gestión de Activos';
    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Relaciones')
                    ->schema([
                        Forms\Components\Select::make('predio_id')
                            ->label('Predio')
                            ->relationship('predio', 'nombre')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->reactive()
                            ->columnSpan(2),

                        Forms\Components\Select::make('zona_id')
                            ->label('Zona')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->options(function (callable $get) {
                                $predioId = $get('predio_id');
                                if (! $predioId) {
                                    return Zona::query()->pluck('nombre', 'id');
                                }
                                return Zona::where('predio_id', $predioId)->pluck('nombre', 'id');
                            })
                            ->columnSpan(2),
                    ]),

                Forms\Components\Section::make('Identidad')
                    ->schema([
                        Forms\Components\TextInput::make('clave')
                            ->required()
                            ->maxLength(50),
                        Forms\Components\TextInput::make('nombre')
                            ->required()
                            ->maxLength(150),
                        Forms\Components\Textarea::make('descripcion')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('objetivo')
                            ->columnSpanFull(),
                        Forms\Components\Textarea::make('tareas_sugeridas')
                            ->columnSpanFull(),
                        Forms\Components\TextInput::make('frecuencia')
                            ->maxLength(150),
                        Forms\Components\TextInput::make('tiempo_minutos')
                            ->numeric(),
                        Forms\Components\Toggle::make('activo')
                            ->required(),
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
                Tables\Columns\TextColumn::make('zona.nombre')
                    ->label('Zona')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('clave')
                    ->searchable(),
                Tables\Columns\TextColumn::make('nombre')
                    ->searchable(),
                Tables\Columns\TextColumn::make('frecuencia')
                    ->searchable(),
                Tables\Columns\TextColumn::make('tiempo_minutos')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\IconColumn::make('activo')
                    ->boolean(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('predio_id')
                    ->label('Predio')
                    ->relationship('predio', 'nombre')
                    ->multiple()
                    ->preload(),
                SelectFilter::make('zona_id')
                    ->label('Zona')
                    ->relationship('zona', 'nombre')
                    ->multiple()
                    ->preload(),
                TernaryFilter::make('activo')
                    ->label('Activo')
                    ->placeholder('Todas')
                    ->trueLabel('Solo activas')
                    ->falseLabel('Solo inactivas'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListTareaZonas::route('/'),
            'create' => Pages\CreateTareaZona::route('/create'),
            'edit' => Pages\EditTareaZona::route('/{record}/edit'),
        ];
    }
}
