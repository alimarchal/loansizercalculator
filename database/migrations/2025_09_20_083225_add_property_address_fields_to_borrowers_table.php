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
            // Add property address fields after property_state
            $table->string('property_address')->nullable()->after('property_state');
            $table->string('property_zip_code')->nullable()->after('property_address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrowers', function (Blueprint $table) {
            $table->dropColumn([
                'property_address',
                'property_zip_code'
            ]);
        });
    }
};
