<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PropertyTypeLtvAdjustment extends Model
{
    /** @use HasFactory<\Database\Factories\PropertyTypeLtvAdjustmentFactory> */
    use HasFactory;

    protected $fillable = [
        'loan_type_id',
        'property_type_id',
        'ltv_ratio_id',
        'adjustment_pct'
    ];

    public function loanType()
    {
        return $this->belongsTo(LoanType::class);
    }

    public function propertyType()
    {
        return $this->belongsTo(PropertyType::class);
    }

    public function ltvRatio()
    {
        return $this->belongsTo(LtvRatio::class);
    }
}
