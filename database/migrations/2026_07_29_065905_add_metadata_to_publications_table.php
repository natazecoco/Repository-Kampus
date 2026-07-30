<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('publications', function (Blueprint $table) {
            // nullable() agar aman jika skripsi mahasiswa tidak punya data ini
            $table->string('language')->default('id')->nullable()->after('abstract'); 
            $table->string('research_method')->nullable()->after('language');
            $table->string('license')->default('Internal Use Only')->nullable()->after('research_method');
            $table->string('doi')->nullable()->after('license');
        });
    }

    public function down(): void
    {
        Schema::table('publications', function (Blueprint $table) {
            $table->dropColumn(['language', 'research_method', 'license', 'doi']);
        });
    }
};