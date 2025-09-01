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
}
