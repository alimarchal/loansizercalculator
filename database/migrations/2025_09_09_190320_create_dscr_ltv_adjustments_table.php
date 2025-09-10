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
        Schema::create('dscr_ltv_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId(column: 'loan_type_id')->nullable()->constrained()->cascadeOnUpdate();
            $table->foreignId(column: 'dscr_range_id')->nullable()->constrained()->cascadeOnUpdate();
            $table->foreignId(column: 'ltv_ratio_id')->nullable()->constrained()->cascadeOnUpdate();
            $table->decimal(column: 'adjustment_pct', total: 6, places: 4)->nullable();
            $table->timestamps();
            $table->unique(['dscr_range_id', 'ltv_ratio_id'], 'dscr_ltv_adj_unique');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dscr_ltv_adjustments');
    }
};
