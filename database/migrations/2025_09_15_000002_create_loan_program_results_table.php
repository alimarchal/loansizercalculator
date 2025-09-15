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
        Schema::create('loan_program_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borrower_id')->constrained()->onDelete('cascade');

            // Loan Program Information
            $table->string('loan_type');
            $table->string('loan_program');
            $table->string('loan_term');

            // Rates and Points
            $table->decimal('interest_rate', 5, 3)->nullable();
            $table->decimal('lender_points', 5, 2)->nullable();

            // Loan Ratios
            $table->decimal('max_ltv', 5, 2)->nullable();
            $table->decimal('max_ltc', 5, 2)->nullable();
            $table->decimal('max_ltfc', 5, 2)->nullable();

            // Loan Amounts
            $table->decimal('purchase_loan_up_to', 15, 2)->nullable();
            $table->decimal('rehab_loan_up_to', 15, 2)->nullable();
            $table->decimal('total_loan_up_to', 15, 2)->nullable();

            // Rehab Information
            $table->string('rehab_category')->nullable();
            $table->decimal('rehab_percentage', 5, 2)->nullable();

            // Pricing Tier Information
            $table->string('pricing_tier')->nullable();

            // Selection Status
            $table->boolean('is_selected')->default(false);

            // Raw JSON data from API
            $table->longText('raw_loan_data')->nullable()->comment('Complete loan program data from API');

            // Additional metadata
            $table->integer('display_order')->default(0);
            $table->string('program_status')->default('available'); // available, selected, rejected

            $table->timestamps();

            // Indexes
            $table->index(['borrower_id', 'is_selected']);
            $table->index(['loan_type', 'loan_program']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_program_results');
    }
};