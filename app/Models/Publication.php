<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Jobs\GenerateRecommendations;

class Publication extends Model
{
    protected $guarded = [];

    // Relasi: Banyak Publication dimiliki oleh Satu Container
    public function container()
    {
        return $this->belongsTo(Container::class);
    }

    // Fungsi ini akan otomatis berjalan saat ada kejadian (event) di database
    protected static function booted()
    {
        // Berjalan SATU KALI saat ada skripsi BARU ditambah
        static::created(function ($publication) {
            GenerateRecommendations::dispatch($publication);
        });

        // Berjalan setiap kali skripsi DIEDIT (misal judulnya direvisi)
        static::updated(function ($publication) {
            GenerateRecommendations::dispatch($publication);
        });
    }

    public function files()
    {
        return $this->hasMany(PublicationFile::class)->orderBy('sort_order');
    }
}