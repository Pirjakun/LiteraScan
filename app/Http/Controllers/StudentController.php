<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class StudentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $students = Student::latest()->get();
        return view('students.index', compact('students'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $student = new Student();
        return view('students.form', compact('student'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'rfid_uid' => 'required|string|max:50|unique:students,rfid_uid',
            'nim' => 'required|string|max:20|unique:students,nim',
            'name' => 'required|string|max:100',
            'major' => 'required|string|max:100',
        ], [], [
            'nim' => 'NIS',
            'rfid_uid' => 'RFID UID',
            'name' => 'Nama',
            'major' => 'Kelas',
        ]);

        Student::create($validated);
        Cache::forget('dashboard_metrics');

        return redirect()->route('students.index')->with('success', 'Data siswa berhasil ditambahkan.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Student $student)
    {
        return view('students.form', compact('student'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'rfid_uid' => 'required|string|max:50|unique:students,rfid_uid,' . $student->id,
            'nim' => 'required|string|max:20|unique:students,nim,' . $student->id,
            'name' => 'required|string|max:100',
            'major' => 'required|string|max:100',
        ], [], [
            'nim' => 'NIS',
            'rfid_uid' => 'RFID UID',
            'name' => 'Nama',
            'major' => 'Kelas',
        ]);

        $student->update($validated);
        Cache::forget('dashboard_metrics');

        return redirect()->route('students.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        $student->delete();
        Cache::forget('dashboard_metrics');
        return redirect()->route('students.index')->with('success', 'Data siswa berhasil dihapus.');
    }

    /**
     * Export students to CSV.
     */
    public function export()
    {
        $students = Student::latest()->get();
        $filename = 'data_siswa_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($students) {
            $file = fopen('php://output', 'w');
            
            // Add UTF-8 BOM for Excel
            fputs($file, "\xEF\xBB\xBF");
            
            // Add separator directive for Excel
            fputs($file, "sep=,\r\n");
            
            // Header columns
            fputcsv($file, ['No', 'Nama', 'NIS', 'RFID UID', 'Kelas', 'Tanggal Terdaftar'], ',');

            foreach ($students as $index => $student) {
                fputcsv($file, [
                    $index + 1,
                    $student->name,
                    $student->nim,
                    $student->rfid_uid,
                    $student->major,
                    $student->created_at ? $student->created_at->format('d-m-Y H:i') : '-',
                ], ',');
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
