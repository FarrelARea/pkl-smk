<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Imports\ClassesImport;
use App\Imports\SchoolsImport;
use App\Imports\StudentsImport;
use App\Imports\TeachersImport;
use App\Imports\CompaniesImport;
use App\Imports\SupervisorsImport;
use App\Imports\InternshipsImport;
use App\Imports\SchoolAdminsImport;
use App\Imports\AttendancePointsImport;
use App\Imports\TeacherStudentAssignmentsImport;
use App\Imports\TeacherCompanyAssignmentsImport;
use App\Exports\ClassesExport;
use App\Exports\SchoolsExport;
use App\Exports\StudentsExport;
use App\Exports\TeachersExport;
use App\Exports\AssessmentRecapExport;
use App\Exports\CompaniesExport;
use App\Exports\SupervisorsExport;
use App\Exports\InternshipsExport;
use App\Exports\SchoolAdminsExport;
use App\Exports\AttendancePointsExport;
use App\Exports\TeacherStudentAssignmentsExport;
use App\Exports\TeacherCompanyAssignmentsExport;
use App\Exports\TemplateExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ImportExportController extends Controller
{
    // ─── IMPORTS ─────────────────────────────────────────────

    public function importSchools(Request $request)
    {
        return $this->handleImport($request, new SchoolsImport, 'Sekolah');
    }

    public function importClasses(Request $request)
    {
        return $this->handleImport($request, new ClassesImport, 'Kelas');
    }

    public function importTeachers(Request $request)
    {
        return $this->handleImport($request, new TeachersImport, 'Guru');
    }

    public function importStudents(Request $request)
    {
        return $this->handleImport($request, new StudentsImport, 'Siswa');
    }

    public function importCompanies(Request $request)
    {
        return $this->handleImport($request, new CompaniesImport, 'Perusahaan');
    }

    public function importSupervisors(Request $request)
    {
        return $this->handleImport($request, new SupervisorsImport, 'Pembimbing Lapangan');
    }

    public function importInternships(Request $request)
    {
        return $this->handleImport($request, new InternshipsImport, 'Magang');
    }

    public function importSchoolAdmins(Request $request)
    {
        return $this->handleImport($request, new SchoolAdminsImport, 'Admin Sekolah');
    }

    public function importAttendancePoints(Request $request)
    {
        return $this->handleImport($request, new AttendancePointsImport, 'Titik Absensi');
    }

    public function importTeacherAssignments(Request $request)
    {
        return $this->handleImport($request, new TeacherStudentAssignmentsImport, 'Penugasan Guru');
    }

    public function importTeacherCompanyAssignments(Request $request)
    {
        return $this->handleImport($request, new TeacherCompanyAssignmentsImport, 'Penugasan Perusahaan');
    }

    // ─── EXPORTS ─────────────────────────────────────────────

    public function exportSchools()
    {
        return Excel::download(new SchoolsExport, 'sekolah.xlsx');
    }

    public function exportClasses()
    {
        return Excel::download(new ClassesExport, 'kelas.xlsx');
    }

    public function exportTeachers()
    {
        return Excel::download(new TeachersExport, 'guru.xlsx');
    }

    public function exportStudents()
    {
        return Excel::download(new StudentsExport, 'siswa.xlsx');
    }

    public function exportCompanies()
    {
        return Excel::download(new CompaniesExport, 'perusahaan.xlsx');
    }

    public function exportSupervisors()
    {
        return Excel::download(new SupervisorsExport, 'pembimbing-lapangan.xlsx');
    }

    public function exportInternships()
    {
        return Excel::download(new InternshipsExport, 'magang.xlsx');
    }

    public function exportSchoolAdmins()
    {
        return Excel::download(new SchoolAdminsExport, 'admin-sekolah.xlsx');
    }

    public function exportAttendancePoints()
    {
        return Excel::download(new AttendancePointsExport, 'titik-absensi.xlsx');
    }

    public function exportTeacherAssignments()
    {
        return Excel::download(new TeacherStudentAssignmentsExport, 'penugasan-guru.xlsx');
    }

    public function exportTeacherCompanyAssignments()
    {
        return Excel::download(new TeacherCompanyAssignmentsExport, 'penugasan-perusahaan.xlsx');
    }

    public function exportAssessments(Request $request)
    {
        $classId = $request->query('class_id') ? (int) $request->query('class_id') : null;
        $internshipId = $request->query('internship_id') ? (int) $request->query('internship_id') : null;
        $academicYear = $request->query('academic_year');

        return Excel::download(new AssessmentRecapExport($classId, $internshipId, $academicYear), 'rekap-penilaian.xlsx');
    }

    // ─── TEMPLATES ───────────────────────────────────────────

    public function downloadSchoolsTemplate()
    {
        return $this->downloadTemplate(
            ['Nama', 'Alamat', 'Telepon', 'Email'],
            [['SMK Negeri 1 Jakarta', 'Jl. Merdeka No. 1', '021-12345678', 'info@smkn1jkt.sch.id']],
            'template-import-sekolah.xlsx'
        );
    }

    public function downloadClassesTemplate()
    {
        return $this->downloadTemplate(
            ['Nama Sekolah', 'Nama', 'Tahun Ajaran'],
            [['SMK Negeri 1 Jakarta', 'XII RPL 1', '2025/2026']],
            'template-import-kelas.xlsx'
        );
    }

    public function downloadTeachersTemplate()
    {
        return $this->downloadTemplate(
            ['Nama Sekolah', 'Nama', 'Email', 'Password'],
            [['SMK Negeri 1 Jakarta', 'Budi Santoso', 'budi@smkn1jkt.sch.id', 'password123']],
            'template-import-guru.xlsx'
        );
    }

    public function downloadStudentsTemplate()
    {
        return $this->downloadTemplate(
            ['Nama Sekolah', 'Nama', 'Email', 'Password', 'Nama Kelas'],
            [['SMK Negeri 1 Jakarta', 'Ani Rahmawati', 'ani@student.sch.id', 'password123', 'XII RPL 1']],
            'template-import-siswa.xlsx'
        );
    }

    public function downloadCompaniesTemplate()
    {
        return $this->downloadTemplate(
            ['Nama Sekolah', 'Nama', 'Alamat', 'Provinsi', 'Kota', 'Kecamatan', 'Desa', 'Industri', 'Telepon', 'Email', 'Latitude', 'Longitude', 'Batas Jarak (m)'],
            [[
                'SMK Negeri 1 Jakarta',
                'PT Maju Jaya',
                'Jl. Industri No. 10',
                'DKI Jakarta',
                'Jakarta Pusat',
                'Gambir',
                'Gambir',
                'Teknologi Informasi',
                '021-98765432',
                'info@majujaya.com',
                '-6.17511',
                '106.86504',
                '100',
            ]],
            'template-import-perusahaan.xlsx'
        );
    }

    public function downloadSupervisorsTemplate()
    {
        return $this->downloadTemplate(
            ['Nama Sekolah', 'Nama Perusahaan', 'Nama', 'Email', 'Password'],
            [['SMK Negeri 1 Jakarta', 'PT Maju Jaya', 'Ahmad Syahputra', 'ahmad@majujaya.com', 'password123']],
            'template-import-pembimbing.xlsx'
        );
    }

    public function downloadInternshipsTemplate()
    {
        return $this->downloadTemplate(
            ['Email Siswa', 'Nama Sekolah', 'Nama Perusahaan', 'Email Pembimbing', 'Tanggal Mulai', 'Tanggal Selesai', 'Status', 'Catatan'],
            [[
                'ani@student.sch.id',
                'SMK Negeri 1 Jakarta',
                'PT Maju Jaya',
                'ahmad@majujaya.com',
                '2026-01-10',
                '2026-06-30',
                'active',
                'Magang bidang Teknologi Informasi',
            ]],
            'template-import-magang.xlsx'
        );
    }

    public function downloadSchoolAdminsTemplate()
    {
        return $this->downloadTemplate(
            ['Nama Sekolah', 'Nama', 'Email', 'Password'],
            [['SMK Negeri 1 Jakarta', 'Admin Sekolah', 'admin@smkn1jkt.sch.id', 'password123']],
            'template-import-admin-sekolah.xlsx'
        );
    }

    public function downloadAttendancePointsTemplate()
    {
        return $this->downloadTemplate(
            ['Nama Sekolah', 'Nama Perusahaan', 'Nama', 'Latitude', 'Longitude', 'Batas Jarak (m)', 'Status'],
            [[
                'SMK Negeri 1 Jakarta',
                'PT Maju Jaya',
                'Pintu Utama',
                '-6.17511',
                '106.86504',
                '100',
                'approved',
            ]],
            'template-import-titik-absensi.xlsx'
        );
    }

    public function downloadTeacherAssignmentsTemplate()
    {
        return $this->downloadTemplate(
            ['Email Guru', 'Email Siswa'],
            [['budi@smkn1jkt.sch.id', 'ani@student.sch.id']],
            'template-import-penugasan-guru.xlsx'
        );
    }

    public function downloadTeacherCompanyAssignmentsTemplate()
    {
        return $this->downloadTemplate(
            ['Email Guru', 'Nama Perusahaan'],
            [['budi@smkn1jkt.sch.id', 'PT Maju Jaya']],
            'template-import-penugasan-perusahaan.xlsx'
        );
    }

    // ─── HELPERS ─────────────────────────────────────────────

    private function handleImport(Request $request, $import, string $label)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        try {
            Excel::import($import, $request->file('file'));
            return response()->json(['message' => $label . ' berhasil diimport']);
        } catch (\Exception $e) {
            Log::error('Import ' . $label . ' failed: ' . $e->getMessage());
            return response()->json([
                'message' => 'Gagal import ' . $label . ': ' . $e->getMessage(),
            ], 422);
        }
    }

    private function downloadTemplate(array $headings, array $data, string $filename)
    {
        return Excel::download(new TemplateExport($headings, $data), $filename);
    }
}
