<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClassTable;
use App\Models\ClassesStudent;
use App\Models\User;

class ClassController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $classes = ClassTable::all();
        return view('classes.index', compact('classes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $students = User::all();
        return view('classes.create', compact('students'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'user_id' => 'required|exists:users,id',
            'students' => 'required|array',
            'students.*' => 'exists:users,id',
        ]);

        $class = new ClassTable([
            'name' => $request->get('name'),
            'user_id' => $request->get('user_id'),
        ]);

        $class->save();

        foreach ($request->get('students') as $student_id) {
            ClassesStudent::create([
                'class_id' => $class->id,
                'student_id' => $student_id,
            ]);
        }

        return redirect('teacher')->with('success', 'Class created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $class = ClassTable::findOrFail($id);
        return view('classes.show', compact('class'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $class = ClassTable::findOrFail($id);
        $students = User::all();
        return view('classes.edit', compact('class', 'students'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'user_id' => 'required|exists:users,id',
            'students_id' => 'required|array',
            'students_id.*' => 'exists:users,id',
        ]);

        $class = ClassTable::findOrFail($id);
        $class->name = $request->get('name');
        $class->user_id = $request->get('user_id');
        $class->save();

        // Update class_students table
        ClassesStudent::where('class_id', $id)->delete();
        foreach ($request->get('students_id') as $student_id) {
            ClassesStudent::create([
                'class_id' => $class->id,
                'student_id' => $student_id,
            ]);
        }

        return redirect('/classes')->with('success', 'Class updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $class = ClassTable::findOrFail($id);
        $class->delete();

        return redirect('/classes')->with('success', 'Class deleted successfully.');
    }
}