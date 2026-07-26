<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('topics', function (Blueprint $table) {
            if (! Schema::hasColumn('topics', 'merged_into')) {
                $table->foreignId('merged_into')->nullable()->constrained('topics')->nullOnDelete()->after('sort_order');
            }
        });

        if (! Schema::hasTable('topic_merge_backups')) {
            Schema::create('topic_merge_backups', function (Blueprint $table) {
                $table->id();
                $table->foreignId('source_id')->constrained('topics')->cascadeOnDelete();
                $table->foreignId('target_id')->constrained('topics')->cascadeOnDelete();
                $table->json('publication_ids');
                $table->boolean('undone')->default(false);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::table('topics', function (Blueprint $table) {
            if (Schema::hasColumn('topics', 'merged_into')) {
                $table->dropForeign(['merged_into']);
                $table->dropColumn('merged_into');
            }
        });

        Schema::dropIfExists('topic_merge_backups');
    }
};
