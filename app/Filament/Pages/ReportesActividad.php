<?php

namespace App\Filament\Pages;

use App\Models\Actividad;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Illuminate\Contracts\View\View;

class ReportesActividad extends Page implements HasTable
{
    use InteractsWithTable;

    protected static ?string $navigationIcon = 'heroicon-o-document-chart-bar';
    
    protected static string $view = 'filament.pages.reportes-actividad';
    
    protected static ?string $navigationLabel = 'Reportes de Actividad';
    
    protected static ?string $title = 'Reportes de Actividad';
    
    protected static ?string $navigationGroup = 'Reportes';
    
    protected static ?int $navigationSort = 1;

    public function table(Table $table): Table
    {
        return $table
            ->query(Actividad::query()->with(['usuario', 'estado', 'tipoAccion', 'entidad']))
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha de Registro')
                    ->dateTime('d/m/Y H:i')
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('nombre_accion')
                    ->label('Acción')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap(),
                
                Tables\Columns\TextColumn::make('tipoAccion.nombre')
                    ->label('Tipo de Acción')
                    ->badge()
                    ->color('info')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('entidad_type')
                    ->label('Tipo Entidad')
                    ->formatStateUsing(function ($state) {
                        return $state ? match($state) {
                            'App\Models\Animal' => 'Animal',
                            'App\Models\Cultivo' => 'Cultivo',
                            'App\Models\Herramienta' => 'Herramienta',
                            default => 'N/A',
                        } : 'N/A';
                    })
                    ->badge()
                    ->color('warning'),
                
                Tables\Columns\TextColumn::make('usuario.name')
                    ->label('Responsable')
                    ->sortable()
                    ->searchable()
                    ->placeholder('Sin asignar'),
                
                Tables\Columns\TextColumn::make('estado.nombre')
                    ->label('Estado')
                    ->badge()
                    ->placeholder('Sin estado')
                    ->color(fn (?string $state): string => match ($state) {
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
                    ->sortable()
                    ->placeholder('-'),
                
                Tables\Columns\TextColumn::make('fecha_realizada')
                    ->label('Fecha Realizada')
                    ->date('d/m/Y')
                    ->sortable()
                    ->placeholder('-'),
                
                Tables\Columns\TextColumn::make('descripcion')
                    ->label('Descripción')
                    ->limit(50)
                    ->placeholder('-')
                    ->toggleable(),
                
                Tables\Columns\IconColumn::make('from_scheduled')
                    ->label('Programada')
                    ->boolean()
                    ->trueIcon('heroicon-o-clock')
                    ->falseIcon('heroicon-o-pencil')
                    ->trueColor('info')
                    ->falseColor('gray')
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\Filter::make('fecha_rango')
                    ->form([
                        DatePicker::make('fecha_desde')
                            ->label('Desde')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->placeholder('Fecha inicial'),
                        DatePicker::make('fecha_hasta')
                            ->label('Hasta')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->placeholder('Fecha final'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['fecha_desde'],
                                fn ($query, $date) => $query->whereDate('created_at', '>=', $date)
                            )
                            ->when(
                                $data['fecha_hasta'],
                                fn ($query, $date) => $query->whereDate('created_at', '<=', $date)
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['fecha_desde'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make('Desde: ' . \Carbon\Carbon::parse($data['fecha_desde'])->format('d/m/Y'))
                                ->removeField('fecha_desde');
                        }
                        if ($data['fecha_hasta'] ?? null) {
                            $indicators[] = Tables\Filters\Indicator::make('Hasta: ' . \Carbon\Carbon::parse($data['fecha_hasta'])->format('d/m/Y'))
                                ->removeField('fecha_hasta');
                        }
                        return $indicators;
                    }),
                
                Tables\Filters\SelectFilter::make('tipo_accion_id')
                    ->label('Tipo de Acción')
                    ->relationship('tipoAccion', 'nombre')
                    ->multiple()
                    ->preload()
                    ->searchable(),
                
                Tables\Filters\SelectFilter::make('estado_id')
                    ->label('Estado')
                    ->relationship('estado', 'nombre')
                    ->multiple()
                    ->preload(),
                
                Tables\Filters\SelectFilter::make('usuario_id')
                    ->label('Responsable')
                    ->relationship('usuario', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable(),
                
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
                
                Tables\Filters\Filter::make('completadas')
                    ->label('Solo Completadas')
                    ->query(fn ($query) => $query->whereHas('estado', fn($q) => $q->where('nombre', 'Completada')))
                    ->toggle(),
                
                Tables\Filters\Filter::make('pendientes')
                    ->label('Solo Pendientes')
                    ->query(fn ($query) => $query->whereHas('estado', fn($q) => $q->where('nombre', 'Pendiente')))
                    ->toggle(),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Ver Detalle')
                    ->modalHeading('Detalle de la Actividad')
                    ->modalWidth('lg'),
            ])
            ->headerActions([
                Tables\Actions\ExportAction::make()
                    ->label('Exportar a CSV')
                    ->icon('heroicon-o-arrow-down-tray')
                    ->color('success')
                    ->exporter(\App\Filament\Exports\ActividadExporter::class),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\ExportBulkAction::make()
                        ->label('Exportar Seleccionados')
                        ->exporter(\App\Filament\Exports\ActividadExporter::class),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->emptyStateHeading('No hay actividades para mostrar')
            ->emptyStateDescription('Ajusta los filtros o crea nuevas actividades')
            ->emptyStateIcon('heroicon-o-document-chart-bar');
    }
}

