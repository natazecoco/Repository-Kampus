<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // Menampilkan halaman login
    public function showLogin()
    {
        return view('auth.login');
    }

    // Proses login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'npm' => 'required|digits:8',
            'password' => 'required'
        ], [
            'npm.required' => 'NPM wajib diisi.',
            'npm.digits' => 'NPM harus tepat 8 digit angka.',
        ]);

        if (Auth::attempt([
            'npm' => $credentials['npm'],
            'password' => $credentials['password'],
        ])) {
            $user = Auth::user();

            if ($user?->role !== 'student' && $user?->role !== 'admin') {
                Auth::logout();

                return back()->withErrors([
                    'npm' => 'Akun ini tidak memiliki akses ke login mahasiswa.',
                ])->onlyInput('npm');
            }

            $request->session()->regenerate();

            if ($user->role === 'admin') {
                return redirect('/admin');
            }

            return redirect()->route('home')->with('success', 'Berhasil login!');
        }

        return back()->withErrors([
            'npm' => 'NPM atau password salah.',
        ])->onlyInput('npm');
    }

    // Menampilkan halaman register
    public function showRegister()
    {
        return view('auth.register');
    }

    // Proses register
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'npm' => 'required|digits:8|unique:users,npm',
            'password' => 'required|min:8|confirmed',
        ], [
            'npm.required' => 'NPM wajib diisi.',
            'npm.digits' => 'NPM harus tepat 8 digit angka.',
            'npm.unique' => 'NPM ini sudah terdaftar di sistem.',
            'email.unique' => 'Email ini sudah digunakan.'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'npm' => $request->npm,
            'role' => 'student',
        ]);

        return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan login.');
    }

    // Proses logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Berhasil logout.');
    }
}