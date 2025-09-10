<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanAmountLtvAdjustment extends Model
{
    /** @use HasFactory<\Database\Factories\LoanAmountLtvAdjustmentFactory> */
    use HasFactory;

    protected $fillable = [
        'loan_type_id',
        'loan_amount_id',
        'ltv_ratio_id',
        'adjustment_pct'
    ];

    public function loanType()
    {
        return $this->belongsTo(LoanType::class);
    }

    public function loanAmount()
    {
        return $this->belongsTo(LoanAmount::class);
    }

    public function ltvRatio()
    {
        return $this->belongsTo(LtvRatio::class);
    }
}
