<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $request->session()->put('user', $user);
            if($user->role_id === 1) {
                return redirect()->intended('teacher');
            } else {
                return redirect()->intended('student');
            }
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    public function register(Request $request)
    {
        $request->validate([
            'fullName' => 'required|string|max:200',
            'email' => 'required|string|email|max:200|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|string|in:student,teacher',
        ]);

        $role_id = $request->get('role') === 'student' ? 2 : 1;

        $user = new User([
            'fullName' => $request->get('fullName'),
            'email' => $request->get('email'),
            'password' => bcrypt($request->get('password')),
            'role_id' => $role_id,
        ]);

        $user->save();

        Auth::login($user);
        $request->session()->put('user', $user);

        return redirect()->intended('login');
    }

}