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
        Schema::create('loan_type_dscr_ltv_adjustments', function (Blueprint $table) {
            $table->id();

            // Row dimension FK (loan_types): cascade delete rows if a loan type is removed
            $table->foreignId('loan_type_id')
                ->nullable()->constrained()->cascadeOnUpdate();

            // Shared column FK (ltv_ratios): do NOT allow deleting an LTV column while in use
            $table->foreignId('ltv_ratio_id')
                ->constrained() // ->references('id')->on('ltv_ratios')
                ->cascadeOnUpdate()
                ->restrictOnDelete();

            // Store decimal percentage; 4 dp is fine (e.g. 0.1250)
            $table->decimal('adjustment_pct', 6, 4)->nullable();

            $table->timestamps();

            // One cell per intersection
            $table->unique(['loan_type_id', 'ltv_ratio_id'], 'loan_type_dscr_ltv_adj_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_type_dscr_ltv_adjustments');
    }
};
