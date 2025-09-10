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
        Schema::create('transaction_type_ltv_adjustments', function (Blueprint $table) {
            $table->id();
            $table->foreignId(column: 'transaction_type_id')->nullable()->constrained()->cascadeOnUpdate();
            $table->foreignId(column: 'ltv_ratio_id')->nullable()->constrained()->cascadeOnUpdate();
            $table->decimal(column: 'adjustment_pct', total: 6, places: 4)->nullable();
            $table->timestamps();
            $table->unique(['transaction_type_id', 'ltv_ratio_id'], 'trans_type_ltv_adj_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transaction_type_ltv_adjustments');
    }
};
