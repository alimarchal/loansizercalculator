<?php

namespace App\Models;

use App\Traits\UserTracking;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoanType extends Model
{
    /** @use HasFactory<\Database\Factories\LoanTypeFactory> */
    use HasFactory, UserTracking, SoftDeletes;

    protected $fillable = [
        'name',
        'loan_program',
    ];

    public function experiences()
    {
        return $this->hasMany(Experience::class);
    }

    public function states()
    {
        return $this->belongsToMany(State::class, 'loan_type_states');
    }

    public function propertyTypes()
    {
        return $this->belongsToMany(PropertyType::class, 'loan_type_property_types');
    }

    // LTV Adjustment relationships
    public function ficoLtvAdjustments()
    {
        return $this->hasMany(FicoLtvAdjustment::class);
    }

    public function loanAmountLtvAdjustments()
    {
        return $this->hasMany(LoanAmountLtvAdjustment::class);
    }

    public function propertyTypeLtvAdjustments()
    {
        return $this->hasMany(PropertyTypeLtvAdjustment::class);
    }

    public function occupancyLtvAdjustments()
    {
        return $this->hasMany(OccupancyLtvAdjustments::class);
    }

    public function transactionTypeLtvAdjustments()
    {
        return $this->hasMany(TransactionTypeLtvAdjustments::class);
    }

    public function dscrLtvAdjustments()
    {
        return $this->hasMany(DscrLtvAdjustments::class);
    }

    public function prePayLtvAdjustments()
    {
        return $this->hasMany(PrePayLtvAdjustments::class);
    }

    public function loanTypeDscrLtvAdjustments()
    {
        return $this->hasMany(LoanTypeDscrLtvAdjustments::class);
    }
}
