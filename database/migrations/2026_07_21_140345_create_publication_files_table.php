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
        Schema::create('publication_files', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel publications. cascadeOnDelete memastikan 
            // jika skripsi dihapus, semua file pecahannya ikut terhapus.
            $table->foreignId('publication_id')->constrained()->cascadeOnDelete();
            
            // Nama bagian file (contoh: "Cover", "Bab 1: Pendahuluan", "Bab 2")
            $table->string('title');
            
            // Path penyimpanan file di storage
            $table->string('file_path');
            
            // KUNCI UTAMA: Penentu apakah file ini bisa diakses publik atau harus login
            $table->enum('access_type', ['public', 'restricted'])->default('restricted');
            
            // Urutan tampilan di UI (agar Cover selalu di atas Bab 1, dst)
            $table->integer('sort_order')->default(0);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publication_files');
    }
};
