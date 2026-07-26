<?php

namespace Database\Seeders;

use App\Models\Container;
use App\Models\Publication;
use App\Models\Topic;
use Illuminate\Database\Seeder;

class TopicSeeder extends Seeder
{
    public function run(): void
    {
        // Create some common topics
        $topics = [
            'Kecerdasan Buatan',
            'Sistem Informasi',
            'Pembelajaran Mesin',
            'Jaringan Komputer',
            'Sains Data',
        ];

        foreach ($topics as $name) {
            Topic::firstOrCreate(['slug' => \Illuminate\Support\Str::slug($name)], ['name' => $name]);
        }

        // Create a sample container + publications for local dev if none exist
        if (Container::count() === 0) {
            $container = Container::create(['name' => 'Repository Sample', 'type' => 'university']);
        } else {
            $container = Container::first();
        }

        if (Publication::count() === 0) {
            $p1 = Publication::create([
                'container_id' => $container->id,
                'type' => 'thesis',
                'title' => 'Publikasi AI Contoh',
                'author' => 'Penulis Contoh',
                'year' => 2026,
                'abstract' => 'Contoh abstrak untuk topik AI.',
                'keywords' => 'ai, contoh',
            ]);

            $p2 = Publication::create([
                'container_id' => $container->id,
                'type' => 'article',
                'title' => 'Publikasi SI Contoh',
                'author' => 'Penulis Contoh 2',
                'year' => 2025,
                'abstract' => 'Contoh abstrak untuk topik SI.',
                'keywords' => 'si, contoh',
            ]);

            $ai = Topic::where('slug', 'kecerdasan-buatan')->first();
            $si = Topic::where('slug', 'sistem-informasi')->first();

            if ($ai) { $p1->topics()->attach($ai->id); }
            if ($si) { $p2->topics()->attach($si->id); }
        }
    }
}
