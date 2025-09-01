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
        // Create loan_type_states pivot table
        Schema::create('loan_type_states', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('state_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['loan_type_id', 'state_id']);
        });

        // Create loan_type_property_types pivot table
        Schema::create('loan_type_property_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_type_id')->constrained()->onDelete('cascade');
            $table->foreignId('property_type_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['loan_type_id', 'property_type_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loan_type_property_types');
        Schema::dropIfExists('loan_type_states');
    }
};
