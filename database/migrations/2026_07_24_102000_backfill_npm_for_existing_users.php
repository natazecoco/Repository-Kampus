<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
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
    }

    public function down(): void
    {
        // Intentionally left blank. Existing data should remain stable after backfill.
    }
};
