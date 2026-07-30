<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Tambahkan 'npm' karena kolom tersebut wajib diisi di tabel users
        User::updateOrCreate(
            ['email' => 'admin@repository.com'],
            [
                'name' => 'Admin Repositori',
                'npm' => '10000001',
                'role' => 'admin',
                'password' => Hash::make('password123'),
            ]
        );

        // Seed topics & sample publications for local development
        $this->call(TopicSeeder::class);
    }
}