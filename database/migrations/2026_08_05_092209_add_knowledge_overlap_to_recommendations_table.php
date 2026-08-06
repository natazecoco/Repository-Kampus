<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('recommendations', function (Blueprint $table) {
            // Menambahkan kolom knowledge_overlap setelah similarity_score
            $table->integer('knowledge_overlap')->default(0)->after('similarity_score');
        });
    }

    public function down(): void
    {
        Schema::table('recommendations', function (Blueprint $table) {
            $table->dropColumn('knowledge_overlap');
        });
    }
};