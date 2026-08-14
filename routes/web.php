<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/login');
});

Route::get('/login', function () {
    return view('auth.login');
});

Route::redirect('/login/{role}', '/login');

Route::get('/dashboard', function () {
    return view('dashboard');
});

// Student pages
Route::get('/student/attendance', fn() => view('student.attendance'));
Route::get('/student/daily-logs', fn() => view('student.daily-logs'));
Route::get('/student/evaluations', fn() => view('student.evaluations'));
Route::get('/student/permission-requests', fn() => view('student.permission-requests'));

// Admin CRUD pages
Route::get('/admin/school-admins', fn() => view('admin.school-admins'));
Route::get('/admin/schools', fn() => view('admin.schools'));
Route::get('/admin/classes', fn() => view('admin.classes'));
Route::get('/admin/teachers', fn() => view('admin.teachers'));
Route::get('/admin/students', fn() => view('admin.students'));
Route::get('/admin/companies', fn() => view('admin.companies'));
Route::get('/admin/supervisors', fn() => view('admin.supervisors'));
Route::get('/admin/internships', fn() => view('admin.internships'));
Route::get('/admin/daily-logs', fn() => view('admin.daily-logs'));
Route::get('/admin/attendance', fn() => view('admin.attendance'));
Route::get('/admin/evaluations', fn() => view('admin.evaluations'));
Route::get('/admin/teacher-assignments', fn() => view('admin.teacher-assignments'));
Route::get('/admin/teacher-company-assignments', fn() => view('admin.teacher-company-assignments'));
Route::get('/admin/attendance-points', fn() => view('admin.attendance-points'));
Route::get('/admin/assessment-templates', fn() => view('admin.assessment-templates'));

// Supervisor pages
Route::get('/supervisor/students/{id}', fn() => view('supervisor.student-detail'));
Route::get('/supervisor/attendance-points', fn() => view('supervisor.attendance-points'));

// Teacher pages
Route::get('/teacher/students/{id}', fn() => view('teacher.student-detail'));
Route::get('/teacher/attendance-points', fn() => view('teacher.attendance-points'));
