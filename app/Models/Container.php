<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Container extends Model
{
    // Mengizinkan semua kolom diisi (mass assignment)
    protected $guarded = [];

    // Relasi: Satu Container punya Banyak Publication
    public function publications()
    {
        return $this->hasMany(Publication::class);
    }
}