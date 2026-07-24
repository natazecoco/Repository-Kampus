<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            if (! Schema::hasColumn('users', 'npm')) {
                $table->string('npm', 8)->nullable()->unique();
            }
        });

        $users = DB::table('users')->whereNull('npm')->orWhere('npm', '')->get();

        foreach ($users as $user) {
            $candidate = str_pad((string) ($user->id + 10000000), 8, '0', STR_PAD_LEFT);
            $counter = 1;

            while (DB::table('users')->where('npm', $candidate)->where('id', '!=', $user->id)->exists()) {
                $candidate = str_pad((string) ($user->id + 10000000 + $counter), 8, '0', STR_PAD_LEFT);
                $counter++;
            }

            DB::table('users')->where('id', $user->id)->update([
                'npm' => $candidate,
            ]);
        }

        Schema::table('users', function (Blueprint $table) {
            $table->string('npm', 8)->nullable(false)->change();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['npm']);
            $table->dropColumn('npm');
        });
    }
};
