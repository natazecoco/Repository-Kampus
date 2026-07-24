<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

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
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            
            // Arahkan admin ke panel Filament, sisanya ke home
            if (Auth::user()->role === 'admin') {
                return redirect('/admin');
            }
            
            return redirect()->route('home')->with('success', 'Berhasil login!');
        }

        return back()->withErrors([
            'email' => 'Email atau password salah.',
        ])->onlyInput('email');
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
            'password' => 'required|min:8|confirmed',
            // Validasi diubah: NPM boleh kosong, tapi kalau diisi WAJIB 8 digit angka
            'npm' => 'nullable|digits:8|unique:users,npm' 
        ], [
            // Pesan error kustom biar lebih jelas
            'npm.digits' => 'NPM harus tepat 8 digit angka.',
            'npm.unique' => 'NPM ini sudah terdaftar di sistem.',
            'email.unique' => 'Email ini sudah digunakan.'
        ]);

        // Logika Penentuan Role: Jika pakai email kampus, jadi mahasiswa
        $role = 'umum';
        if (Str::endsWith($request->email, '@student.gunadarma.ac.id')) {
            $role = 'mahasiswa';
        }

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'npm' => $request->npm,
            'role' => $role,
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