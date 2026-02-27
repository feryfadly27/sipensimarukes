<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            [
                'nama' => 'Super Admin',
                'role' => 'superadmin',
                'username' => 'superadmin',
                'password' => Hash::make('arteriahelena'),
                'no_telp' => '081234567890',
            ],
            [
                'nama' => 'Admin Sistem',
                'role' => 'admin',
                'username' => 'admin',
                'password' => Hash::make('password'),
                'no_telp' => '081234567891',
            ],
            [
                'nama' => 'Petugas Pendaftaran',
                'role' => 'pendaftaran',
                'username' => 'pendaftaran',
                'password' => Hash::make('password'),
                'no_telp' => '081234567892',
            ],
            [
                'nama' => 'PLP (Pranata Lab)',
                'role' => 'plp',
                'username' => 'plp',
                'password' => Hash::make('password'),
                'no_telp' => '081234567893',
            ],
            [
                'nama' => 'Dokter Pemeriksa',
                'role' => 'dokter',
                'username' => 'dokter',
                'password' => Hash::make('password'),
                'no_telp' => '081234567894',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }
        
        $this->command->info('✓ 5 users created successfully!');
        $this->command->info('  - superadmin / arteriahelena');
        $this->command->info('  - admin / password');
        $this->command->info('  - pendaftaran / password');
        $this->command->info('  - plp / password');
        $this->command->info('  - dokter / password');
    }
}

