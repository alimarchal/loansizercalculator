<?php

namespace App\Models;

use App\Traits\UserTracking;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PropertyType extends Model
{
    /** @use HasFactory<\Database\Factories\PropertyTypeFactory> */
    use HasFactory, UserTracking, SoftDeletes;

    protected $fillable = [
        'name',
    ];

    public function loanTypes()
    {
        return $this->belongsToMany(LoanType::class, 'loan_type_property_types');
    }

    public function propertyTypeLtvAdjustments()
    {
        return $this->hasMany(PropertyTypeLtvAdjustment::class);
    }
}
