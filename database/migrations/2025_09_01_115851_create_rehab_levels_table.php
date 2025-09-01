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
        Schema::create('rehab_levels', function (Blueprint $table) {
            $table->id();
            // LIGHT REHAB, MODERATE REHAB, HEAVY  REHAB, EXTENSIVE  REHAB
            $table->enum('name', ['LIGHT REHAB', 'MODERATE REHAB', 'HEAVY REHAB', 'EXTENSIVE REHAB']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rehab_levels');
    }
};
