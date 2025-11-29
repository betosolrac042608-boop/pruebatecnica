<?php

namespace App\Filament\Resources\PlanTrabajoDiarioResource\RelationManagers;

use App\Models\Zona;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Components\FileUpload;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class FotosRelationManager extends RelationManager
{
    protected static string $relationship = 'fotos';

    protected function canCreate(): bool
    {
        return $this->userCanHandlePhotos();
    }

    protected function canDelete(Model $record): bool
    {
        return $this->userCanHandlePhotos();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('zona_id')
                    ->label('Zona')
                    ->relationship('zona', 'nombre')
                    ->required()
                    ->searchable(),
                Forms\Components\Select::make('tipo')
                    ->label('Tipo de foto')
                    ->required()
                    ->options([
                        'antes' => 'Antes',
                        'despues' => 'Después',
                    ]),
                FileUpload::make('ruta')
                    ->label('Imagen (ruta)')
                    ->directory('planes-trabajo')
                    ->image()
                    ->required(),
                Forms\Components\Textarea::make('metadata')
                    ->label('Metadatos')
                    ->rows(2)
                    ->hint('Puede contener etiquetas JSON extraídas'),
                Forms\Components\DateTimePicker::make('tomada_en')
                    ->label('Fecha y hora')
                    ->required()
                    ->default(now()),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('zona.nombre')
                    ->label('Zona')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('tipo')
                    ->label('Tipo')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'antes' => 'Antes',
                        'despues' => 'Después',
                        default => $state,
                    })
                    ->color(fn (string $state): string => match ($state) {
                        'antes' => 'info',
                        'despues' => 'success',
                        default => 'gray',
                    })
                    ->sortable(),
                Tables\Columns\ImageColumn::make('ruta')
                    ->label('Foto'),
                Tables\Columns\TextColumn::make('tomada_en')
                    ->label('Tomada en')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('zona_id')
                    ->label('Zona')
                    ->relationship('zona', 'nombre'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->visible(fn () => $this->userCanHandlePhotos()),
            ])
            ->actions([
                Tables\Actions\DeleteAction::make()
                    ->visible(fn () => $this->userCanHandlePhotos()),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    private function userCanHandlePhotos(): bool
    {
        $user = Filament::auth()->user();

        if (! $user || ! $user->rol) {
            return false;
        }

        return in_array($user->rol->nombre, ['Supervisor', 'Operador']);
    }
}
