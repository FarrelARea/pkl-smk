<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class TestUserSeeder extends Seeder
{
    /**
     * Seed test users for all roles.
     */
    public function run(): void
    {
        $users = [
            ['name' => 'Admin Sekolah', 'email' => 'admin@school.com', 'role' => 'school_admin'],
            ['name' => 'Guru', 'email' => 'teacher@school.com', 'role' => 'teacher'],
            ['name' => 'Siswa', 'email' => 'student@school.com', 'role' => 'student'],
            ['name' => 'Pembimbing', 'email' => 'supervisor@company.com', 'role' => 'company_supervisor'],
        ];

        foreach ($users as $user) {
            User::updateOrCreate(
                ['email' => $user['email']],
                [
                    'name' => $user['name'],
                    'password' => 'password',
                    'role' => $user['role'],
                ]
            );
        }

        $this->command?->info('Created test users (password: password):');
        $this->command?->info('  admin@school.com      → school_admin');
        $this->command?->info('  teacher@school.com    → teacher');
        $this->command?->info('  student@school.com    → student');
        $this->command?->info('  supervisor@company.com → company_supervisor');
    }
}
