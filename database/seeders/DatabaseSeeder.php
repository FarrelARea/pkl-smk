<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Faker\Factory as FakerFactory;
use App\Models\School;
use App\Models\SchoolClass;
use App\Models\User;
use App\Models\Company;
use App\Models\Internship;
use App\Models\DailyLog;
use App\Models\Attendance;
use App\Models\Evaluation;
use App\Models\FinalAssessment;
use App\Models\PermissionRequest;
use App\Models\ClockInOut;

class DatabaseSeeder extends Seeder
{
    private $faker;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 15. Create superadmin user
        User::create([
            'name' => 'Super Admin',
            'email' => 'superadmin@example.com',
            'password' => 'password',
            'role' => 'superadmin',
        ]);
        echo "✓ Created superadmin user (superadmin@example.com / password)\n";
    }
}
