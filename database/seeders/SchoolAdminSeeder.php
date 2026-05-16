<?php

namespace Database\Seeders;

use App\Models\School;
use App\Models\User;
use Illuminate\Database\Seeder;

class SchoolAdminSeeder extends Seeder
{
    public function run(): void
    {
        $school = School::query()->first();

        if (!$school) {
            $this->command?->warn('Skipped school admin seed because no school exists yet.');
            return;
        }

        User::updateOrCreate(
            ['email' => 'admin.sekolah@example.com'],
            [
                'name' => 'Admin Sekolah Demo',
                'password' => 'password',
                'role' => 'school_admin',
                'school_id' => $school->id,
            ]
        );

        $this->command?->info('Created school admin user (admin.sekolah@example.com / password)');
    }
}
