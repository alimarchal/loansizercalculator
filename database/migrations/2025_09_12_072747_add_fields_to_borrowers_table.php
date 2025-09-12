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
            $table->string('first_name')->after('id');
            $table->string('last_name')->after('first_name');
            $table->string('email')->unique()->after('last_name');
            $table->string('phone')->nullable()->after('email');
            $table->integer('credit_score')->nullable()->after('phone');
            $table->decimal('annual_income', 15, 2)->nullable()->after('credit_score');
            $table->integer('years_of_experience')->default(0)->after('annual_income');
            $table->string('employment_status')->nullable()->after('years_of_experience');
            $table->string('property_state', 2)->nullable()->after('employment_status');
            $table->string('property_type')->nullable()->after('property_state');
            $table->decimal('loan_amount_requested', 15, 2)->nullable()->after('property_type');
            $table->string('loan_purpose')->nullable()->after('loan_amount_requested');
            $table->string('status')->default('active')->after('loan_purpose'); // active, inactive, pending
            $table->text('notes')->nullable()->after('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrowers', function (Blueprint $table) {
            $table->dropColumn([
                'first_name',
                'last_name',
                'email',
                'phone',
                'credit_score',
                'annual_income',
                'years_of_experience',
                'employment_status',
                'property_state',
                'property_type',
                'loan_amount_requested',
                'loan_purpose',
                'status',
                'notes'
            ]);
        });
    }
};
