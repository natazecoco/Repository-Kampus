<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('publications', function (Blueprint $table) {
            $table->enum('type', ['thesis', 'scientific_paper', 'article', 'book', 'proceeding', 'report'])
                ->default('thesis')
                ->change();
        });

        Schema::table('publication_files', function (Blueprint $table) {
            $table->string('section')->nullable()->after('title');
            $table->enum('visibility', ['public', 'authenticated', 'admin'])->default('authenticated')->after('access_type');
            $table->boolean('allow_download')->default(false)->after('visibility');
        });

        DB::table('publication_files')
            ->where('access_type', 'public')
            ->update([
                'visibility' => 'public',
                'allow_download' => true,
            ]);

        DB::table('publication_files')
            ->where('access_type', 'restricted')
            ->update([
                'visibility' => 'authenticated',
                'allow_download' => false,
            ]);
    }

    public function down(): void
    {
        Schema::table('publication_files', function (Blueprint $table) {
            $table->dropColumn(['section', 'visibility', 'allow_download']);
        });

        Schema::table('publications', function (Blueprint $table) {
            $table->enum('type', ['thesis', 'article', 'book'])->default('thesis')->change();
        });
    }
};
