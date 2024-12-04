<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attendance;
use App\Models\User;
use App\Models\ClassSession;

class AttendanceController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $attendances = Attendance::all();
        return view('attendance.index', compact('attendances'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $students = User::all();
        $classSessions = ClassSession::all();
        return view('attendance.create', compact('students', 'classSessions'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'students' => 'required|array', // Ensure an array of students is provided
            'students.*' => 'boolean', // Each student should either be checked (true) or unchecked (false)
            'class_session_id' => 'required|exists:class_sessions,id', // Ensure class session exists
        ]);

        // Loop through the students and store their attendance
        foreach ($request->get('students') as $studentId => $isPresent) {
            echo $studentId . "+" .  $isPresent;
            // Only store attendance if the student is marked as present
            $singleStudentAttendance = new Attendance([
                'student_id' => $studentId,
                'class_session_id' => $request->get('class_session_id'),
                'is_present' => (bool) $isPresent,
            ]);

            $singleStudentAttendance->save();
            echo $singleStudentAttendance;
        }
            $classSessionId = $request->get('class_session_id');
        ClassSession::where('id', $classSessionId)
            ->update(['attendance_marked' => true]);

        return redirect('/teacher')->with('success', 'Attendance recorded successfully.');
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $attendance = Attendance::findOrFail($id);
        return view('attendance.show', compact('attendance'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $attendance = Attendance::findOrFail($id);
        $students = User::all();
        $classSessions = ClassSession::all();
        return view('attendance.edit', compact('attendance', 'students', 'classSessions'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'student_id' => 'required|exists:users,id',
            'class_session_id' => 'required|exists:class_sessions,id',
            'is_present' => 'required|boolean',
        ]);

        $attendance = Attendance::findOrFail($id);
        $attendance->student_id = $request->get('student_id');
        $attendance->class_session_id = $request->get('class_session_id');
        $attendance->is_present = $request->get('is_present');
        $attendance->save();

        return redirect('/attendance')->with('success', 'Attendance updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $attendance = Attendance::findOrFail($id);
        $attendance->delete();

        return redirect('/attendance')->with('success', 'Attendance deleted successfully.');
    }
}
