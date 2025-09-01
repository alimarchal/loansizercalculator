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
        Schema::create('borrower_carts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('credit_score')->nullable();
            $table->unsignedTinyInteger('fix_and_flip_rental_experience')->comment('Years of experience')->nullable();
            $table->unsignedTinyInteger('new_construction_experience')->comment('Years of experience')->nullable();
            $table->string('borrower_name')->nullable();
            $table->string('borrower_email')->nullable();
            $table->string('borrower_phone')->nullable();
            $table->string('broker_name')->nullable();
            $table->string('broker_email')->nullable();
            $table->string('broker_phone')->nullable();
            $table->decimal('broker_points', 5, 2)->nullable();
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
        Schema::dropIfExists('borrower_carts');
    }
};
