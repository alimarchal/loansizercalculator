<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrepayPeriods extends Model
{
    /** @use HasFactory<\Database\Factories\PrepayPeriodsFactory> */
    use HasFactory;

    protected $fillable = [
        'prepay_name',
        'display_order'
    ];

    public function dscrRateMatrices()
    {
        return $this->hasMany(DscrRateMatrix::class);
    }

    public function prePayLtvAdjustments()
    {
        return $this->hasMany(PrePayLtvAdjustments::class, 'pre_pay_id');
    }
}
