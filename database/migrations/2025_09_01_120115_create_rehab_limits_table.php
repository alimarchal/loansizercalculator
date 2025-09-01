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
        Schema::create('rehab_limits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_rule_id')->constrained()->cascadeOnUpdate();
            $table->foreignId('rehab_level_id')->constrained()->cascadeOnUpdate();
            $table->decimal('max_ltc', 12, 2)->nullable();
            $table->decimal('max_ltv', 12, 2)->nullable();
            $table->decimal('max_ltfc', 12, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rehab_limits');
    }
};
