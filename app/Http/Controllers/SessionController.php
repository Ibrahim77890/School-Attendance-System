<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ClassSession;
use App\Models\ClassTable;

class SessionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $sessions = ClassSession::all();
        return view('sessions.index', compact('sessions'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $classes = ClassTable::all();
        return view('sessions.create', compact('classes'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'start_time' => 'required|date_format:H:i',
            'stop_time' => 'required|date_format:H:i|after:start_time',
        ]);

        echo "Request validation completed";

        $session = new ClassSession([
            'class_id' => $request->get('class_id'),
            'start_time' => $request->get('start_time'),
            'end_time' => $request->get('stop_time'),
            'attendance_marked' => false,
        ]);

        $session->save();

        echo $session;


        return redirect('teacher')->with('success', 'Session created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $session = ClassSession::findOrFail($id);
        return view('sessions.show', compact('session'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $session = ClassSession::findOrFail($id);
        $classes = ClassTable::all();
        return view('sessions.edit', compact('session', 'classes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        $session = ClassSession::findOrFail($id);
        $session->class_id = $request->get('class_id');
        $session->start_time = $request->get('start_time');
        $session->end_time = $request->get('end_time');
        $session->save();

        return redirect('/sessions')->with('success', 'Session updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $session = ClassSession::findOrFail($id);
        $session->delete();

        return redirect('/sessions')->with('success', 'Session deleted successfully.');
    }
}