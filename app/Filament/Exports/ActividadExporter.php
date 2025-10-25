<?php

namespace App\Filament\Exports;

use App\Models\Actividad;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ActividadExporter extends Exporter
{
    protected static ?string $model = Actividad::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('id')
                ->label('ID'),
            
            ExportColumn::make('created_at')
                ->label('Fecha de Registro'),
            
            ExportColumn::make('nombre_accion')
                ->label('Nombre de la Acción'),
            
            ExportColumn::make('descripcion')
                ->label('Descripción'),
            
            ExportColumn::make('tipoAccion.nombre')
                ->label('Tipo de Acción'),
            
            ExportColumn::make('entidad_type')
                ->label('Tipo de Entidad')
                ->formatStateUsing(fn ($state) => match($state) {
                    'App\Models\Animal' => 'Animal',
                    'App\Models\Cultivo' => 'Cultivo',
                    'App\Models\Herramienta' => 'Herramienta',
                    default => 'N/A',
                }),
            
            ExportColumn::make('entidad_id')
                ->label('ID Entidad'),
            
            ExportColumn::make('usuario.name')
                ->label('Responsable'),
            
            ExportColumn::make('usuario.email')
                ->label('Email Responsable'),
            
            ExportColumn::make('estado.nombre')
                ->label('Estado'),
            
            ExportColumn::make('fecha_programada')
                ->label('Fecha Programada'),
            
            ExportColumn::make('hora_programada')
                ->label('Hora Programada'),
            
            ExportColumn::make('fecha_realizada')
                ->label('Fecha Realizada'),
            
            ExportColumn::make('hora_realizada')
                ->label('Hora Realizada'),
            
            ExportColumn::make('notas')
                ->label('Notas'),
            
            ExportColumn::make('from_scheduled')
                ->label('Programada Automáticamente')
                ->formatStateUsing(fn ($state) => $state ? 'Sí' : 'No'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'Se ha completado la exportación de ' . number_format($export->successful_rows) . ' ' . str('actividad')->plural($export->successful_rows) . '.';

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('actividad')->plural($failedRowsCount) . ' no se pudieron exportar.';
        }

        return $body;
    }
}

