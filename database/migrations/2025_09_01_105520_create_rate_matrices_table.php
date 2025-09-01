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

            /*
            Experience Tier are based on the user experience inputs. 
            Tier 1 = Borrower with 0 Experience
            Tier 2 = Borrower with  1-2  Experience combined  
            Tier 3 = Borrower with  3-4  Experience combined 
            Tier 4 = Borrower with  5-9  Experience combined 
            Tier 5 = Borrower with  10+  Experience combined 
            */
            $table->integer('experience_tier'); // 1-5

            // FICO Score Ranges - Minimum 660 required
            // If FICO < 660 “You FICO score is below the minimum of 660 required”
            $table->integer('fico_min');
            $table->integer('fico_max');
            $table->string('fico_range', 20); // '660-679', '680-699', '700-719', '720-739', '740+'

            //Purchase Transaction Allowed Refinance NOT Allowed (For Fix and Flip Loan Type)
            $table->enum('transaction_type', ['Purchase', 'Refinance'])->default('Purchase');

            // Loan Limits
            // If Total Loan < $50,000: "Your Loan Size is below the minimum $50,000 required"
            // If Total Loan > $1,000,000: "Your Loan Size above $1,000,000 require Underwriting review"
            $table->decimal('max_total_loan', 12, 2)->nullable();

            // Maximum Budget Validation
            // Any budget above max: "The Maximum Budget allowed is $XXX,XXX"
            // Maximum budget amount allowed. Any budget size above should give prompt message. “The Maximum Budget allowed is $100,000”
            $table->decimal('max_budget', 12, 2)->nullable();

            // Light Rehab (≤25% of purchase price)
            // Max LTV/LTC allowed for Light Rehab transactions

            // Max LTC allowed for Loan that is classified as “Light Rehab” Transaction. Light Rehab transaction is when Rehab Budget (input) is 25% or Less of the Purchase Price.
            $table->decimal('max_ltc_light_rehab', 5, 2)->nullable();
            // Max LTV allowed for Loan that is classified as “Light Rehab” Transaction. Light Rehab transaction is when Rehab Budget (input) is 25% or Less of the Purchase Price.
            $table->decimal('max_ltv_light_rehab', 5, 2)->nullable();


            // Moderate Rehab (25%-50% of purchase price)
            // Max LTV/LTC allowed for Moderate Rehab transactions  
            // Max LTC allowed for Loan that is classified as “Moderate Rehab” Transaction. Moderate Rehab transaction is when Rehab Budget (input) is 25%-50% of the Purchase Price.
            $table->decimal('max_ltc_moderate_rehab', 5, 2)->nullable();
            // Max LTV allowed for Loan that is classified as “Moderate Rehab” Transaction. Moderate Rehab transaction is when Rehab Budget (input) is 25%-50% of the Purchase Price.
            $table->decimal('max_ltv_moderate_rehab', 5, 2)->nullable();





            // Heavy Rehab (50%-100% of purchase price)
            // Max LTV/LTC allowed for Heavy Rehab transactions
            // Max LTC allowed for Loan that is classified as “Heavy Rehab” Transaction. Heavy Rehab transaction is when Rehab Budget (input) is 50%-100% of the Purchase Price. 
            $table->decimal('max_ltc_heavy_rehab', 5, 2)->nullable();
            //Max LTV allowed for Loan that is classified as “Heavy Rehab” Transaction. Heavy Rehab transaction is when Rehab Budget (input) is 50%-100% of the Purchase Price. 
            $table->decimal('max_ltv_heavy_rehab', 5, 2)->nullable();


            // Max LTC allowed for Loan that is classified as “Extensive Rehab” Transaction. Extensive Rehab transaction is when Rehab Budget is 100% or more of the Purchase Price. 
            $table->decimal('max_ltc_extensive_rehab', 5, 2)->nullable();
            // Max LTV allowed for Loan that is classified as “Extensive Rehab” Transaction. Extensive Rehab transaction is when Rehab Budget (input) is 100% or more of the Purchase Price. 
            $table->decimal('max_ltv_extensive_rehab', 5, 2)->nullable();
            //Max T-LTC allowed for Loan that is classified as “Heavy Rehab” Transaction. LTFC means “Loan to Final Cost” which is a percentage that represents the MAXIMUM percentage the loan will fund compared to borrowers cost (purchase +rehab). Extensive Rehab transaction is when Rehab Budget (input) is 100% or more of the Purchase Price. 
            $table->decimal('max_ltfc_extensive_rehab', 5, 2)->nullable();


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