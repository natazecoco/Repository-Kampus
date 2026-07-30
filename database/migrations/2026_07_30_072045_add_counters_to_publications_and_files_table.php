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
        Schema::table('publications', function (Blueprint $table) {
            $table->unsignedInteger('views_count')->default(0)->after('abstract');
        });

        Schema::table('publication_files', function (Blueprint $table) {
            $table->unsignedInteger('downloads_count')->default(0)->after('allow_download');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('publications', function (Blueprint $table) {
            $table->dropColumn('views_count');
        });

        Schema::table('publication_files', function (Blueprint $table) {
            $table->dropColumn('downloads_count');
        });
    }
};