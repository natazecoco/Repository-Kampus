<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        $methods = [
            [
                'method_name' => 'Design Thinking',
                'aliases' => ['design thinking', 'metode design thinking'],
                'category' => 'development',
                'priority' => 100,
            ],
            [
                'method_name' => 'User Experience Questionnaire (UEQ)',
                'aliases' => ['user experience questionnaire', 'ueq', 'metode ueq'],
                'category' => 'testing',
                'priority' => 90,
            ],
            [
                'method_name' => 'Min-Max',
                'aliases' => ['min-max', 'min max', 'metode min-max'],
                'category' => 'analysis',
                'priority' => 90,
            ],
            [
                'method_name' => 'Technology Acceptance Model (TAM)',
                'aliases' => ['technology acceptance model', 'tam', 'model penerimaan teknologi'],
                'category' => 'research',
                'priority' => 90,
            ],
            [
                'method_name' => 'Regresi Linear Sederhana',
                'aliases' => ['regresi sederhana', 'simple linear regression'],
                'category' => 'analysis',
                'priority' => 100,
            ],
        ];

        foreach ($methods as $method) {
            DB::table('research_method_dictionaries')->updateOrInsert(
                ['method_name' => $method['method_name']],
                [
                    'aliases' => json_encode($method['aliases']),
                    'category' => $method['category'],
                    'priority' => $method['priority'],
                    'is_active' => true,
                    'updated_at' => $now,
                    'created_at' => $now,
                ],
            );
        }

        DB::table('research_method_dictionaries')
            ->where('method_name', 'Research and Development (R&D)')
            ->whereJsonContains('aliases', 'rad')
            ->get()
            ->each(function (object $method): void {
                $aliases = array_values(array_filter(
                    json_decode($method->aliases ?? '[]', true),
                    fn (string $alias): bool => strtolower($alias) !== 'rad',
                ));

                DB::table('research_method_dictionaries')
                    ->where('id', $method->id)
                    ->update(['aliases' => json_encode($aliases), 'updated_at' => now()]);
            });
    }

    public function down(): void
    {
        DB::table('research_method_dictionaries')
            ->whereIn('method_name', [
                'Design Thinking',
                'User Experience Questionnaire (UEQ)',
                'Min-Max',
                'Technology Acceptance Model (TAM)',
                'Regresi Linear Sederhana',
            ])
            ->delete();
    }
};