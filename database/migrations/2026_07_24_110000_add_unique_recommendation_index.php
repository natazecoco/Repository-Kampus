<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $duplicates = DB::table('recommendations')
            ->select('publication_id', 'recommended_id', DB::raw('MAX(id) as keep_id'))
            ->groupBy('publication_id', 'recommended_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $duplicate) {
            DB::table('recommendations')
                ->where('publication_id', $duplicate->publication_id)
                ->where('recommended_id', $duplicate->recommended_id)
                ->where('id', '!=', $duplicate->keep_id)
                ->delete();
        }

        Schema::table('recommendations', function (Blueprint $table) {
            $table->unique(['publication_id', 'recommended_id']);
        });
    }

    public function down(): void
    {
        Schema::table('recommendations', function (Blueprint $table) {
            $table->dropUnique(['publication_id', 'recommended_id']);
        });
    }
};
