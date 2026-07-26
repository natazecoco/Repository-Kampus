<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('topic_merge_backups') && ! Schema::hasColumn('topic_merge_backups', 'added_publication_ids')) {
            Schema::table('topic_merge_backups', function (Blueprint $table) {
                $table->json('added_publication_ids')->nullable()->after('publication_ids');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('topic_merge_backups') && Schema::hasColumn('topic_merge_backups', 'added_publication_ids')) {
            Schema::table('topic_merge_backups', function (Blueprint $table) {
                $table->dropColumn('added_publication_ids');
            });
        }
    }
};
