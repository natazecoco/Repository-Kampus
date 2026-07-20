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
        Schema::create('publications', function (Blueprint $table) {
            $table->id();
            
            // Relasi ke tabel containers (Wajib ada)
            // cascadeOnDelete memastikan kalau jurnal/univ dihapus, skripsinya ikut terhapus biar nggak jadi data yatim
            $table->foreignId('container_id')->constrained()->cascadeOnDelete();
            
            $table->string('title');
            $table->string('author');
            $table->year('year');
            
            // Jenis karya, defaultnya kita set 'thesis' (skripsi)
            $table->enum('type', ['thesis', 'article', 'book'])->default('thesis');
            
            // Data WAJIB untuk algoritma Content-Based Filtering (TF-IDF)
            $table->text('abstract');
            $table->string('keywords');
            
            // Opsional: Untuk menyimpan lokasi file PDF nanti
            $table->string('file_path')->nullable(); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publications');
    }
};
