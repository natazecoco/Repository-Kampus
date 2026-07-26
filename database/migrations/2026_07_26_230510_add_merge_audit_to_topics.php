<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('topics')) {
            Schema::table('topics', function (Blueprint $table) {
                if (! Schema::hasColumn('topics', 'merged_by')) {
                    $table->foreignId('merged_by')->nullable()->constrained('users')->nullOnDelete()->after('merged_into');
                }
                if (! Schema::hasColumn('topics', 'merged_at')) {
                    $table->timestamp('merged_at')->nullable()->after('merged_by');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('topics')) {
            Schema::table('topics', function (Blueprint $table) {
                if (Schema::hasColumn('topics', 'merged_at')) {
                    $table->dropColumn('merged_at');
                }
                if (Schema::hasColumn('topics', 'merged_by')) {
                    $table->dropForeign(['merged_by']);
                    $table->dropColumn('merged_by');
                }
            });
        }
    }
};
