<?php

namespace Database\Seeders;

use App\Models\PricingTier;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PricingTierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tiers = [
            [
                'price_range' => '<250k',
                'min_amount' => 0.00,        // open low
                'max_amount' => 249999.99,
            ],
            [
                'price_range' => '250-500k',
                'min_amount' => 250000.00,
                'max_amount' => 500000.00,
            ],
            [
                'price_range' => '>=500k',
                'min_amount' => 500001.00,
                'max_amount' => 1000000.00,        // open high
            ],
        ];

        PricingTier::upsert($tiers, ['price_range']);
    }
}
