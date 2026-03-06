<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;

class StudentController extends Controller
{
    /**
     * Display a listing of the students with search.
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $status = $request->input('status');
        $course = $request->input('course');
        
        $students = Student::query()
            ->when($search, function ($query, $search) {
                return $query->search($search);
            })
            ->when($status, function ($query, $status) {
                return $query->where('status', $status);
            })
            ->when($course, function ($query, $course) {
                return $query->where('course', $course);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        // Get unique courses for filter dropdown
        $courses = Student::distinct('course')->pluck('course');
        $statuses = ['active', 'inactive', 'graduated', 'suspended'];

        return view('students.index', compact('students', 'courses', 'statuses', 'search', 'status', 'course'));
    }

    /**
     * Show the form for creating a new student.
     */
    public function create()
    {
        return view('students.create');
    }

    /**
     * Store a newly created student in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'email' => 'required|email|unique:students,email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'phone' => 'nullable|string|max:20',
            'address' => 'required|string',
            'enrollment_date' => 'required|date',
            'course' => 'required|string|max:255',
            'year_level' => 'required|integer|min:1|max:5',
            'gpa' => 'nullable|numeric|min:0|max:4',
            'status' => 'nullable|in:active,inactive,graduated,suspended',
            'remarks' => 'nullable|string',
            'profile_photo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Create user account for student
        $user = User::create([
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'student',
        ]);

        // Generate unique student number
        $validated['student_number'] = $this->generateStudentId();
        
        // Set default status
        $validated['status'] = $validated['status'] ?? 'active';
        
        // Associate user with student
        $validated['user_id'] = $user->id;

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            $path = $request->file('profile_photo')->store('students/profile-photos', 'public');
            $validated['profile_photo'] = $path;
        }

        Student::create($validated);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student and user account created successfully.');
    }

    /**
     * Display the specified student.
     */
    public function show(Student $student)
    {
        return view('students.show', compact('student'));
    }

    /**
     * Show the form for editing the specified student.
     */
    public function edit(Student $student)
    {
        return view('students.edit', compact('student'));
    }

    /**
     * Update the specified student in storage.
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:male,female,other',
            'email' => 'required|email|unique:students,email,' . $student->id,
            'phone' => 'nullable|string|max:20',
            'address' => 'required|string',
            'enrollment_date' => 'required|date',
            'course' => 'required|string|max:255',
            'year_level' => 'required|integer|min:1|max:5',
            'gpa' => 'nullable|numeric|min:0|max:4',
            'remarks' => 'nullable|string',
        ]);

        // Handle profile photo upload
        if ($request->hasFile('profile_photo')) {
            // Delete old photo
            if ($student->profile_photo) {
                Storage::disk('public')->delete($student->profile_photo);
            }
            
            $path = $request->file('profile_photo')->store('students/profile-photos', 'public');
            $validated['profile_photo'] = $path;
        }

        $student->update($validated);

        return redirect()->route('admin.students.index')
            ->with('success', 'Student updated successfully.');
    }

    /**
     * Remove the specified student from storage.
     */
    public function destroy(Student $student)
    {
        // Delete profile photo if exists
        if ($student->profile_photo) {
            Storage::disk('public')->delete($student->profile_photo);
        }

        $student->delete();

        return redirect()->route('admin.students.index')
            ->with('success', 'Student deleted successfully.');
    }

    /**
     * Generate unique student number
     */
    private function generateStudentId()
    {
        $year = date('Y');
        $lastStudent = Student::whereYear('created_at', $year)
            ->orderBy('student_number', 'desc')
            ->first();

        if ($lastStudent) {
            $lastNumber = intval(substr($lastStudent->student_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }

        return $year . $newNumber;
    }
}