<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublicationFile extends Model
{
    protected $guarded = [];

    // Relasi kembali ke Publikasi
    public function publication()
    {
        return $this->belongsTo(Publication::class);
    }
}