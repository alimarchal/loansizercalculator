<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     * 
     * Rate Matrix for Fix and Flip Loan Qualification System
     * - Purchase transactions allowed, refinance NOT allowed for Fix and Flip
     * - Minimum FICO score: 660 (below 660 = rejection)
     * - Minimum loan amount: $50,000 (below = rejection)
     * - Maximum loan amount: $1,000,000 (above = underwriting review required)
     * - Approved states only (see approved_states table)
     * - Property types: Single Family, Condo, 2-4 Unit, Townhome
     */
    public function up(): void
    {
        Schema::create('rate_matrices', function (Blueprint $table) {
            $table->id();

            // Experience Tier Mapping:
            // Tier 1 = 0 Experience
            // Tier 2 = 1-2 Experience combined  
            // Tier 3 = 3-4 Experience combined 
            // Tier 4 = 5-9 Experience combined 
            // Tier 5 = 10+ Experience combined
            $table->integer('experience_tier'); // 1-5

            // FICO Score Ranges - Minimum 660 required
            // Below 660: "Your FICO score is below the minimum of 660 required"
            $table->integer('fico_min');
            $table->integer('fico_max');
            $table->string('fico_range', 20); // '660-679', '680-699', '700-719', '720-739', '740+'

            // Loan Limits
            // If Total Loan < $50,000: "Your Loan Size is below the minimum $50,000 required"
            // If Total Loan > $1,000,000: "Your Loan Size above $1,000,000 require Underwriting review"
            $table->decimal('max_total_loan', 12, 2)->nullable();

            // Maximum Budget Validation
            // Any budget above max: "The Maximum Budget allowed is $XXX,XXX"
            $table->decimal('max_budget', 12, 2)->nullable();

            // Light Rehab (≤25% of purchase price)
            // Max LTV/LTC allowed for Light Rehab transactions
            $table->decimal('max_ltv_light', 5, 2)->nullable();
            $table->decimal('max_ltc_light', 5, 2)->nullable();

            // Moderate Rehab (25%-50% of purchase price)
            // Max LTV/LTC allowed for Moderate Rehab transactions  
            $table->decimal('max_ltv_moderate', 5, 2)->nullable();
            $table->decimal('max_ltc_moderate', 5, 2)->nullable();

            // Heavy Rehab (50%-100% of purchase price)
            // Max LTV/LTC allowed for Heavy Rehab transactions
            $table->decimal('max_ltv_heavy', 5, 2)->nullable();
            $table->decimal('max_ltc_heavy', 5, 2)->nullable();

            // Extensive Rehab (100%+ of purchase price)
            // Max LTV/LTC allowed for Extensive Rehab transactions
            // LTC = "Loan to Final Cost" - max percentage loan will fund vs borrower cost (purchase + rehab)
            $table->decimal('max_ltv_extensive', 5, 2)->nullable();
            $table->decimal('max_ltc_extensive', 5, 2)->nullable();

            // Interest Rate Pricing - Selected based on FICO and Experience inputs
            $table->decimal('interest_rate_base', 6, 3);
            $table->decimal('lender_points_base', 5, 3);

            // Unique constraint to prevent duplicate rate rules
            $table->unique(['experience_tier', 'fico_min', 'fico_max']);

            $table->userTracking();
            $table->softDeletes();
            $table->timestamps();

            // Performance indexes for rate lookups
            $table->index(['fico_min', 'fico_max']);
            $table->index('experience_tier');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rate_matrices');
    }
};