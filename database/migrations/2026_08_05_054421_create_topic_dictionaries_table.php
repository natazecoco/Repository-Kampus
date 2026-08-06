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
    Schema::create('topic_dictionaries', function (Blueprint $table) {
        $table->id();
        $table->string('keyword')->unique()->comment('Kata kunci pencarian (contoh: machine learning)');
        $table->string('target_topic')->comment('Topik baku tujuannya (contoh: Kecerdasan Buatan)');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topic_dictionaries');
    }
};
