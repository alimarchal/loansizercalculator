<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PricingTier extends Model
{
    /** @use HasFactory<\Database\Factories\PricingTierFactory> */
    use HasFactory;
    protected $fillable = ['price_range', 'min_amount', 'max_amount'];

    public function pricings()
    {
        return $this->hasMany(Pricing::class);
    }
}
