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
        Schema::create('loan_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('loan_program')->nullable();
            $table->decimal('underwritting_fee', 8, 2)->default(1495);
            $table->decimal('legal_doc_prep_fee', 8, 2)->default(0);
            $table->decimal('loan_starting_rate', 8, 3)->default(0);
            $table->userTracking();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_types');
    }
};
