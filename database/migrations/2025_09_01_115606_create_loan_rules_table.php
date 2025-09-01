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
        Schema::create('loan_rules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('experience_id')->constrained()->cascadeOnUpdate();
            $table->foreignId('fico_band_id')->constrained()->cascadeOnUpdate();
            $table->foreignId('transaction_type_id')->constrained()->cascadeOnUpdate();
            // Loan Limits
            // If Total Loan < $50,000: "Your Loan Size is below the minimum $50,000 required"
            // If Total Loan > $1,000,000: "Your Loan Size above $1,000,000 require Underwriting review"
            $table->decimal('max_total_loan', 12, 2)->nullable();
            // Maximum Budget Validation
            // Any budget above max: "The Maximum Budget allowed is $XXX,XXX"
            // Maximum budget amount allowed. Any budget size above should give prompt message. “The Maximum Budget allowed is $100,000”
            $table->decimal('max_budget', 12, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_rules');
    }
};
