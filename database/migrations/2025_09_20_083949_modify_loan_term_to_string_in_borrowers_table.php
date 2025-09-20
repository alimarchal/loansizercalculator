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
            // Change loan_term from integer to string to support DSCR loan terms like "30 Year Fixed"
            $table->string('loan_term')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrowers', function (Blueprint $table) {
            // Revert loan_term back to integer
            $table->integer('loan_term')->nullable()->change();
        });
    }
};
