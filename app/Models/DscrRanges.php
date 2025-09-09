<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DscrRanges extends Model
{
    /** @use HasFactory<\Database\Factories\DscrRangesFactory> */
    use HasFactory;

    protected $fillable = [
        'range_name',
        'min_dscr',
        'max_dscr',
        'display_order'
    ];

    public function dscrRateMatrices()
    {
        return $this->hasMany(DscrRateMatrix::class);
    }
}
