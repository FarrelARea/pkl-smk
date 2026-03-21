<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Imports\ClassesImport;
use App\Imports\SchoolsImport;
use App\Imports\StudentsImport;
use App\Imports\TeachersImport;
use App\Exports\ClassesExport;
use App\Exports\SchoolsExport;
use App\Exports\StudentsExport;
use App\Exports\TeachersExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class ImportExportController extends Controller
{
    public function importSchools(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        Excel::import(new SchoolsImport, $request->file('file'));

        return response()->json(['message' => 'Schools imported successfully']);
    }

    public function importClasses(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        Excel::import(new ClassesImport, $request->file('file'));

        return response()->json(['message' => 'Classes imported successfully']);
    }

    public function importTeachers(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        Excel::import(new TeachersImport, $request->file('file'));

        return response()->json(['message' => 'Teachers imported successfully']);
    }

    public function importStudents(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls|max:10240',
        ]);

        Excel::import(new StudentsImport, $request->file('file'));

        return response()->json(['message' => 'Students imported successfully']);
    }

    public function exportSchools()
    {
        return Excel::download(new SchoolsExport, 'schools.xlsx');
    }

    public function exportClasses()
    {
        return Excel::download(new ClassesExport, 'classes.xlsx');
    }

    public function exportTeachers()
    {
        return Excel::download(new TeachersExport, 'teachers.xlsx');
    }

    public function exportStudents()
    {
        return Excel::download(new StudentsExport, 'students.xlsx');
    }
}