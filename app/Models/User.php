<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

#[Fillable(['name', 'email', 'password', 'role', 'school_id'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role', 'school_id', 'company_id'];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [
            'role' => $this->role,
            'school_id' => $this->school_id,
        ];
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    public function classes(): BelongsToMany
    {
        return $this->belongsToMany(SchoolClass::class, 'class_user', 'user_id', 'class_id')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function teacherClasses(): BelongsToMany
    {
        return $this->classes()->wherePivot('role', 'teacher');
    }

    public function studentClasses(): BelongsToMany
    {
        return $this->classes()->wherePivot('role', 'student');
    }

    public function supervisedInternships(): HasMany
    {
        return $this->hasMany(Internship::class, 'supervisor_id');
    }

    public function dailyLogs(): HasMany
    {
        return $this->hasMany(DailyLog::class, 'student_id');
    }

    public function attendance(): HasMany
    {
        return $this->hasMany(Attendance::class, 'student_id');
    }

    public function evaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class, 'student_id');
    }

    public function givenEvaluations(): HasMany
    {
        return $this->hasMany(Evaluation::class, 'evaluator_id');
    }

    public function finalAssessments(): HasMany
    {
        return $this->hasMany(FinalAssessment::class, 'student_id');
    }

    public function isSchoolAdmin(): bool
    {
        return $this->role === 'school_admin';
    }

    public function isTeacher(): bool
    {
        return $this->role === 'teacher';
    }

    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    public function isCompanySupervisor(): bool
    {
        return $this->role === 'company_supervisor';
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    public function relatedStudents(): BelongsToMany
    {
        if ($this->isTeacher()) {
            return $this->belongsToMany(User::class, 'class_user', 'user_id', 'user_id')
                ->wherePivot('role', 'teacher')
                ->orWhere(function ($query) {
                    $query->wherePivot('role', 'student');
                });
        }

        if ($this->isCompanySupervisor()) {
            return $this->belongsToMany(User::class, 'internships', 'supervisor_id', 'student_id');
        }

        return $this->belongsToMany(User::class);
    }

    public function getMyStudents(): \Illuminate\Database\Eloquent\Collection
    {
        if ($this->isTeacher()) {
            $classIds = $this->classes()->pluck('classes.id');
            return User::where('role', 'student')
                ->whereHas('classes', function ($query) use ($classIds) {
                    $query->whereIn('classes.id', $classIds);
                })
                ->get();
        }

        if ($this->isCompanySupervisor()) {
            return User::where('role', 'student')
                ->whereHas('supervisedInternships', function ($query) {
                    $query->where('supervisor_id', $this->id);
                })
                ->get();
        }

        return collect();
    }

    public function permissionRequests(): HasMany
    {
        return $this->hasMany(\App\Models\PermissionRequest::class, 'student_id');
    }

    public function handledPermissionRequests(): HasMany
    {
        return $this->hasMany(\App\Models\PermissionRequest::class, 'handled_by');
    }

    public function internships(): HasMany
    {
        return $this->hasMany(Internship::class, 'student_id');
    }

    public function studentDocuments(): HasMany
    {
        return $this->hasMany(StudentDocument::class, 'student_id');
    }

    public function documentRequirements(): HasMany
    {
        return $this->hasMany(DocumentRequirement::class, 'teacher_id');
    }
}
