<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'Administrador del Sistema',
                'email' => 'admin@sistema.com',
                'telefono' => '555-111-2222',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'rol_id' => DB::table('roles')->where('nombre', 'Administrador')->first()->id,
                'active' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Juan Pérez - Supervisor',
                'email' => 'supervisor@sistema.com',
                'telefono' => '555-333-4444',
                'username' => 'supervisor',
                'password' => Hash::make('password'),
                'rol_id' => DB::table('roles')->where('nombre', 'Supervisor')->first()->id,
                'active' => true,
                'email_verified_at' => now(),
            ],
            [
                'name' => 'Carlos Ruiz - Operario',
                'email' => 'operario@sistema.com',
                'telefono' => '555-555-6666',
                'username' => 'operario',
                'password' => Hash::make('password'),
                'rol_id' => DB::table('roles')->where('nombre', 'Operador')->first()->id,
                'active' => true,
                'email_verified_at' => now(),
            ]
        ];

        foreach ($users as $user) {
            DB::table('users')->insertOrIgnore($user);
        }
    }
}
