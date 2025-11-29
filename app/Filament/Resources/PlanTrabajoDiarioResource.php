<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanTrabajoDiarioResource\Pages;
use App\Filament\Resources\PlanTrabajoDiarioResource\RelationManagers;
use App\Filament\Concerns\HideFromOperarioSupervisor;
use App\Models\PlanTrabajoDiario;
use App\Services\PlanTrabajoService;
use Filament\Facades\Filament;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PlanTrabajoDiarioResource extends Resource
{
    use HideFromOperarioSupervisor;
    protected static ?string $model = PlanTrabajoDiario::class;

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';
    protected static ?string $navigationLabel = 'Planes de Trabajo';
    protected static ?string $modelLabel = 'Plan Diario';
    protected static ?string $pluralModelLabel = 'Planes Diarios';
    protected static ?string $navigationGroup = 'Gestión de Activos';
    protected static ?int $navigationSort = 6;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Información general')
                    ->schema([
                        Forms\Components\Select::make('predio_id')
                            ->label('Predio')
                            ->placeholder('Selecciona un predio')
                            ->relationship('predio', 'nombre')
                            ->searchable()
                            ->required(),
                        Forms\Components\Select::make('usuario_id')
                            ->label('Encargado')
                            ->placeholder('Selecciona al usuario responsable')
                            ->relationship('usuario', 'name', fn ($query) => $query
                                ->where('active', true)
                                ->whereHas('rol', fn ($rol) => $rol->whereIn('nombre', [
                                    'Supervisor',
                                    'Operador',
                                ])))
                            ->searchable()
                            ->required()
                            ->helperText('Selecciona al usuario que ya tiene rol Supervisor u Operador para ejecutar el plan.'),
                        Forms\Components\Select::make('tipo_plan')
                            ->label('Tipo de plan')
                            ->options([
                                'diario' => 'Plan Diario',
                                'semanal' => 'Plan Semanal (7 días)',
                            ])
                            ->default('diario')
                            ->required()
                            ->reactive()
                            ->helperText('Selecciona si quieres crear un plan diario o semanal'),
                        Forms\Components\DatePicker::make('fecha')
                            ->label('Fecha del plan')
                            ->required(fn ($get) => $get('tipo_plan') === 'diario')
                            ->default(now())
                            ->displayFormat('d/m/Y')
                            ->visible(fn ($get) => $get('tipo_plan') === 'diario' || $get('tipo_plan') === null),
                        Forms\Components\DatePicker::make('fecha_inicio_semanal')
                            ->label('Fecha de inicio (semanal)')
                            ->required(fn ($get) => $get('tipo_plan') === 'semanal')
                            ->default(now())
                            ->displayFormat('d/m/Y')
                            ->helperText('Se crearán planes para los próximos 7 días a partir de esta fecha')
                            ->visible(fn ($get) => $get('tipo_plan') === 'semanal'),
                        Forms\Components\Select::make('estado')
                            ->label('Estado')
                            ->options([
                                'pendiente' => 'Pendiente',
                                'en_progreso' => 'En progreso',
                                'completado' => 'Completado',
                            ])
                            ->default('pendiente')
                            ->required(),
                    ]),
                Forms\Components\Section::make('Horarios')
                    ->schema([
                        Forms\Components\TextInput::make('turno_inicio')
                            ->label('Inicio de turno')
                            ->type('time')
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('turno_fin')
                            ->label('Fin de turno')
                            ->type('time')
                            ->columnSpan(2),
                        Forms\Components\TextInput::make('comida_inicio')
                            ->label('Inicio de comida')
                            ->type('time'),
                        Forms\Components\TextInput::make('comida_fin')
                            ->label('Fin de comida')
                            ->type('time'),
                    ]),
                Forms\Components\Section::make('Mensaje de GuardIAno')
                    ->schema([
                        Forms\Components\Textarea::make('resumen_ia')
                            ->label('Resumen y Mensaje de la IA')
                            ->columnSpanFull()
                            ->rows(5)
                            ->disabled()
                            ->helperText('Mensaje motivacional y resumen del plan generado por GuardIAno'),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('fecha', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('predio.nombre')
                    ->label('Predio')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('usuario.name')
                    ->label('Encargado')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('fecha')
                    ->label('Fecha')
                    ->date('d/m/Y')
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('estado')
                    ->label('Estado')
                    ->colors([
                        'danger' => 'pendiente',
                        'warning' => 'en_progreso',
                        'success' => 'completado',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'pendiente' => 'Pendiente',
                        'en_progreso' => 'En progreso',
                        'completado' => 'Completado',
                        default => $state,
                    })
                    ->sortable(),
                Tables\Columns\BadgeColumn::make('datos_gpt')
                    ->label('IA')
                    ->getStateUsing(fn ($record) => $record->datos_gpt ? 'Lista' : 'Pendiente')
                    ->colors([
                        'success' => 'Lista',
                        'warning' => 'Pendiente',
                    ]),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Creado')
                    ->dateTime('d/m/Y')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('predio_id')
                    ->label('Predio')
                    ->relationship('predio', 'nombre')
                    ->multiple()
                    ->preload(),
                TernaryFilter::make('estado')
                    ->label('Estado')
                    ->placeholder('Todos'),
            ])
            ->actions([
                Action::make('reenviar_gpt')
                    ->label('Reenviar a GPT')
                    ->icon('heroicon-o-arrow-path')
                    ->color('secondary')
                    ->requiresConfirmation()
                    ->modalHeading('Reenviar plan a IA')
                    ->modalSubheading('Esto volverá a enviar las tareas por zona a GPT y actualizará las tareas en la base.')
                    ->action(fn (PlanTrabajoDiario $record, PlanTrabajoService $service) => $service->generarPlan($record))
                    ->successNotificationTitle('Plan regenerado')
                    ->visible(fn (PlanTrabajoDiario $record) => $record->estado !== 'completado'),
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
            RelationManagers\TareasRelationManager::class,
            RelationManagers\FotosRelationManager::class,
            RelationManagers\EvaluacionesRelationManager::class,
            RelationManagers\LogsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPlanTrabajoDiarios::route('/'),
            'create' => Pages\CreatePlanTrabajoDiario::route('/create'),
            'edit' => Pages\EditPlanTrabajoDiario::route('/{record}/edit'),
        ];
    }
}
