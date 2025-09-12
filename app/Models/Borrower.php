<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Borrower extends Model
{
    /** @use HasFactory<\Database\Factories\BorrowerFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'email',
        'phone',
        'credit_score',
        'annual_income',
        'years_of_experience',
        'employment_status',
        'property_state',
        'property_type',
        'loan_amount_requested',
        'loan_purpose',
        'status',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'annual_income' => 'decimal:2',
        'loan_amount_requested' => 'decimal:2',
        'credit_score' => 'integer',
        'years_of_experience' => 'integer',
    ];

    /**
     * Get the full name attribute
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Scope to filter by active status
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope to filter by credit score range
     */
    public function scopeCreditScoreRange($query, $min, $max)
    {
        return $query->whereBetween('credit_score', [$min, $max]);
    }

    /**
     * Scope to filter by loan amount range
     */
    public function scopeLoanAmountRange($query, $min, $max)
    {
        return $query->whereBetween('loan_amount_requested', [$min, $max]);
    }
}
