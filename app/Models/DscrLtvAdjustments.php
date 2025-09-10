<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DscrLtvAdjustments extends Model
{
    /** @use HasFactory<\Database\Factories\DscrLtvAdjustmentsFactory> */
    use HasFactory;

    protected $fillable = [
        'loan_type_id',
        'dscr_range_id',
        'ltv_ratio_id',
        'adjustment_pct'
    ];

    public function loanType()
    {
        return $this->belongsTo(LoanType::class);
    }

    public function dscrRange()
    {
        return $this->belongsTo(DscrRanges::class);
    }

    public function ltvRatio()
    {
        return $this->belongsTo(LtvRatio::class);
    }
}
