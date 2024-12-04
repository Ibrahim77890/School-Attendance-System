<?php

namespace App\Http\Controllers;

use App\Models\ClassesStudent;
use App\Models\ClassSession;
use App\Models\ClassTable;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    // Get all students with role_id = 2
    $students = User::where('role_id', 2)->get();

    // Get all classes for the authenticated user
    $classes = ClassTable::where('user_id', Auth::user()->id)->get();
    $classIds = $classes->pluck('id');

    $studentsClasses = ClassesStudent::whereIn('class_id', $classes->pluck('id'))->join('users', 'classes_students.student_id', '=', 'users.id')->select(
        'classes_students.id as id',
        'classes_students.class_id as class_id',
        'classes_students.student_id as student_id',
        'users.fullName as student_name'
    )->get();

    $sessions = ClassSession::whereIn('class_id', $classIds)
    ->join('classes', 'class_sessions.class_id', '=', 'classes.id')
    ->select(
        'class_sessions.id as session_id',
        'class_sessions.start_time as start_time',
        'class_sessions.end_time as end_time',
        'class_sessions.attendance_marked as attendance_marked',
        'classes.id as class_id',
        'classes.name as class_name'
        )
        ->get();

        $studentsByClass = $studentsClasses->groupBy('class_id');

        // Combine sessions with their respective students
        $sessionsWithStudents = $sessions->map(function ($session) use ($studentsByClass) {
            // Get the students for the current session's class_id
            $students = isset($studentsByClass[$session->class_id]) ? $studentsByClass[$session->class_id] : collect();
            // Attach the students to the session
            $session->students = $students;
            return $session;
        });

        // echo $sessionsWithStudents;

    // Return the teacher view with all data
    return view('teacher', compact('students', 'classes', 'sessions', 'studentsClasses', 'sessionsWithStudents'));	
}

public function studentDashboard()
    {
        $studentId = Auth::user()->id;
        $sessions = ClassSession::join('classes', 'class_sessions.class_id', '=', 'classes.id') // Join with ClassTable
        ->leftJoin('attendance', 'class_sessions.id', '=', 'attendance.class_session_id') // Left join with Attendance
        ->leftJoin('users as students', 'attendance.student_id', '=', 'students.id') // Left join with User (Student)
        ->select(
            'class_sessions.id as session_id',
            'class_sessions.start_time',
            'class_sessions.end_time',
            'class_sessions.attendance_marked',
            'classes.name as class_name',
            'attendance.id as attendance_id',
            'attendance.is_present',
            'students.id as student_id',
            'students.fullname as student_name'
        )
        ->where('attendance.student_id', '=', $studentId)
        ->get();

    // dd($sessions);
    //     dd($sessions);

        // echo $sessions;

        return view('student', compact('sessions'));
    }


    public function student()
    {
        return view('student');
    }

    public function teacher()
    {
        return view('teacher');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'fullName' => 'required|string|max:200',
            'email' => 'required|string|email|max:200|unique:users',
            'password' => 'required|string|min:8',
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = new User([
            'fullName' => $request->get('fullName'),
            'email' => $request->get('email'),
            'password' => bcrypt($request->get('password')),
            'role_id' => $request->get('role_id'),
        ]);

        $user->save();

        return redirect('welcome')->with('success', 'User created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::findOrFail($id);
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user = User::findOrFail($id);
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'fullName' => 'required|string|max:200',
            'email' => 'required|string|email|max:200|unique:users,email,' . $id,
            'password' => 'nullable|string|min:8',
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = User::findOrFail($id);
        $user->fullName = $request->get('fullName');
        $user->email = $request->get('email');
        if ($request->get('password')) {
            $user->password = bcrypt($request->get('password'));
        }
        $user->role_id = $request->get('role_id');
        $user->save();

        return redirect('/users')->with('success', 'User updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return redirect('/users')->with('success', 'User deleted successfully.');
    }
}