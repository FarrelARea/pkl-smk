<?php

namespace App\Exports;

use App\Models\StudentAssessment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AssessmentRecapExport implements FromCollection, WithHeadings
{
    protected ?int $classId;
    protected ?int $internshipId;
    protected ?string $academicYear;

    public function __construct(?int $classId = null, ?int $internshipId = null, ?string $academicYear = null)
    {
        $this->classId = $classId;
        $this->internshipId = $internshipId;
        $this->academicYear = $academicYear;
    }

    public function collection()
    {
        $query = StudentAssessment::with(['student', 'internship.company', 'teacher', 'scores']);

        if ($this->academicYear) {
            $academicYear = $this->academicYear;
            $query->whereHas('student', function ($q) use ($academicYear) {
                $q->whereHas('classes', function ($q2) use ($academicYear) {
                    $q2->where('academic_year', $academicYear);
                });
            });
        }

        if ($this->classId) {
            $classId = $this->classId;
            $query->whereHas('student', function ($q) use ($classId) {
                $q->whereHas('classes', function ($q2) use ($classId) {
                    $q2->where('classes.id', $classId);
                });
            });
        }

        if ($this->internshipId) {
            $query->where('internship_id', $this->internshipId);
        }

        return $query->get()->map(function ($assessment) {
            $nonNullScores = $assessment->scores->filter(fn ($s) => $s->score !== null);
            $sectionAvgs = $nonNullScores->groupBy('section_number')->map(function ($scores) {
                return round($scores->avg('score'), 1);
            });

            $overall = $nonNullScores->isNotEmpty()
                ? round($nonNullScores->avg('score'), 1)
                : null;

            // Build dynamic section columns
            $row = [
                'student_name' => $assessment->student->name ?? '',
                'student_email' => $assessment->student->email ?? '',
                'company' => $assessment->internship->company->name ?? '',
                'teacher' => $assessment->teacher->name ?? '',
                'status' => $assessment->status,
            ];

            // Add section averages (TP1-TP4)
            for ($i = 1; $i <= 4; $i++) {
                $row["tp{$i}_avg"] = $sectionAvgs->get((string) $i, '');
            }

            $row['overall_avg'] = $overall ?? '';
            $row['teacher_notes'] = $assessment->teacher_notes ?? '';

            return $row;
        });
    }

    public function headings(): array
    {
        return [
            'Nama Siswa',
            'Email',
            'Tempat PKL',
            'Guru Pembimbing',
            'Status',
            'Rata-rata TP1',
            'Rata-rata TP2',
            'Rata-rata TP3',
            'Rata-rata TP4',
            'Rata-rata Keseluruhan',
            'Catatan Guru',
        ];
    }
}
