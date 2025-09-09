<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanAmount extends Model
{
    /** @use HasFactory<\Database\Factories\LoanAmountFactory> */
    use HasFactory;

    protected $fillable = [
        'range_name',
        'min_amount',
        'max_amount',
        'display_order'
    ];

    public function dscrRateMatrices()
    {
        return $this->hasMany(DscrRateMatrix::class);
    }
}
