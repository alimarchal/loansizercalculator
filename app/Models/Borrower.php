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
        'user_id',
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
        'property_address',
        'property_zip_code',
        'street_number',
        'street_name',
        'city',
        'loan_amount_requested',
        'loan_purpose',
        'status',
        'notes',

        // Calculator Input Fields
        'transaction_type',
        'loan_term',
        'purchase_price',
        'arv',
        'rehab_budget',
        'broker_points',
        'payoff_amount',
        'lender_points',
        'pre_pay_penalty',
        'occupancy_type',
        'monthly_market_rent',
        'annual_tax',
        'annual_insurance',
        'annual_hoa',
        'dscr',
        'purchase_date',
        'title_charges',
        'property_insurance',

        // Selected Loan Program
        'selected_loan_type',
        'selected_loan_program',

        // Calculated Loan Amounts
        'purchase_loan_amount',
        'rehab_loan_amount',
        'total_loan_amount',

        // Costs
        'property_costs',
        'lender_origination_fee',
        'broker_fee',
        'underwriting_processing_fee',
        'interest_reserves',
        'total_lender_fees',
        'title_costs',
        'legal_doc_prep_fee',
        'total_other_costs',
        'subtotal_closing_costs',
        'cash_due_to_buyer',

        // Application Status
        'application_status',
        'api_url_called',
        'api_response_json',
        'application_submitted_at',
        'application_source',
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
        'loan_term' => 'string',
        'purchase_price' => 'decimal:2',
        'arv' => 'decimal:2',
        'rehab_budget' => 'decimal:2',
        'broker_points' => 'decimal:2',
        'payoff_amount' => 'decimal:2',
        'lender_points' => 'decimal:2',
        'monthly_market_rent' => 'decimal:2',
        'annual_tax' => 'decimal:2',
        'annual_insurance' => 'decimal:2',
        'annual_hoa' => 'decimal:2',
        'dscr' => 'decimal:2',
        'purchase_date' => 'date',
        'title_charges' => 'decimal:2',
        'property_insurance' => 'decimal:2',
        'purchase_loan_amount' => 'decimal:2',
        'rehab_loan_amount' => 'decimal:2',
        'total_loan_amount' => 'decimal:2',
        'property_costs' => 'decimal:2',
        'lender_origination_fee' => 'decimal:2',
        'broker_fee' => 'decimal:2',
        'underwriting_processing_fee' => 'decimal:2',
        'interest_reserves' => 'decimal:2',
        'total_lender_fees' => 'decimal:2',
        'title_costs' => 'decimal:2',
        'legal_doc_prep_fee' => 'decimal:2',
        'total_other_costs' => 'decimal:2',
        'subtotal_closing_costs' => 'decimal:2',
        'cash_due_to_buyer' => 'decimal:2',
        'application_submitted_at' => 'datetime',
        'api_response_json' => 'json',
    ];

    /**
     * Get the full name attribute
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Get the user that owns the borrower.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the loan program results for the borrower.
     */
    public function loanProgramResults()
    {
        return $this->hasMany(LoanProgramResult::class);
    }

    /**
     * Get the selected loan program result.
     */
    public function selectedLoanProgram()
    {
        return $this->hasOne(LoanProgramResult::class)->where('is_selected', true);
    }

    /**
     * Get all loan program results ordered by display order.
     */
    public function orderedLoanProgramResults()
    {
        return $this->hasMany(LoanProgramResult::class)->orderBy('display_order');
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
