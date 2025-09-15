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
        Schema::table('users', function (Blueprint $table) {
            // Add fields for auto-generated accounts from loan calculator
            $table->boolean('is_auto_generated')->default(false)->after('is_active');
            $table->timestamp('password_sent_at')->nullable()->after('is_auto_generated');
            $table->boolean('initial_password_reset')->default(false)->after('password_sent_at');
            $table->string('account_source')->default('manual')->after('initial_password_reset'); // manual, loan_calculator, etc.
            $table->text('temp_password')->nullable()->after('account_source'); // Store temporarily for email
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'is_auto_generated',
                'password_sent_at',
                'initial_password_reset',
                'account_source',
                'temp_password'
            ]);
        });
    }
};