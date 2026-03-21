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
        $this->faker = FakerFactory::create();

        // Truncate all tables to ensure clean state
        $this->truncateTables();

        // Configuration for data volumes
        $schoolCount = 5;
        $classPerSchoolCount = 4;
        $teacherCount = 30;
        $studentPerSchoolCount = 60;
        $companyCount = 10;
        $supervisorCount = 10;
        $internshipCount = 150;
        $dailyLogsPerStudent = 10;
        $attendancePerStudent = 20;
        $evaluationCount = 450;
        $finalAssessmentCount = 300;
        $permissionRequestCount = 500;
        $clockInOutCount = 1000;

        // 1. Create schools
        $schools = School::factory($schoolCount)->create();
        echo "✓ Created {$schoolCount} schools\n";

        // 2. Create classes for each school
        $classCount = $schoolCount * $classPerSchoolCount;
        foreach ($schools as $school) {
            SchoolClass::factory($classPerSchoolCount)->create([
                'school_id' => $school->id,
            ]);
        }
        echo "✓ Created {$classCount} classes\n";

        // Get all classes for teacher assignments
        $classes = SchoolClass::all();

        // 3. Create teachers and assign to classes
        $teachers = [];
        for ($i = 0; $i < $teacherCount; $i++) {
            $teacher = User::factory()->state([
                'role' => 'teacher',
                'school_id' => $schools->random()->id,
            ])->create();
            $teachers[] = $teacher;

            // Assign teacher to 2-4 random classes
            $assignedClasses = $classes->random($this->faker->numberBetween(2, 4));
            foreach ($assignedClasses as $class) {
                $teacher->classes()->attach($class->id, ['role' => 'teacher']);
            }
        }
        echo "✓ Created {$teacherCount} teachers with class assignments\n";

        // 4. Create students for each school and assign to classes
        $students = [];
        $studentCount = $schoolCount * $studentPerSchoolCount;
        foreach ($schools as $school) {
            $schoolClasses = $school->classes;
            for ($i = 0; $i < $studentPerSchoolCount; $i++) {
                $student = User::factory()->state([
                    'role' => 'student',
                    'school_id' => $school->id,
                ])->create();
                $students[] = $student;

                // Assign student to exactly one class
                if ($schoolClasses->isNotEmpty()) {
                    $student->classes()->attach($schoolClasses->random()->id, ['role' => 'student']);
                }
            }
        }
        echo "✓ Created {$studentCount} students with class assignments\n";

        // 5. Create companies
        $companies = Company::factory($companyCount)->create();
        echo "✓ Created {$companyCount} companies\n";

        // 6. Create supervisors and assign to companies
        $supervisors = [];
        for ($i = 0; $i < $supervisorCount; $i++) {
            $supervisor = User::factory()->state([
                'role' => 'company_supervisor',
                'company_id' => $companies->random()->id,
            ])->create();
            $supervisors[] = $supervisor;
        }
        echo "✓ Created {$supervisorCount} supervisors with company assignments\n";

        // 7. Create internships linking students to supervisors
        $internships = [];
        $studentChunks = collect($students)->chunk(ceil(count($students) / $internshipCount));
        $supervisorIndex = 0;
        foreach ($studentChunks as $chunk) {
            if (count($internships) >= $internshipCount) break;
            foreach ($chunk as $student) {
                if (count($internships) >= $internshipCount) break;
                $supervisor = $supervisors[$supervisorIndex % count($supervisors)];
                $company = $supervisor->company;
                $supervisorIndex++;

                $internships[] = Internship::create([
                    'student_id' => $student->id,
                    'company_id' => $company->id,
                    'supervisor_id' => $supervisor->id,
                    'start_date' => $this->faker->dateTimeBetween('-6 months', '-1 month'),
                    'end_date' => $this->faker->dateTimeBetween('now', '+6 months'),
                    'status' => $this->faker->randomElement(['active', 'completed', 'cancelled']),
                ]);
            }
        }
        echo "✓ Created " . count($internships) . " internships\n";

        // 8. Create daily logs
        $dailyLogsCount = count($students) * $dailyLogsPerStudent;
        $createdDailyLogs = 0;
        foreach ($students as $student) {
            // Get a random internship for this student
            $internship = Internship::where('student_id', $student->id)->first();
            if (!$internship) continue;

            // Generate unique log dates for this student
            $startDate = \Carbon\Carbon::now()->subMonths(3);
            $endDate = \Carbon\Carbon::now();

            for ($i = 0; $i < $dailyLogsPerStudent; $i++) {
                // Generate random dates ensuring uniqueness
                $logDate = $this->faker->dateTimeBetween($startDate, $endDate);

                try {
                    DailyLog::create([
                        'student_id' => $student->id,
                        'internship_id' => $internship->id,
                        'log_date' => $logDate,
                        'activities' => $this->faker->sentence(),
                        'reflection' => $this->faker->paragraph(),
                    ]);
                    $createdDailyLogs++;
                } catch (\Exception $e) {
                    // Skip duplicate date errors
                }
            }
        }
        echo "✓ Created {$createdDailyLogs} daily logs\n";

        // 9. Create attendance records
        $attendanceCount = count($students) * $attendancePerStudent;
        $createdAttendance = 0;
        $statuses = ['present', 'absent', 'sick', 'permission'];
        foreach ($students as $student) {
            // Get a random internship for this student
            $internship = Internship::where('student_id', $student->id)->first();
            if (!$internship) continue;

            $startDate = \Carbon\Carbon::now()->subMonths(3);
            $endDate = \Carbon\Carbon::now();

            for ($i = 0; $i < $attendancePerStudent; $i++) {
                $attendanceDate = $this->faker->dateTimeBetween($startDate, $endDate);

                try {
                    Attendance::create([
                        'student_id' => $student->id,
                        'internship_id' => $internship->id,
                        'attendance_date' => $attendanceDate,
                        'status' => $this->faker->randomElement($statuses),
                    ]);
                    $createdAttendance++;
                } catch (\Exception $e) {
                    // Skip duplicates
                }
            }
        }
        echo "✓ Created {$createdAttendance} attendance records\n";

        // 10. Create evaluations
        $createdEvaluations = 0;
        $evaluationTypes = ['teacher', 'supervisor'];
        for ($i = 0; $i < $evaluationCount; $i++) {
            $student = collect($students)->random();
            $internship = Internship::where('student_id', $student->id)->first();
            if (!$internship) continue;

            $evaluationType = $this->faker->randomElement($evaluationTypes);
            if ($evaluationType === 'teacher') {
                $evaluator = collect($teachers)->random();
            } else {
                $evaluator = collect($supervisors)->random();
            }

            Evaluation::create([
                'student_id' => $student->id,
                'internship_id' => $internship->id,
                'evaluator_id' => $evaluator->id,
                'type' => $evaluationType,
                'score' => $this->faker->numberBetween(60, 100),
                'comments' => $this->faker->paragraph(),
            ]);
            $createdEvaluations++;
        }
        echo "✓ Created {$createdEvaluations} evaluations\n";

        // 11. Create final assessments
        $createdAssessments = 0;
        $grades = ['A', 'B', 'C', 'D'];
        foreach ($students as $student) {
            $internship = Internship::where('student_id', $student->id)->first();
            if (!$internship) continue;

            $schoolAdmin = $schools->first();
            $adminUser = User::where('role', 'school_admin')->where('school_id', $schoolAdmin->id)->first();
            if (!$adminUser) continue;

            FinalAssessment::create([
                'student_id' => $student->id,
                'internship_id' => $internship->id,
                'school_admin_id' => $adminUser->id,
                'final_score' => $this->faker->numberBetween(60, 100),
                'grade' => $this->faker->randomElement($grades),
            ]);
            $createdAssessments++;
        }
        echo "✓ Created {$createdAssessments} final assessments\n";

        // 12. Create permission requests
        $createdPermissions = 0;
        $permissionStatuses = ['pending', 'approved', 'rejected'];
        $permissionTypes = ['sick', 'permit', 'other'];
        for ($i = 0; $i < $permissionRequestCount; $i++) {
            $student = collect($students)->random();
            $internship = Internship::where('student_id', $student->id)->first();
            if (!$internship) continue;

            $handler = $this->faker->boolean(60) ? collect($teachers)->random() : null;

            PermissionRequest::create([
                'student_id' => $student->id,
                'internship_id' => $internship->id,
                'request_date' => $this->faker->dateTimeBetween('-3 months', 'now'),
                'type' => $this->faker->randomElement($permissionTypes),
                'reason' => $this->faker->sentence(),
                'status' => $this->faker->randomElement($permissionStatuses),
                'handled_by' => $handler?->id,
            ]);
            $createdPermissions++;
        }
        echo "✓ Created {$createdPermissions} permission requests\n";

        // 13. Create clock in/out records
        $createdClockRecords = 0;
        $clockTypes = ['clock_in', 'clock_out'];
        for ($i = 0; $i < $clockInOutCount; $i++) {
            $student = collect($students)->random();
            $internship = Internship::where('student_id', $student->id)->first();
            if (!$internship) continue;

            $company = $internship->company;

            ClockInOut::create([
                'user_id' => $student->id,
                'internship_id' => $internship->id,
                'type' => $this->faker->randomElement($clockTypes),
                'latitude' => $this->faker->latitude(),
                'longitude' => $this->faker->longitude(),
                'company_latitude' => $company ? $this->faker->latitude() : null,
                'company_longitude' => $company ? $this->faker->longitude() : null,
            ]);
            $createdClockRecords++;
        }
        echo "✓ Created {$createdClockRecords} clock in/out records\n";

        // 14. Create admin user
        $admin = User::create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'password' => 'password',
            'role' => 'school_admin',
            'school_id' => $schools->first()->id,
        ]);
        echo "✓ Created admin user (admin@example.com / password)\n";

        echo "\n✅ Database seeding completed successfully!\n";
        echo "Total records created:\n";
        echo "  - Schools: {$schoolCount}\n";
        echo "  - Classes: {$classCount}\n";
        echo "  - Teachers: {$teacherCount}\n";
        echo "  - Students: {$studentCount}\n";
        echo "  - Companies: {$companyCount}\n";
        echo "  - Supervisors: {$supervisorCount}\n";
        echo "  - Internships: " . count($internships) . "\n";
        echo "  - Daily Logs: {$createdDailyLogs}\n";
        echo "  - Attendance: {$createdAttendance}\n";
        echo "  - Evaluations: {$createdEvaluations}\n";
        echo "  - Final Assessments: {$createdAssessments}\n";
        echo "  - Permission Requests: {$createdPermissions}\n";
        echo "  - Clock In/Out: {$createdClockRecords}\n";
    }

    /**
     * Truncate all tables in dependency order
     */
    private function truncateTables(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        $tables = [
            'clock_in_outs',
            'permission_requests',
            'final_assessments',
            'evaluations',
            'attendance',
            'daily_logs',
            'internships',
            'class_user',
            'users',
            'school_classes',
            'companies',
            'schools',
        ];

        foreach ($tables as $table) {
            try {
                DB::table($table)->truncate();
            } catch (\Exception $e) {
                // Table might not exist
            }
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
}
