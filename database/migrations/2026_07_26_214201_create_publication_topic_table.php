<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('publication_topic')) {
            Schema::create('publication_topic', function (Blueprint $table) {
                $table->id();
                $table->foreignId('publication_id')->constrained()->cascadeOnDelete();
                $table->foreignId('topic_id')->constrained('topics')->cascadeOnDelete();
                $table->unique(['publication_id', 'topic_id']);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('publication_topic');
    }
};
