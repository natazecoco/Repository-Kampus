<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('research_method_dictionaries')) {
            Schema::create('research_method_dictionaries', function (Blueprint $table): void {
                $table->id();
                $table->string('method_name');
                $table->json('aliases')->nullable();
                $table->text('description')->nullable();
                $table->string('category')->default('research');
                $table->unsignedInteger('priority')->default(50);
                $table->boolean('is_active')->default(true);
                $table->timestamps();
            });
        } else {
            Schema::table('research_method_dictionaries', function (Blueprint $table): void {
                if (! Schema::hasColumn('research_method_dictionaries', 'category')) {
                    $table->string('category')->default('research');
                }
                if (! Schema::hasColumn('research_method_dictionaries', 'priority')) {
                    $table->unsignedInteger('priority')->default(50);
                }
            });
        }

        $now = now();
        $rows = [];
        foreach (config('research.methods', []) as $keyword => $label) {
            $rows[] = [
            'method_name' => $label,
            'aliases' => json_encode([$keyword]),
            'description' => null,
                'category' => $this->categoryFor($label),
                'priority' => $this->priorityFor($label),
                'is_active' => true,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if ($rows !== [] && DB::table('research_method_dictionaries')->count() === 0) {
            DB::table('research_method_dictionaries')->insert($rows);
        }
    }

    private function categoryFor(string $label): string
    {
        $label = strtolower($label);

        if (str_contains($label, 'testing') || str_contains($label, 'usability')) {
            return 'testing';
        }

        if (str_contains($label, 'machine learning')
            || str_contains($label, 'deep learning')
            || str_contains($label, 'neural')
            || str_contains($label, 'random forest')
            || str_contains($label, 'naive bayes')
            || str_contains($label, 'k-means')
            || str_contains($label, 'data mining')) {
            return 'technology';
        }

        if (str_contains($label, 'waterfall')
            || str_contains($label, 'agile')
            || str_contains($label, 'rapid application')
            || str_contains($label, 'prototyp')
            || str_contains($label, 'rup')
            || str_contains($label, 'sdlc')
            || str_contains($label, 'devops')) {
            return 'development';
        }

        return 'research';
    }

    private function priorityFor(string $label): int
    {
        return $this->categoryFor($label) === 'technology' ? 40 : 100;
    }

    public function down(): void
    {
        Schema::dropIfExists('research_method_dictionaries');
    }
};
