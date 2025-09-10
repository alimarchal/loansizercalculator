<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FicoLtvAdjustment extends Model
{
    /** @use HasFactory<\Database\Factories\FicoLtvAdjustmentFactory> */
    use HasFactory;

    protected $fillable = [
        'loan_type_id',
        'fico_band_id',
        'ltv_ratio_id',
        'adjustment_pct'
    ];

    public function loanType()
    {
        return $this->belongsTo(LoanType::class);
    }

    public function ficoBand()
    {
        return $this->belongsTo(FicoBand::class);
    }

    public function ltvRatio()
    {
        return $this->belongsTo(LtvRatio::class);
    }
}
