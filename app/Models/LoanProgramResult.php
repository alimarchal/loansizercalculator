<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanProgramResult extends Model
{
    /** @use HasFactory<\Database\Factories\LoanProgramResultFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'borrower_id',
        'loan_type',
        'loan_program',
        'loan_term',
        'interest_rate',
        'lender_points',
        'max_ltv',
        'max_ltc',
        'max_ltfc',
        'purchase_loan_up_to',
        'rehab_loan_up_to',
        'total_loan_up_to',
        'rehab_category',
        'rehab_percentage',
        'pricing_tier',
        'is_selected',
        'raw_loan_data',
        'display_order',
        'program_status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'interest_rate' => 'decimal:3',
        'lender_points' => 'decimal:2',
        'max_ltv' => 'decimal:2',
        'max_ltc' => 'decimal:2',
        'max_ltfc' => 'decimal:2',
        'purchase_loan_up_to' => 'decimal:2',
        'rehab_loan_up_to' => 'decimal:2',
        'total_loan_up_to' => 'decimal:2',
        'rehab_percentage' => 'decimal:2',
        'is_selected' => 'boolean',
        'raw_loan_data' => 'json',
        'display_order' => 'integer',
    ];

    /**
     * Get the borrower that owns the loan program result.
     */
    public function borrower()
    {
        return $this->belongsTo(Borrower::class);
    }

    /**
     * Scope to filter by selected programs
     */
    public function scopeSelected($query)
    {
        return $query->where('is_selected', true);
    }

    /**
     * Scope to filter by loan type
     */
    public function scopeByLoanType($query, $loanType)
    {
        return $query->where('loan_type', $loanType);
    }

    /**
     * Scope to filter by loan program
     */
    public function scopeByLoanProgram($query, $loanProgram)
    {
        return $query->where('loan_program', $loanProgram);
    }

    /**
     * Scope to order by display order
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('created_at');
    }

    /**
     * Get formatted interest rate
     */
    public function getFormattedInterestRateAttribute()
    {
        return $this->interest_rate ? number_format($this->interest_rate, 2) . '%' : 'N/A';
    }

    /**
     * Get formatted lender points
     */
    public function getFormattedLenderPointsAttribute()
    {
        return $this->lender_points ? number_format($this->lender_points, 2) . '%' : 'N/A';
    }

    /**
     * Get formatted loan amounts
     */
    public function getFormattedPurchaseLoanAttribute()
    {
        return $this->purchase_loan_up_to ? '$' . number_format($this->purchase_loan_up_to, 2) : '$0.00';
    }

    public function getFormattedRehabLoanAttribute()
    {
        return $this->rehab_loan_up_to ? '$' . number_format($this->rehab_loan_up_to, 2) : '$0.00';
    }

    public function getFormattedTotalLoanAttribute()
    {
        return $this->total_loan_up_to ? '$' . number_format($this->total_loan_up_to, 2) : '$0.00';
    }

    /**
     * Check if this is the selected program for the borrower
     */
    public function isSelectedProgram()
    {
        return $this->is_selected === true;
    }

    /**
     * Mark this program as selected and unselect others for the same borrower
     */
    public function markAsSelected()
    {
        // First, unselect all programs for this borrower
        static::where('borrower_id', $this->borrower_id)->update(['is_selected' => false]);

        // Then select this one
        $this->update(['is_selected' => true]);

        return $this;
    }
}