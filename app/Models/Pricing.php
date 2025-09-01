<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pricing extends Model
{
    /** @use HasFactory<\Database\Factories\PricingFactory> */
    use HasFactory;
    protected $fillable = ['loan_rule_id', 'pricing_tier_id', 'interest_rate', 'lender_points'];

    public function loanRule()
    {
        return $this->belongsTo(LoanRule::class);
    }

    public function pricingTier()
    {
        return $this->belongsTo(PricingTier::class);
    }
}
