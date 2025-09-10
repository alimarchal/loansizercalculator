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

    // LTV Adjustment relationships
    public function ficoLtvAdjustments()
    {
        return $this->hasMany(FicoLtvAdjustment::class);
    }

    public function loanAmountLtvAdjustments()
    {
        return $this->hasMany(LoanAmountLtvAdjustment::class);
    }

    public function propertyTypeLtvAdjustments()
    {
        return $this->hasMany(PropertyTypeLtvAdjustment::class);
    }

    public function occupancyLtvAdjustments()
    {
        return $this->hasMany(OccupancyLtvAdjustments::class);
    }

    public function transactionTypeLtvAdjustments()
    {
        return $this->hasMany(TransactionTypeLtvAdjustments::class);
    }

    public function dscrLtvAdjustments()
    {
        return $this->hasMany(DscrLtvAdjustments::class);
    }

    public function prePayLtvAdjustments()
    {
        return $this->hasMany(PrePayLtvAdjustments::class);
    }

    public function loanTypeDscrLtvAdjustments()
    {
        return $this->hasMany(LoanTypeDscrLtvAdjustments::class);
    }
}
