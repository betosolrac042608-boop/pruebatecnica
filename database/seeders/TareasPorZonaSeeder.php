<?php

namespace Database\Seeders;

use App\Models\Predio;
use App\Models\TareaZona;
use App\Models\Zona;
use Illuminate\Database\Seeder;

class TareasPorZonaSeeder extends Seeder
{
    public function run(): void
    {
        $predio = Predio::where('codigo', 'P-QUINTA')->first();
        if (! $predio) {
            return;
        }

        $zonas = Zona::where('predio_id', $predio->id)->get()->keyBy('codigo');

        $tareas = [
            [
                'zona' => 'Z-GEN',
                'clave' => 'M-Aspers',
                'nombre' => 'Mantenimiento de aspersores',
                'descripcion' => 'Revisión completa de líneas y boquillas para evitar fugas y asegurar cobertura.',
                'objetivo' => 'Evitar zonas secas, proteger la madera cercana y mantener el sistema alineado.',
                'tareas_sugeridas' => 'Tomar fotos previas y posteriores; reportar aspersores obstruidos o dañados.',
                'frecuencia' => 'Cada 15 días',
                'tiempo_minutos' => 60,
            ],
            [
                'zona' => 'Z-CAB',
                'clave' => 'M-CAB',
                'nombre' => 'Limpieza integral de la cabaña y baño',
                'descripcion' => 'Lavar ropa de cama, limpiar muebles, quitar telarañas, trapear y revisar grifería.',
                'objetivo' => 'Mantener áreas de hospedaje listas para el siguiente huésped y evitar deterioros.',
                'tareas_sugeridas' => 'Tomar fotografías antes y después; confirmar fumigación y limpieza de boiler.',
                'frecuencia' => 'Cada vez que se renta la quinta',
                'tiempo_minutos' => 180,
            ],
            [
                'zona' => 'Z-CAB',
                'clave' => 'L-CAB',
                'nombre' => 'Limpieza, poda y mantenimiento del área de la cabaña',
                'descripcion' => 'Poda de jazmín, mezquites, césped y limpieza de piedra del piso y techos.',
                'objetivo' => 'Mantener la jardinería decorativa y el sendero libres de maleza.',
                'tareas_sugeridas' => 'Fotografiar jardines antes y después; confirmar fertilización y soplado de hojas.',
                'frecuencia' => 'Según tarea',
                'tiempo_minutos' => 0,
            ],
            [
                'zona' => 'Z-PAL',
                'clave' => 'L-FUE',
                'nombre' => 'Limpieza de la fuente de la palapa',
                'descripcion' => 'Limpiar y drenar la fuente; verificar bombas y desagües.',
                'objetivo' => 'Evitar estancamiento, hongos y asegurar que el agua esté cristalina.',
                'tareas_sugeridas' => 'Fotos del agua limpia; revisar drenado completo.',
                'frecuencia' => 'Cada que se rente la quinta',
                'tiempo_minutos' => 30,
            ],
            [
                'zona' => 'Z-PAL',
                'clave' => 'L-COC',
                'nombre' => 'Limpieza del cocedor de la palapa',
                'descripcion' => 'Quitar telarañas, revisar chimenea, limpiar interior con desinfectante.',
                'objetivo' => 'Evitar nidos de animales y tener la instalación lista para uso gastronómico.',
                'tareas_sugeridas' => 'Foto del interior limpio; confirmar eliminación de telarañas.',
                'frecuencia' => 'Cada 10 usos',
                'tiempo_minutos' => 10,
            ],
            [
                'zona' => 'Z-PAL',
                'clave' => 'L-PAL',
                'nombre' => 'Limpieza general de la palapa principal',
                'descripcion' => 'Barrido, limpieza de fregaderos y vitrinas, soplado de polvo y sacudida de telas.',
                'objetivo' => 'Dejar el área social en condiciones seguras y estéticas.',
                'tareas_sugeridas' => 'Fotos antes y después; confirmar pintado y limpieza de muebles.',
                'frecuencia' => 'Cada 30 días',
                'tiempo_minutos' => 30,
            ],
            [
                'zona' => 'Z-JAC',
                'clave' => 'M-JAC',
                'nombre' => 'Mantenimiento del jacuzzi',
                'descripcion' => 'Poda de árboles cercanos, limpieza de bordes y revisión de bombas y filtros.',
                'objetivo' => 'Evitar residuos, mantener equipo de alberca y asegurar agua limpia.',
                'tareas_sugeridas' => 'Fotografías del jacuzzi limpio; confirmar mantenimiento a equipos.',
                'frecuencia' => 'Según tarea',
                'tiempo_minutos' => 0,
            ],
            [
                'zona' => 'Z-JAC',
                'clave' => 'L-JAC',
                'nombre' => 'Limpieza del área del jacuzzi',
                'descripcion' => 'Cepillado de pisos, lavado de muebles, soplado de caminos y orden en bodegas.',
                'objetivo' => 'Garantizar espacios ordenados para los huéspedes del spa.',
                'tareas_sugeridas' => 'Fotos del jacuzzi limpio y bodegas ordenadas; confirmar mantenimiento de bombas.',
                'frecuencia' => 'Según tarea',
                'tiempo_minutos' => 0,
            ],
            [
                'zona' => 'Z-TEM',
                'clave' => 'P-TEM',
                'nombre' => 'Poda y mantenimiento del temazcal',
                'descripcion' => 'Poda de nogales y bugambilias, limpieza de pisos y mantenimiento de aspersores.',
                'objetivo' => 'Preservar la estética del temazcal y permitir rituales seguros.',
                'tareas_sugeridas' => 'Fotos del temazcal y jardines; confirmar mantenimiento de aspersores.',
                'frecuencia' => 'Según tarea',
                'tiempo_minutos' => 0,
            ],
            [
                'zona' => 'Z-FOG',
                'clave' => 'P-FOG',
                'nombre' => 'Poda y limpieza del fogatero',
                'descripcion' => 'Limpieza de bancos, poda de arbustos, retiro de basura y fumigación ligera.',
                'objetivo' => 'Mantener el fogatero listo para eventos y libre de maleza.',
                'tareas_sugeridas' => 'Fotos del fogatero limpio; confirmar poda y fumigación.',
                'frecuencia' => 'Según tarea',
                'tiempo_minutos' => 0,
            ],
            [
                'zona' => 'Z-EXT',
                'clave' => 'P-EXT',
                'nombre' => 'Poda y limpieza del exterior',
                'descripcion' => 'Poda de moras, jacarandas, riego de jardines y retiro de maleza lateral.',
                'objetivo' => 'Asegurar accesos despejados y cuidar la imagen exterior.',
                'tareas_sugeridas' => 'Fotos del exterior; confirmar retiro de maleza y fertilización.',
                'frecuencia' => 'Según tarea',
                'tiempo_minutos' => 0,
            ],
        ];

        foreach ($tareas as $tarea) {
            $zona = $zonas->get($tarea['zona']);
            if (! $zona) {
                continue;
            }

            TareaZona::updateOrCreate(
                [
                    'zona_id' => $zona->id,
                    'clave' => $tarea['clave'],
                ],
                [
                    'predio_id' => $predio->id,
                    'nombre' => $tarea['nombre'],
                    'descripcion' => $tarea['descripcion'],
                    'objetivo' => $tarea['objetivo'],
                    'tareas_sugeridas' => $tarea['tareas_sugeridas'],
                    'frecuencia' => $tarea['frecuencia'],
                    'tiempo_minutos' => $tarea['tiempo_minutos'],
                    'activo' => true,
                ]
            );
        }
    }
}
