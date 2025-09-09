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
        Schema::create('dscr_ranges', function (Blueprint $table) {
            $table->id();
            $table->string('dscr_range', 20)->unique();
            $table->decimal('min_dscr', 4, 2)->nullable();
            $table->decimal('max_dscr', 4, 2)->nullable();
            $table->integer('display_order')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dscr_ranges');
    }
};
