<?php

use App\Http\Controllers\Auth\StudentAuthController;
use App\Http\Controllers\PublicationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [PublicationController::class, 'index'])->name('home');
Route::get('/publication/{publication}', [PublicationController::class, 'show'])->name('publications.show');

Route::middleware('guest')->group(function () {
    Route::get('/login', [StudentAuthController::class, 'showLoginForm'])->name('student.login');
    Route::post('/login', [StudentAuthController::class, 'login'])->name('student.login.submit');
    Route::get('/register', [StudentAuthController::class, 'showRegisterForm'])->name('student.register');
    Route::post('/register', [StudentAuthController::class, 'register'])->name('student.register.submit');
});

Route::post('/logout', [StudentAuthController::class, 'logout'])->name('student.logout');