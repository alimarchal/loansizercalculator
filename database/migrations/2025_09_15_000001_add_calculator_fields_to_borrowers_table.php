<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('borrowers', function (Blueprint $table) {
            // User relationship
            $table->foreignId('user_id')->nullable()->after('id')->constrained()->onDelete('cascade');

            // Calculator Input Fields
            $table->string('transaction_type')->nullable()->after('property_type');
            $table->integer('loan_term')->nullable()->after('transaction_type');
            $table->decimal('purchase_price', 15, 2)->nullable()->after('loan_term');
            $table->decimal('arv', 15, 2)->nullable()->after('purchase_price');
            $table->decimal('rehab_budget', 15, 2)->nullable()->after('arv');
            $table->decimal('broker_points', 5, 2)->nullable()->after('rehab_budget');
            $table->decimal('payoff_amount', 15, 2)->nullable()->after('broker_points');
            $table->decimal('lender_points', 5, 2)->nullable()->after('payoff_amount');
            $table->string('pre_pay_penalty')->nullable()->after('lender_points');
            $table->string('occupancy_type')->nullable()->after('pre_pay_penalty');
            $table->decimal('monthly_market_rent', 10, 2)->nullable()->after('occupancy_type');
            $table->decimal('annual_tax', 10, 2)->nullable()->after('monthly_market_rent');
            $table->decimal('annual_insurance', 10, 2)->nullable()->after('annual_tax');
            $table->decimal('annual_hoa', 10, 2)->nullable()->after('annual_insurance');
            $table->decimal('dscr', 5, 2)->nullable()->after('annual_hoa');
            $table->date('purchase_date')->nullable()->after('dscr');
            $table->decimal('title_charges', 10, 2)->nullable()->after('purchase_date');
            $table->decimal('property_insurance', 10, 2)->nullable()->after('title_charges');

            // Selected Loan Program
            $table->string('selected_loan_type')->nullable()->after('property_insurance');
            $table->string('selected_loan_program')->nullable()->after('selected_loan_type');

            // Calculated Loan Amounts
            $table->decimal('purchase_loan_amount', 15, 2)->nullable()->after('selected_loan_program');
            $table->decimal('rehab_loan_amount', 15, 2)->nullable()->after('purchase_loan_amount');
            $table->decimal('total_loan_amount', 15, 2)->nullable()->after('rehab_loan_amount');

            // Property Costs
            $table->decimal('property_costs', 15, 2)->nullable()->after('total_loan_amount');

            // Lender Fees
            $table->decimal('lender_origination_fee', 10, 2)->nullable()->after('property_costs');
            $table->decimal('broker_fee', 10, 2)->nullable()->after('lender_origination_fee');
            $table->decimal('underwriting_processing_fee', 10, 2)->nullable()->after('broker_fee');
            $table->decimal('interest_reserves', 10, 2)->nullable()->after('underwriting_processing_fee');
            $table->decimal('total_lender_fees', 10, 2)->nullable()->after('interest_reserves');

            // Other Costs
            $table->decimal('title_costs', 10, 2)->nullable()->after('total_lender_fees');
            $table->decimal('legal_doc_prep_fee', 10, 2)->nullable()->after('title_costs');
            $table->decimal('total_other_costs', 10, 2)->nullable()->after('legal_doc_prep_fee');

            // Total Summary
            $table->decimal('subtotal_closing_costs', 15, 2)->nullable()->after('total_other_costs');
            $table->decimal('cash_due_to_buyer', 15, 2)->nullable()->after('subtotal_closing_costs');

            // Application Status
            $table->string('application_status')->default('IN_PROCESS')->after('cash_due_to_buyer');

            // API Data Storage
            $table->text('api_url_called')->nullable()->after('application_status');
            $table->longText('api_response_json')->nullable()->after('api_url_called');

            // Application metadata
            $table->timestamp('application_submitted_at')->nullable()->after('api_response_json');
            $table->string('application_source')->default('loan_calculator')->after('application_submitted_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrowers', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropColumn([
                'user_id',
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
                'selected_loan_type',
                'selected_loan_program',
                'purchase_loan_amount',
                'rehab_loan_amount',
                'total_loan_amount',
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
                'application_status',
                'api_url_called',
                'api_response_json',
                'application_submitted_at',
                'application_source'
            ]);
        });
    }
};