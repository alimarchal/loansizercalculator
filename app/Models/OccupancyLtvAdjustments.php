<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OccupancyLtvAdjustments extends Model
{
    /** @use HasFactory<\Database\Factories\OccupancyLtvAdjustmentsFactory> */
    use HasFactory;

    protected $fillable = [
        'loan_type_id',
        'occupancy_type_id',
        'ltv_ratio_id',
        'adjustment_pct'
    ];

    public function loanType()
    {
        return $this->belongsTo(LoanType::class);
    }

    public function occupancyType()
    {
        return $this->belongsTo(OccupancyTypes::class);
    }

    public function ltvRatio()
    {
        return $this->belongsTo(LtvRatio::class);
    }
}
