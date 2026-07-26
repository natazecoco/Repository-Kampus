<?php

use App\Http\Controllers\Auth\StudentAuthController;
use App\Http\Controllers\PublicationController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileAccessController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DocumentController; // <-- Ini tambahan baru untuk PDF kita

Route::get('/', [PublicationController::class, 'index'])->name('home');
Route::get('/publication/{publication}', [PublicationController::class, 'show'])->name('publications.show');
Route::get('/publication/{publication}/read', [DocumentController::class, 'publicationViewer'])->name('publications.viewer');

// Rute akses file PDF yang lama (dibiarkan saja kalau masih dipakai bagian lain)
Route::get('/dokumen/{file}', [FileAccessController::class, 'show'])->name('file.akses');

Route::get('/document/{file}/stream', [DocumentController::class, 'stream'])->name('document.stream');

Route::middleware('guest')->group(function () {
    Route::get('/login', [StudentAuthController::class, 'showLoginForm'])->name('student.login');
    Route::post('/login', [StudentAuthController::class, 'login'])->name('student.login.submit');
    Route::get('/register', [StudentAuthController::class, 'showRegisterForm'])->name('student.register');
    Route::post('/register', [StudentAuthController::class, 'register'])->name('student.register.submit');
});

Route::post('/logout', [StudentAuthController::class, 'logout'])->name('student.logout');

// Route untuk pengunjung yang belum login (Guest)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Route untuk yang sudah login (Auth)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});
