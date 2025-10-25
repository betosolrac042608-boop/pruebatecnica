<?php

namespace App\Filament\Widgets;

use App\Models\Animal;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;

class AnimalesPorEspecieChart extends ChartWidget
{
    protected static ?string $heading = 'Distribución de Animales por Especie';
    
    protected static ?int $sort = 4;
    
    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        // Obtener la distribución de animales por especie
        $distribucion = Animal::select('especie', DB::raw('count(*) as total'))
            ->whereNull('deleted_at')
            ->groupBy('especie')
            ->orderBy('total', 'desc')
            ->get();

        $especies = $distribucion->pluck('especie')->toArray();
        $totales = $distribucion->pluck('total')->toArray();

        // Colores para el gráfico
        $colores = [
            'rgb(16, 185, 129)', // Emerald
            'rgb(59, 130, 246)', // Blue
            'rgb(251, 146, 60)', // Orange
            'rgb(168, 85, 247)', // Purple
            'rgb(236, 72, 153)', // Pink
            'rgb(234, 179, 8)', // Yellow
            'rgb(239, 68, 68)', // Red
            'rgb(20, 184, 166)', // Teal
        ];

        return [
            'datasets' => [
                [
                    'label' => 'Animales por Especie',
                    'data' => $totales,
                    'backgroundColor' => array_slice($colores, 0, count($especies)),
                    'borderColor' => 'rgb(255, 255, 255)',
                    'borderWidth' => 2,
                ],
            ],
            'labels' => $especies,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }

    protected function getOptions(): array
    {
        return [
            'plugins' => [
                'legend' => [
                    'display' => true,
                    'position' => 'bottom',
                    'labels' => [
                        'usePointStyle' => true,
                        'padding' => 15,
                        'font' => [
                            'size' => 12,
                            'weight' => '500',
                        ],
                    ],
                ],
                'tooltip' => [
                    'enabled' => true,
                    'callbacks' => [
                        'label' => 'function(context) {
                            let label = context.label || "";
                            let value = context.parsed || 0;
                            let total = context.dataset.data.reduce((a, b) => a + b, 0);
                            let percentage = ((value / total) * 100).toFixed(1);
                            return label + ": " + value + " (" + percentage + "%)";
                        }',
                    ],
                ],
            ],
            'maintainAspectRatio' => true,
            'responsive' => true,
        ];
    }
}

