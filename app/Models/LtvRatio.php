<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LtvRatio extends Model
{
    /** @use HasFactory<\Database\Factories\LtvRatioFactory> */
    use HasFactory;

    protected $fillable = [
        'ratio_range',
        'ltv_min',
        'ltv_max',
        'display_order'
    ];

    public function dscrRateMatrices()
    {
        return $this->hasMany(DscrRateMatrix::class);
    }
}
