<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recommendation extends Model
{
    protected $guarded = [];

    // Relasi: Banyak Recommendation dimiliki oleh Satu Publication
    public function publication()
    {
        return $this->belongsTo(Publication::class);
    }

    // Relasi: Banyak Recommendation dimiliki oleh Satu Publication yang direkomendasikan
    public function recommendedPublication()
    {
        return $this->belongsTo(Publication::class, 'recommended_id');
    }
}
