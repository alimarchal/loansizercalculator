<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB; // ← ADD THIS LINE

return new class extends Migration {
    public function up(): void
    {
        Schema::create('id_prefixes', function (Blueprint $table) {
            $table->id();
            $table->string('name')->unique()->comment('Type name like invoice, complaint');
            $table->string('prefix', 10)->unique()->comment('Short prefix like INV, CMP');
            $table->timestamps();
        });

        // Insert sample data
        DB::table('id_prefixes')->insert([
            ['name' => 'real_estate_purchase', 'prefix' => 'RELP', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'real_estate_refinance', 'prefix' => 'RERF', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('id_prefixes');
    }
};