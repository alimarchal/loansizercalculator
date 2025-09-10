<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrePayLtvAdjustments extends Model
{
    /** @use HasFactory<\Database\Factories\PrePayLtvAdjustmentsFactory> */
    use HasFactory;

    protected $fillable = [
        'loan_type_id',
        'pre_pay_id',
        'ltv_ratio_id',
        'adjustment_pct'
    ];

    public function loanType()
    {
        return $this->belongsTo(LoanType::class);
    }

    public function prepayPeriod()
    {
        return $this->belongsTo(PrepayPeriods::class, 'pre_pay_id');
    }

    public function ltvRatio()
    {
        return $this->belongsTo(LtvRatio::class);
    }
}
