<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Cek jika kolom 'npm' belum ada, maka buat kolomnya
            if (!Schema::hasColumn('users', 'npm')) {
                // Menggunakan panjang 8 digit sesuai format Gunadarma
                $table->string('npm', 8)->nullable()->unique()->after('email');
            }
            
            // Cek jika kolom 'role' belum ada, maka buat kolomnya
            if (!Schema::hasColumn('users', 'role')) {
                $table->enum('role', ['admin', 'mahasiswa', 'umum'])->default('umum')->after('npm');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus kolom hanya jika kolomnya memang ada
            if (Schema::hasColumn('users', 'npm')) {
                $table->dropColumn('npm');
            }
            // (Opsional) Kolom role tidak kita drop di sini untuk menjaga data admin sebelumnya
        });
    }
};