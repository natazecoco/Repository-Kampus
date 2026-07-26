<?php

use App\Http\Controllers\Auth\StudentAuthController;
use App\Http\Controllers\DocumentController;
use App\Http\Controllers\FileAccessController;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\TopicBulkController;
use App\Http\Controllers\AuthController;
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
Route::get('/publication/{publication}', [PublicationController::class, 'show'])->name('publications.show');
Route::get('/publication/{publication}/read', [DocumentController::class, 'publicationViewer'])->name('publications.viewer');

// Rute akses file PDF yang lama (dibiarkan saja kalau masih dipakai bagian lain)
Route::get('/dokumen/{file}', [FileAccessController::class, 'show'])->name('file.akses');

Route::get('/document/{file}/stream', [DocumentController::class, 'stream'])->name('document.stream');

Route::middleware(['auth'])->prefix('admin/topics')->group(function () {
    Route::get('/export', [TopicBulkController::class, 'export'])->name('admin.topics.export');
    Route::get('/import', [TopicBulkController::class, 'showImport'])->name('admin.topics.import');
    Route::post('/import', [TopicBulkController::class, 'import'])->name('admin.topics.import.process');
    Route::get('/duplicates', [TopicBulkController::class, 'duplicates'])->name('admin.topics.duplicates');
});

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

// Route untuk pengunjung yang belum login (Guest) - generic auth (if used)
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
