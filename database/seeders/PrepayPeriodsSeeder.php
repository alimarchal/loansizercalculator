<?php

namespace Database\Seeders;

use App\Models\PrepayPeriods;
use Illuminate\Database\Seeder;

class PrepayPeriodsSeeder extends Seeder
{
    public function run(): void
    {
        $prepayPeriods = [
            ['prepay_name' => '3 Year Prepay', 'display_order' => 1],
            ['prepay_name' => '5 Year Prepay', 'display_order' => 2],
        ];

        foreach ($prepayPeriods as $period) {
            PrepayPeriods::firstOrCreate(
                ['prepay_name' => $period['prepay_name']],
                $period
            );
        }
    }
}
