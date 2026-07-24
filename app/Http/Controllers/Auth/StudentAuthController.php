<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class StudentAuthController extends Controller
{
    public function showLoginForm()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'npm' => ['required', 'string', 'digits:8'],
            'password' => ['required', 'string'],
        ]);

        if (! Auth::attempt([
            'npm' => $credentials['npm'],
            'password' => $credentials['password'],
        ], $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'npm' => ['NPM atau password salah.'],
            ]);
        }

        $user = Auth::user();

        if ($user?->role !== 'student') {
            Auth::logout();

            throw ValidationException::withMessages([
                'npm' => ['Akun ini tidak dapat masuk melalui halaman login mahasiswa.'],
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended('/');
    }

    public function showRegisterForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'npm' => ['required', 'string', 'digits:8', 'unique:users,npm'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'npm' => $data['npm'],
            'role' => 'student',
            'password' => Hash::make($data['password']),
        ]);

        Auth::login($user);

        $request->session()->regenerate();

        return redirect()->intended('/');
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }
}
