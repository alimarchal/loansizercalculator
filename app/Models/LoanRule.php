<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanRule extends Model
{
    /** @use HasFactory<\Database\Factories\LoanRuleFactory> */
    use HasFactory;

    protected $fillable = [
        'experience_id',
        'fico_band_id',
        'transaction_type_id',
        'max_total_loan',
        'max_budget',
    ];

    public function experience()
    {
        return $this->belongsTo(Experience::class);
    }

    public function ficoBand()
    {
        return $this->belongsTo(FicoBand::class);
    }

    public function transactionType()
    {
        return $this->belongsTo(TransactionType::class);
    }

    public function rehabLimits()
    {
        return $this->hasMany(RehabLimit::class);
    }

    public function pricings()
    {
        return $this->hasMany(Pricing::class);
    }
}
