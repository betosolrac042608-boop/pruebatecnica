<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ActivosSeeder extends Seeder
{
    public function run(): void
    {
        // Animales - 7 registros
        $animales = [
            [
                'matricula' => 'VAC-001',
                'nombre' => 'Pinta',
                'especie' => 'Vacuno',
                'raza' => 'Holstein',
                'sexo' => 'H',
                'peso' => 450.00,
                'fecha_nacimiento' => '2021-05-10',
                'fecha_adquisicion' => '2022-05-10',
                'estado' => 'Activo',
                'ubicacion' => 'Corral A',
                'ultima_revision' => '2025-09-15',
                'ultima_vacuna' => '2025-08-20',
                'observaciones' => 'Vaca lechera principal',
                'created_at' => now()
            ],
            [
                'matricula' => 'VAC-002',
                'nombre' => 'Manchitas',
                'especie' => 'Vacuno',
                'raza' => 'Jersey',
                'sexo' => 'H',
                'peso' => 380.50,
                'fecha_nacimiento' => '2022-03-15',
                'fecha_adquisicion' => '2022-08-20',
                'estado' => 'Activo',
                'ubicacion' => 'Corral A',
                'ultima_revision' => '2025-09-15',
                'observaciones' => 'Buena productora de leche',
                'created_at' => now()
            ],
            [
                'matricula' => 'POR-001',
                'nombre' => 'Rosita',
                'especie' => 'Porcino',
                'raza' => 'Yorkshire',
                'sexo' => 'H',
                'peso' => 180.00,
                'fecha_nacimiento' => '2024-01-10',
                'fecha_adquisicion' => '2024-06-15',
                'estado' => 'Activo',
                'ubicacion' => 'Corral B',
                'ultima_vacuna' => '2025-07-10',
                'observaciones' => 'Cerda reproductora',
                'created_at' => now()
            ],
            [
                'matricula' => 'OVI-001',
                'nombre' => 'Nube',
                'especie' => 'Ovino',
                'raza' => 'Merino',
                'sexo' => 'H',
                'peso' => 45.00,
                'fecha_nacimiento' => '2023-09-20',
                'fecha_adquisicion' => '2024-02-10',
                'estado' => 'Activo',
                'ubicacion' => 'Corral C',
                'ultima_revision' => '2025-08-01',
                'observaciones' => 'Oveja de lana fina',
                'created_at' => now()
            ],
            [
                'matricula' => 'EQU-001',
                'nombre' => 'Rayo',
                'especie' => 'Equino',
                'raza' => 'Pura Sangre',
                'sexo' => 'M',
                'peso' => 520.00,
                'fecha_nacimiento' => '2020-04-12',
                'fecha_adquisicion' => '2021-10-05',
                'estado' => 'Activo',
                'ubicacion' => 'Establo Principal',
                'ultima_revision' => '2025-10-01',
                'ultima_vacuna' => '2025-09-15',
                'observaciones' => 'Caballo de trabajo',
                'created_at' => now()
            ],
            [
                'matricula' => 'AVE-001',
                'nombre' => 'Lote 1 Gallinas',
                'especie' => 'Aviar',
                'raza' => 'Rhode Island',
                'sexo' => 'H',
                'peso' => 2.50,
                'fecha_nacimiento' => '2025-01-15',
                'fecha_adquisicion' => '2025-03-01',
                'estado' => 'Activo',
                'ubicacion' => 'Gallinero 1',
                'ultima_vacuna' => '2025-09-01',
                'observaciones' => 'Lote de 50 gallinas ponedoras',
                'created_at' => now()
            ],
            [
                'matricula' => 'CAP-001',
                'nombre' => 'Capitán',
                'especie' => 'Caprino',
                'raza' => 'Alpina',
                'sexo' => 'M',
                'peso' => 75.00,
                'fecha_nacimiento' => '2022-11-05',
                'fecha_adquisicion' => '2023-04-10',
                'estado' => 'Activo',
                'ubicacion' => 'Corral D',
                'ultima_revision' => '2025-09-20',
                'observaciones' => 'Macho reproductor',
                'created_at' => now()
            ],
        ];

        foreach ($animales as $animal) {
            DB::table('animales')->insert($animal);
        }

        // Cultivos - 7 registros
        $cultivos = [
            [
                'matricula' => 'CUL-001',
                'nombre' => 'Parcela Maíz Norte',
                'tipo' => 'Cereal',
                'especie' => 'Maíz',
                'area' => 2.50,
                'estado' => 'En Producción',
                'ubicacion' => 'Sector A, Parcela 1',
                'fecha_siembra' => '2025-03-01',
                'fecha_estimada_cosecha' => '2025-12-01',
                'ultima_fertilizacion' => '2025-09-15',
                'ultimo_riego' => '2025-10-10',
                'observaciones' => 'Maíz híbrido de alto rendimiento',
                'created_at' => now()
            ],
            [
                'matricula' => 'CUL-002',
                'nombre' => 'Tomates Invernadero',
                'tipo' => 'Hortaliza',
                'especie' => 'Tomate',
                'area' => 0.50,
                'estado' => 'En Crecimiento',
                'ubicacion' => 'Invernadero 1',
                'fecha_siembra' => '2025-08-15',
                'fecha_estimada_cosecha' => '2025-11-15',
                'ultimo_riego' => '2025-10-16',
                'observaciones' => 'Tomate cherry para venta',
                'created_at' => now()
            ],
            [
                'matricula' => 'CUL-003',
                'nombre' => 'Frijol Parcela Sur',
                'tipo' => 'Leguminosa',
                'especie' => 'Frijol',
                'area' => 1.20,
                'estado' => 'Sembrado',
                'ubicacion' => 'Sector B, Parcela 2',
                'fecha_siembra' => '2025-09-01',
                'fecha_estimada_cosecha' => '2026-01-15',
                'ultima_fertilizacion' => '2025-09-30',
                'observaciones' => 'Frijol negro',
                'created_at' => now()
            ],
            [
                'matricula' => 'CUL-004',
                'nombre' => 'Lechugas Hidropónicas',
                'tipo' => 'Hortaliza',
                'especie' => 'Lechuga',
                'area' => 0.30,
                'estado' => 'En Producción',
                'ubicacion' => 'Invernadero 2',
                'fecha_siembra' => '2025-09-20',
                'fecha_estimada_cosecha' => '2025-11-01',
                'ultimo_riego' => '2025-10-17',
                'observaciones' => 'Sistema hidropónico',
                'created_at' => now()
            ],
            [
                'matricula' => 'CUL-005',
                'nombre' => 'Naranjos',
                'tipo' => 'Frutal',
                'especie' => 'Naranja',
                'area' => 3.00,
                'estado' => 'En Producción',
                'ubicacion' => 'Huerto Este',
                'fecha_siembra' => '2020-04-10',
                'fecha_estimada_cosecha' => '2026-03-01',
                'ultima_fertilizacion' => '2025-09-01',
                'observaciones' => '50 árboles plantados',
                'created_at' => now()
            ],
            [
                'matricula' => 'CUL-006',
                'nombre' => 'Alfalfa para Forraje',
                'tipo' => 'Forraje',
                'especie' => 'Alfalfa',
                'area' => 5.00,
                'estado' => 'En Producción',
                'ubicacion' => 'Campo Norte',
                'fecha_siembra' => '2024-10-01',
                'ultimo_riego' => '2025-10-15',
                'observaciones' => 'Para alimentación del ganado',
                'created_at' => now()
            ],
            [
                'matricula' => 'CUL-007',
                'nombre' => 'Zanahorias Orgánicas',
                'tipo' => 'Hortaliza',
                'especie' => 'Zanahoria',
                'area' => 0.80,
                'estado' => 'En Crecimiento',
                'ubicacion' => 'Sector C, Parcela 5',
                'fecha_siembra' => '2025-08-20',
                'fecha_estimada_cosecha' => '2025-12-10',
                'ultima_fertilizacion' => '2025-09-20',
                'ultimo_riego' => '2025-10-16',
                'observaciones' => 'Cultivo orgánico certificado',
                'created_at' => now()
            ],
        ];

        foreach ($cultivos as $cultivo) {
            DB::table('cultivos')->insert($cultivo);
        }

        // Herramientas - 7 registros
        $herramientas = [
            [
                'matricula' => 'HER-001',
                'nombre' => 'Tractor John Deere',
                'tipo' => 'Maquinaria Pesada',
                'marca' => 'John Deere',
                'modelo' => '5075E',
                'numero_serie' => 'JD2024-5075E-001',
                'estado' => 'Funcional',
                'ubicacion' => 'Taller Principal',
                'fecha_adquisicion' => '2024-07-15',
                'valor' => 250000.00,
                'ultimo_mantenimiento' => '2025-09-01',
                'proximo_mantenimiento' => '2026-03-01',
                'observaciones' => 'Tractor para labores generales',
                'created_at' => now()
            ],
            [
                'matricula' => 'HER-002',
                'nombre' => 'Motosierra Stihl',
                'tipo' => 'Herramienta Manual',
                'marca' => 'Stihl',
                'modelo' => 'MS 271',
                'numero_serie' => 'ST2023-271-089',
                'estado' => 'Funcional',
                'ubicacion' => 'Almacén de Herramientas',
                'fecha_adquisicion' => '2023-05-20',
                'valor' => 8500.00,
                'ultimo_mantenimiento' => '2025-08-15',
                'proximo_mantenimiento' => '2026-02-15',
                'observaciones' => 'Para poda y mantenimiento',
                'created_at' => now()
            ],
            [
                'matricula' => 'HER-003',
                'nombre' => 'Sistema de Riego Automático',
                'tipo' => 'Equipo de Riego',
                'marca' => 'RainBird',
                'modelo' => 'ESP-TM2',
                'estado' => 'Funcional',
                'ubicacion' => 'Invernadero 1',
                'fecha_adquisicion' => '2024-01-10',
                'valor' => 45000.00,
                'ultimo_mantenimiento' => '2025-07-20',
                'proximo_mantenimiento' => '2026-01-20',
                'observaciones' => 'Sistema programable de riego',
                'created_at' => now()
            ],
            [
                'matricula' => 'HER-004',
                'nombre' => 'Arado de Discos',
                'tipo' => 'Implemento',
                'marca' => 'Bison',
                'modelo' => 'AD-2400',
                'numero_serie' => 'BS2022-AD2400-034',
                'estado' => 'Funcional',
                'ubicacion' => 'Bodega 2',
                'fecha_adquisicion' => '2022-11-05',
                'valor' => 35000.00,
                'ultimo_mantenimiento' => '2025-06-10',
                'proximo_mantenimiento' => '2025-12-10',
                'observaciones' => 'Para preparación de terreno',
                'created_at' => now()
            ],
            [
                'matricula' => 'HER-005',
                'nombre' => 'Camioneta Pick-up',
                'tipo' => 'Vehículo',
                'marca' => 'Toyota',
                'modelo' => 'Hilux 2023',
                'numero_serie' => 'TY2023-HILUX-8765',
                'estado' => 'Funcional',
                'ubicacion' => 'Estacionamiento',
                'fecha_adquisicion' => '2023-03-15',
                'valor' => 450000.00,
                'ultimo_mantenimiento' => '2025-09-25',
                'proximo_mantenimiento' => '2026-03-25',
                'observaciones' => 'Para transporte de productos',
                'created_at' => now()
            ],
            [
                'matricula' => 'HER-006',
                'nombre' => 'Fumigadora Manual',
                'tipo' => 'Herramienta Manual',
                'marca' => 'Matabi',
                'modelo' => 'Super Green 16',
                'estado' => 'Funcional',
                'ubicacion' => 'Almacén de Herramientas',
                'fecha_adquisicion' => '2024-04-20',
                'valor' => 2500.00,
                'observaciones' => 'Para aplicación de pesticidas',
                'created_at' => now()
            ],
            [
                'matricula' => 'HER-007',
                'nombre' => 'Ordeñadora Mecánica',
                'tipo' => 'Equipo Eléctrico',
                'marca' => 'DeLaval',
                'modelo' => 'MMU Classic',
                'numero_serie' => 'DV2024-MMU-0456',
                'estado' => 'En Mantenimiento',
                'ubicacion' => 'Sala de Ordeño',
                'fecha_adquisicion' => '2024-02-01',
                'valor' => 120000.00,
                'ultimo_mantenimiento' => '2025-10-10',
                'proximo_mantenimiento' => '2025-11-10',
                'observaciones' => 'Requiere revisión de motor',
                'created_at' => now()
            ],
        ];

        foreach ($herramientas as $herramienta) {
            DB::table('herramientas')->insert($herramienta);
        }
    }
}
