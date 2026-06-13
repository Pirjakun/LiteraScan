<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

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
        ]);

        Student::create($validated);

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
        ]);

        $student->update($validated);

        return redirect()->route('students.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Student $student)
    {
        $student->delete();
        return redirect()->route('students.index')->with('success', 'Data siswa berhasil dihapus.');
    }
}
