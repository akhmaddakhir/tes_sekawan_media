<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $users = [
            [
                'name' => 'Admin Pool',
                'email' => 'admin@armadanikel.com',
                'password' => Hash::make('Admin123'),
                'role' => 'admin',
                'jabatan' => 'Staff Pengelola Kendaraan (Pool)',
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'spv@armadanikel.com',
                'password' => Hash::make('Approver123'),
                'role' => 'approver',
                'jabatan' => 'Supervisor Operasional (Level 1)',
            ],
            [
                'name' => 'Siti Aminah',
                'email' => 'manager@armadanikel.com',
                'password' => Hash::make('Approver123'),
                'role' => 'approver',
                'jabatan' => 'Manager Tambang (Level 2)',
            ],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                $user
            );
        }
    }
}