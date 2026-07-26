<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('topics', function (Blueprint $table) {
            if (! Schema::hasColumn('topics', 'is_active')) {
                $table->boolean('is_active')->default(true)->after('slug');
            }
            if (! Schema::hasColumn('topics', 'sort_order')) {
                $table->integer('sort_order')->default(0)->after('is_active');
            }
        });
    }

    public function down(): void
    {
        Schema::table('topics', function (Blueprint $table) {
            if (Schema::hasColumn('topics', 'is_active')) {
                $table->dropColumn('is_active');
            }
            if (Schema::hasColumn('topics', 'sort_order')) {
                $table->dropColumn('sort_order');
            }
        });
    }
};
