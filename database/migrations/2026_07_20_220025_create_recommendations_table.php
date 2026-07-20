<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('recommendations', function (Blueprint $table) {
            $table->id();
            // ID skripsi yang sedang dilihat
            $table->foreignId('publication_id')->constrained()->cascadeOnDelete();
            
            // ID skripsi yang direkomendasikan
            $table->foreignId('recommended_id')->constrained('publications')->cascadeOnDelete();
            
            // Skor kemiripan (untuk ditampilkan di view)
            $table->float('similarity_score', 8, 4);
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('recommendations');
    }
};