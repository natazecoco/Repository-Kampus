<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class TopicDictionary extends Model
{
    // Izinkan kolom ini diisi oleh Filament
    protected $fillable = [
        'keyword',
        'target_topic',
    ];

    protected static function booted(): void
    {
        static::saved(function (): void {
            Cache::forget('topic_dictionary_mappings');
        });

        static::deleted(function (): void {
            Cache::forget('topic_dictionary_mappings');
        });
    }
}