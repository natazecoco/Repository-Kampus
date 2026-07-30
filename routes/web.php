<?php

use App\Http\Controllers\Auth\StudentAuthController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\FileAccessController;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BookmarkController;
use Illuminate\Support\Facades\Route;

// Ensure legacy named route exists in test environments where route registration ordering may differ
if (! Route::has('student.login')) {
    Route::get('/student/login', function () { return redirect('/login'); })->name('student.login');
}
if (! Route::has('student.register')) {
    Route::get('/student/register', function () { return redirect('/register'); })->name('student.register');
}

Route::get('/', [PublicationController::class, 'index'])->name('home');
Route::get('/topic/{slug}', [PublicationController::class, 'index'])->name('topic.show');

// [FASE 2B] Route Download & Viewer (Disamakan strukturnya dengan menerima {publication} dan {file})
Route::get('/publication/{publication}/file/{file}/download', [PublicationController::class, 'downloadFile'])->name('publications.files.download');
Route::get('/publication/{publication}', [PublicationController::class, 'show'])->name('publications.show');
Route::get('/publication/{publication}/read', [DocumentController::class, 'publicationViewer'])->name('publications.viewer');

// Rute akses file PDF yang lama (dibiarkan saja kalau masih dipakai bagian lain)
Route::get('/dokumen/{file}', [FileAccessController::class, 'show'])->name('file.akses');
Route::get('/document/{file}/stream', [DocumentController::class, 'stream'])->name('document.stream');

// Student (mahasiswa) auth routes
Route::middleware('guest')->group(function () {
    Route::get('/student/login', [StudentAuthController::class, 'showLoginForm'])->name('student.login');
    Route::post('/student/login', [StudentAuthController::class, 'login'])->name('student.login.submit');
    Route::get('/student/register', [StudentAuthController::class, 'showRegisterForm'])->name('student.register');
    Route::post('/student/register', [StudentAuthController::class, 'register'])->name('student.register.submit');

    // backward-compatible routes used in some places
    Route::get('/mahasiswa/login', [StudentAuthController::class, 'showLoginForm'])->name('login');
    Route::post('/mahasiswa/login', [StudentAuthController::class, 'login'])->name('login.submit');
    Route::get('/mahasiswa/register', [StudentAuthController::class, 'showRegisterForm'])->name('register');
    Route::post('/mahasiswa/register', [StudentAuthController::class, 'register'])->name('register.submit');
});

Route::post('/student/logout', [StudentAuthController::class, 'logout'])->name('student.logout');
Route::post('/mahasiswa/logout', [StudentAuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [StudentAuthController::class, 'dashboard'])->name('student.dashboard');
    Route::post('/dashboard/profile', [StudentAuthController::class, 'updateProfile'])->name('student.profile.update');
    Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('bookmarks.index');
    Route::post('/bookmarks', BookmarkController::class)->name('bookmarks.toggle');
    Route::post('/topics/{topic}/preference', [BookmarkController::class, 'preference'])->name('topics.preference');
});

// Route untuk pengunjung yang belum login (Guest) - generic auth (jika dipakai admin)
Route::middleware('guest')->group(function () {
    Route::get('/admin-login', [AuthController::class, 'showLogin'])->name('admin.login');
    Route::post('/admin-login', [AuthController::class, 'login']);
    
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});