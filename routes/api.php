<?php

use Illuminate\Support\Facades\Route;

Route::get('/docs', function () {
    return redirect('/api-docs.html');
});

Route::get('/docs.json', function () {
    return response()->json(json_decode(file_get_contents(public_path('api-docs.json')), true));
});

Route::prefix('v1')->group(function () {
    Route::get('address/provinces', [App\Http\Controllers\Api\V1\AddressController::class, 'provinces']);
    Route::get('address/cities', [App\Http\Controllers\Api\V1\AddressController::class, 'cities']);
    Route::get('address/districts', [App\Http\Controllers\Api\V1\AddressController::class, 'districts']);
    Route::get('address/villages', [App\Http\Controllers\Api\V1\AddressController::class, 'villages']);
    Route::get('address/search', [App\Http\Controllers\Api\V1\AddressController::class, 'search']);
    
    Route::prefix('auth')->group(function () {
        Route::post('/login', [App\Http\Controllers\Api\V1\AuthController::class, 'login']);
        Route::post('/register', [App\Http\Controllers\Api\V1\AuthController::class, 'register']);
        Route::post('/logout', [App\Http\Controllers\Api\V1\AuthController::class, 'logout'])->middleware('auth:api');
        Route::post('/refresh', [App\Http\Controllers\Api\V1\AuthController::class, 'refresh'])->middleware('auth:api');
        Route::get('/me', [App\Http\Controllers\Api\V1\AuthController::class, 'me'])->middleware('auth:api');
    });

    Route::middleware('auth:api')->group(function () {
        Route::post('admin/reset-password', [App\Http\Controllers\Api\V1\AdminController::class, 'resetPassword'])
            ->middleware('role:school_admin');

        Route::get('companies/search', [App\Http\Controllers\Api\V1\CompanyController::class, 'search'])
            ->middleware('role:school_admin');
        Route::post('companies/{id}/assign-supervisor', [App\Http\Controllers\Api\V1\CompanyController::class, 'assignSupervisor'])
            ->middleware('role:school_admin');

        Route::apiResources([
            'schools' => App\Http\Controllers\Api\V1\SchoolController::class,
            'classes' => App\Http\Controllers\Api\V1\ClassController::class,
            'teachers' => App\Http\Controllers\Api\V1\TeacherController::class,
            'students' => App\Http\Controllers\Api\V1\StudentController::class,
            'companies' => App\Http\Controllers\Api\V1\CompanyController::class,
            'supervisors' => App\Http\Controllers\Api\V1\SupervisorController::class,
            'internships' => App\Http\Controllers\Api\V1\InternshipController::class,
            'daily-logs' => App\Http\Controllers\Api\V1\DailyLogController::class,
            'attendance' => App\Http\Controllers\Api\V1\AttendanceController::class,
            'evaluations' => App\Http\Controllers\Api\V1\EvaluationController::class,
        ]);

        Route::get('students/{id}/internship-status', [App\Http\Controllers\Api\V1\StudentController::class, 'internshipStatus']);
        Route::post('students/{id}/assign-class', [App\Http\Controllers\Api\V1\StudentController::class, 'assignClass']);
        
        Route::post('teachers/{id}/assign-class', [App\Http\Controllers\Api\V1\TeacherController::class, 'assignClass']);
        
        Route::post('supervisors/{id}/assign-company', [App\Http\Controllers\Api\V1\SupervisorController::class, 'assignCompany']);
        
        Route::post('internships/{id}/end', [App\Http\Controllers\Api\V1\InternshipController::class, 'end']);
        Route::get('students/{id}/internship-history', [App\Http\Controllers\Api\V1\InternshipController::class, 'studentHistory']);
        Route::post('internships/batch', [App\Http\Controllers\Api\V1\InternshipController::class, 'batchStore'])
            ->middleware('role:school_admin');
        
        Route::post('daily-logs/{id}/comment', [App\Http\Controllers\Api\V1\DailyLogController::class, 'addComment']);
        
        Route::post('attendance/bulk', [App\Http\Controllers\Api\V1\AttendanceController::class, 'bulkStore']);
        Route::get('students/{id}/attendance-summary', [App\Http\Controllers\Api\V1\AttendanceController::class, 'summary']);
        
        Route::get('students/{id}/evaluation-summary', [App\Http\Controllers\Api\V1\EvaluationController::class, 'summary']);
        
        Route::apiResource('final-assessments', App\Http\Controllers\Api\V1\FinalAssessmentController::class);
        
        Route::apiResource('permission-requests', App\Http\Controllers\Api\V1\PermissionRequestController::class);
        Route::post('permission-requests/{id}/approve', [App\Http\Controllers\Api\V1\PermissionRequestController::class, 'approve']);
        Route::post('permission-requests/{id}/reject', [App\Http\Controllers\Api\V1\PermissionRequestController::class, 'reject']);
        
        Route::post('clock-in-out', [App\Http\Controllers\Api\V1\ClockInOutController::class, 'store']);
        Route::get('clock-in-out', [App\Http\Controllers\Api\V1\ClockInOutController::class, 'index']);
        Route::get('clock-in-out/{id}', [App\Http\Controllers\Api\V1\ClockInOutController::class, 'show']);
        Route::get('clock-in-out/{id}/today-status', [App\Http\Controllers\Api\V1\ClockInOutController::class, 'todayStatus']);
        Route::delete('clock-in-out/{id}', [App\Http\Controllers\Api\V1\ClockInOutController::class, 'destroy']);
        Route::get('students/{id}/clock-in-out-history', [App\Http\Controllers\Api\V1\ClockInOutController::class, 'history']);
        
        Route::get('my-students', [App\Http\Controllers\Api\V1\StudentActivityController::class, 'myStudents']);
        Route::get('students/{id}/logs', [App\Http\Controllers\Api\V1\StudentActivityController::class, 'studentLogs']);
        Route::get('students/{id}/attendance', [App\Http\Controllers\Api\V1\StudentActivityController::class, 'studentAttendance']);
        Route::get('students/{id}/clock-in-out', [App\Http\Controllers\Api\V1\StudentActivityController::class, 'studentClockInOut']);
        Route::get('students/{id}/summary', [App\Http\Controllers\Api\V1\StudentActivityController::class, 'studentSummary']);
        
        // Student evaluations page (attendance calendar + documents)
        Route::middleware('role:student')->group(function () {
            Route::get('student/evaluations', [App\Http\Controllers\Api\V1\StudentEvaluationController::class, 'index']);
            Route::get('student/attendance', [App\Http\Controllers\Api\V1\StudentEvaluationController::class, 'myAttendance']);
            Route::post('student/documents', [App\Http\Controllers\Api\V1\StudentDocumentController::class, 'store']);
            Route::delete('student/documents/{id}', [App\Http\Controllers\Api\V1\StudentDocumentController::class, 'destroy']);
            Route::post('student/daily-logs/{id}/comments', [App\Http\Controllers\Api\V1\StudentEvaluationController::class, 'addComment']);
        });

        // Teacher document approval + panel
        Route::middleware('role:teacher')->group(function () {
            Route::get('teacher/students/{id}/documents', [App\Http\Controllers\Api\V1\DocumentApprovalController::class, 'teacherDocuments']);
            Route::post('teacher/documents/{id}/approve', [App\Http\Controllers\Api\V1\DocumentApprovalController::class, 'approve']);
            Route::post('teacher/documents/{id}/reject', [App\Http\Controllers\Api\V1\DocumentApprovalController::class, 'reject']);
            Route::get('document-requirements', [App\Http\Controllers\Api\V1\DocumentRequirementController::class, 'index']);
            Route::post('document-requirements', [App\Http\Controllers\Api\V1\DocumentRequirementController::class, 'store']);
            Route::put('document-requirements/{id}', [App\Http\Controllers\Api\V1\DocumentRequirementController::class, 'update']);
            Route::delete('document-requirements/{id}', [App\Http\Controllers\Api\V1\DocumentRequirementController::class, 'destroy']);
            // Teacher panel
            Route::get('teacher/panel/students', [App\Http\Controllers\Api\V1\TeacherPanelController::class, 'students']);
            Route::get('teacher/panel/students/{id}/stats', [App\Http\Controllers\Api\V1\TeacherPanelController::class, 'studentStats']);
            Route::post('teacher/panel/students/{id}/evaluate', [App\Http\Controllers\Api\V1\TeacherPanelController::class, 'evaluate']);
            Route::get('teacher/panel/students/{id}/permissions', [App\Http\Controllers\Api\V1\TeacherPanelController::class, 'permissions']);
            Route::post('teacher/panel/permissions/{id}/approve', [App\Http\Controllers\Api\V1\TeacherPanelController::class, 'approvePermission']);
            Route::post('teacher/panel/permissions/{id}/reject', [App\Http\Controllers\Api\V1\TeacherPanelController::class, 'rejectPermission']);
            Route::get('teacher/panel/students/{id}/logs', [App\Http\Controllers\Api\V1\TeacherPanelController::class, 'logs']);
            Route::post('teacher/panel/daily-logs/{id}/comments', [App\Http\Controllers\Api\V1\TeacherPanelController::class, 'addComment']);
            Route::post('teacher/panel/daily-logs/{id}/review', [App\Http\Controllers\Api\V1\TeacherPanelController::class, 'review']);
            Route::get('teacher/panel/pending', [App\Http\Controllers\Api\V1\TeacherPanelController::class, 'pending']);
        });

        // Admin teacher assignments
        Route::middleware('role:school_admin')->group(function () {
            Route::get('admin/teacher-assignments', [App\Http\Controllers\Api\V1\TeacherStudentAssignmentController::class, 'index']);
            Route::post('admin/teacher-assignments', [App\Http\Controllers\Api\V1\TeacherStudentAssignmentController::class, 'store']);
            Route::delete('admin/teacher-assignments/{id}', [App\Http\Controllers\Api\V1\TeacherStudentAssignmentController::class, 'destroy']);
        });

        // Supervisor document approval (optional)
        Route::middleware('role:company_supervisor')->group(function () {
            Route::post('supervisor/documents/{id}/approve', [App\Http\Controllers\Api\V1\DocumentApprovalController::class, 'supervisorApprove']);
        });

        Route::middleware('role:school_admin')->group(function () {
            Route::post('import/schools', [App\Http\Controllers\Api\V1\ImportExportController::class, 'importSchools']);
            Route::post('import/classes', [App\Http\Controllers\Api\V1\ImportExportController::class, 'importClasses']);
            Route::post('import/teachers', [App\Http\Controllers\Api\V1\ImportExportController::class, 'importTeachers']);
            Route::post('import/students', [App\Http\Controllers\Api\V1\ImportExportController::class, 'importStudents']);
            
            Route::get('export/schools', [App\Http\Controllers\Api\V1\ImportExportController::class, 'exportSchools']);
            Route::get('export/classes', [App\Http\Controllers\Api\V1\ImportExportController::class, 'exportClasses']);
            Route::get('export/teachers', [App\Http\Controllers\Api\V1\ImportExportController::class, 'exportTeachers']);
            Route::get('export/students', [App\Http\Controllers\Api\V1\ImportExportController::class, 'exportStudents']);
            
            Route::post('address/geocode', [App\Http\Controllers\Api\V1\AddressController::class, 'geocode']);
        });
    });
});
