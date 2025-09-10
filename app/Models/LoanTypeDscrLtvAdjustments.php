<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanTypeDscrLtvAdjustments extends Model
{
    /** @use HasFactory<\Database\Factories\LoanTypeDscrLtvAdjustmentsFactory> */
    use HasFactory;

    protected $fillable = [
        'loan_type_id',
        'dscr_loan_type_id',
        'ltv_ratio_id',
        'adjustment_pct'
    ];

    public function loanType()
    {
        return $this->belongsTo(LoanType::class);
    }

    public function dscrLoanType()
    {
        return $this->belongsTo(LoanTypesDscr::class, 'dscr_loan_type_id');
    }

    public function ltvRatio()
    {
        return $this->belongsTo(LtvRatio::class);
    }
}
