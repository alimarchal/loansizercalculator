<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionTypeLtvAdjustments extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionTypeLtvAdjustmentsFactory> */
    use HasFactory;

    protected $fillable = [
        'loan_type_id',
        'transaction_type_id',
        'ltv_ratio_id',
        'adjustment_pct'
    ];

    public function loanType()
    {
        return $this->belongsTo(LoanType::class);
    }

    public function transactionType()
    {
        return $this->belongsTo(TransactionType::class);
    }

    public function ltvRatio()
    {
        return $this->belongsTo(LtvRatio::class);
    }
}
