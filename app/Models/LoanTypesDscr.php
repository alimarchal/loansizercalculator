<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanTypesDscr extends Model
{
    /** @use HasFactory<\Database\Factories\LoanTypesDscrFactory> */
    use HasFactory;

    protected $fillable = [
        'loan_type',
        'display_order'
    ];

    public function dscrRateMatrices()
    {
        return $this->hasMany(DscrRateMatrix::class);
    }
}
