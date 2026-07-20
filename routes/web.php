<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PublicationController;

// Mengarahkan halaman utama (/) langsung ke fungsi index di PublicationController
Route::get('/', [PublicationController::class, 'index'])->name('home');

// Rute baru untuk detail dokumen menggunakan ID unik secara otomatis
Route::get('/publication/{publication}', [PublicationController::class, 'show'])->name('publications.show');