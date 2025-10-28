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
            // Add Google Maps address component fields after property_address
            $table->string('street_number')->nullable()->after('property_address');
            $table->string('street_name')->nullable()->after('street_number');
            $table->string('city')->nullable()->after('street_name');
            // Note: state and zip_code already exist as property_state and property_zip_code
            // We'll keep property_address as the full formatted address for display
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('borrowers', function (Blueprint $table) {
            $table->dropColumn([
                'street_number',
                'street_name',
                'city'
            ]);
        });
    }
};
