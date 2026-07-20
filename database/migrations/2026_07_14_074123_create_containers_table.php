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
        Schema::create('containers', function (Blueprint $table) {
            $table->id();
            $table->string('name'); 
            
            // Menggunakan enum agar jenis wadah terkunci dengan rapi
            $table->enum('type', ['university', 'journal', 'publisher']); 
            
            // Kolom nullable (bisa kosong) untuk menyimpan nomor ISSN atau ISBN jika ada
            $table->string('identifier')->nullable(); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('containers');
    }
};
