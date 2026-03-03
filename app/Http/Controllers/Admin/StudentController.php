<?php

namespace App\Http\Controllers\Admin;

use App\Models\Student;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    /**
     * Display a listing of students with search and filters.
     */
    public function index(Request $request)
    {
        $search     = $request->input('search');
        $status     = $request->input('status');
        $year_level = $request->input('year_level');
        $course     = $request->input('course');

        $students = Student::query()
            ->when($search, fn ($q, $v) => $q->search($v))
            ->when($status, fn ($q, $v) => $q->where('status', $v))
            ->when($year_level, fn ($q, $v) => $q->where('year_level', $v))
            ->when($course, fn ($q, $v) => $q->where('course', $v))
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        $courses  = Student::distinct()->pluck('course');
        $statuses = ['active', 'inactive', 'graduated', 'suspended'];

        return view('admin.students.index', compact(
            'students', 'courses', 'statuses', 'search', 'status', 'year_level', 'course'
        ));
    }

    /**
     * Show the form for creating a new student.
     */
    public function create()
    {
        return view('admin.students.create');
    }

    /**
     * Store a newly created student and their user account.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name'      => 'required|string|max:255',
            'last_name'       => 'required|string|max:255',
            'middle_name'     => 'nullable|string|max:255',
            'date_of_birth'   => 'required|date',
            'gender'          => 'required|in:male,female,other',
            'email'           => 'required|email|unique:students,email|unique:users,email',
            'password'        => 'required|string|min:8|confirmed',
            'phone'           => 'nullable|string|max:20',
            'address'         => 'required|string',
            'enrollment_date' => 'required|date',
            'course'          => 'required|string|max:255',
            'year_level'      => 'required|integer|min:1|max:5',
            'gpa'             => 'nullable|numeric|min:0|max:4',
            'status'          => 'nullable|in:active,inactive,graduated,suspended',
            'remarks'         => 'nullable|string',
            'profile_photo'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Create the linked User account
        $user = User::create([
            'name'     => $validated['first_name'] . ' ' . $validated['last_name'],
            'email'    => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role'     => 'student',
        ]);

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            $validated['profile_photo'] = $request->file('profile_photo')
                ->store('students/profile-photos', 'public');
        }

        Student::create([
            ...$validated,
            'user_id'        => $user->id,
            'student_number' => $this->generateStudentNumber(),
            'status'         => $validated['status'] ?? 'active',
        ]);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student and user account created successfully.');
    }

    /**
     * Display the specified student.
     */
    public function show(Student $student)
    {
        return view('admin.students.show', compact('student'));
    }

    /**
     * Show the form for editing the specified student.
     */
    public function edit(Student $student)
    {
        return view('admin.students.edit', compact('student'));
    }

    /**
     * Update the specified student in storage.
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'first_name'      => 'required|string|max:255',
            'last_name'       => 'required|string|max:255',
            'middle_name'     => 'nullable|string|max:255',
            'date_of_birth'   => 'required|date',
            'gender'          => 'required|in:male,female,other',
            'email'           => 'required|email|unique:students,email,' . $student->id
                                 . '|unique:users,email,' . $student->user_id,
            'phone'           => 'nullable|string|max:20',
            'address'         => 'required|string',
            'enrollment_date' => 'required|date',
            'course'          => 'required|string|max:255',
            'year_level'      => 'required|integer|min:1|max:5',
            'gpa'             => 'nullable|numeric|min:0|max:4',
            'status'          => 'nullable|in:active,inactive,graduated,suspended',
            'remarks'         => 'nullable|string',
            'profile_photo'   => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Sync name and email to the linked User account
        $student->user()->update([
            'name'  => $validated['first_name'] . ' ' . $validated['last_name'],
            'email' => $validated['email'],
        ]);

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            if ($student->profile_photo) {
                Storage::disk('public')->delete($student->profile_photo);
            }
            $validated['profile_photo'] = $request->file('profile_photo')
                ->store('students/profile-photos', 'public');
        }

        $student->update($validated);

        return redirect()->route('admin.students.show', $student)
            ->with('success', 'Student updated successfully.');
    }

    /**
     * Remove the specified student and their user account from storage.
     */
    public function destroy(Student $student)
    {
        if ($student->profile_photo) {
            Storage::disk('public')->delete($student->profile_photo);
        }

        // Deletes the linked User too (cascade handles DB, we handle it explicitly)
        $student->user()->delete();
        $student->delete();

        return redirect()->route('admin.students.index')
            ->with('success', 'Student deleted successfully.');
    }

    /**
     * Generate a unique student number: YYYY0001, YYYY0002, …
     */
    private function generateStudentNumber(): string
    {
        $year        = date('Y');
        $lastStudent = Student::whereYear('created_at', $year)
            ->orderBy('student_number', 'desc')
            ->first();

        $lastNumber = $lastStudent
            ? intval(substr($lastStudent->student_number, -4))
            : 0;

        return $year . str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
    }
}