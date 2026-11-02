<?php

namespace Database\Seeders;

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
                'name' => 'Administrator SIMKERMA',
                'email' => 'admin@unipar.ac.id',
                'role' => 'ADMIN',
                'unit' => 'Biro Sistem Informasi & Kerja Sama UNIPAR',
                'phone' => '0812-4455-6677',
                'password' => Hash::make('admin123'),
            ],
            [
                'name' => 'Unit Pengusul (Prodi TI)',
                'email' => 'unit@unipar.ac.id',
                'role' => 'UNIT',
                'unit' => 'Program Studi S1 Teknik Informatika',
                'phone' => '0813-1122-3344',
                'password' => Hash::make('unit123'),
            ],
            [
                'name' => 'Tim Telaah Hukum & Kerja Sama',
                'email' => 'legal@unipar.ac.id',
                'role' => 'LEGAL',
                'unit' => 'Biro Hukum & Kerja Sama UNIPAR',
                'phone' => '0815-5566-7788',
                'password' => Hash::make('legal123'),
            ],
            [
                'name' => 'Dr. Rosdiana Simbolon, SE., S.Kom., M.Si (Rektor)',
                'email' => 'rektor@unipar.ac.id',
                'role' => 'LEADER',
                'unit' => 'Rektorat Universitas Parna Raya',
                'phone' => '0811-9988-7766',
                'password' => Hash::make('rektor123'),
            ],
        ];

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }
    }
}
