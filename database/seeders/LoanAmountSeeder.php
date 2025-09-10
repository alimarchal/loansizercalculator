<?php

namespace Database\Seeders;

use App\Models\LoanAmount;
use Illuminate\Database\Seeder;

class LoanAmountSeeder extends Seeder
{
    public function run(): void
    {
        $loanAmounts = [
            ['amount_range' => '50,000 - 99,999', 'min_amount' => 50000, 'max_amount' => 99999, 'display_order' => 1],
            ['amount_range' => '100,000 - 249,999', 'min_amount' => 100000, 'max_amount' => 249999, 'display_order' => 2],
            ['amount_range' => '250,000 - 499,999', 'min_amount' => 250000, 'max_amount' => 499999, 'display_order' => 3],
            ['amount_range' => '500,000 - 999,999', 'min_amount' => 500000, 'max_amount' => 999999, 'display_order' => 4],
            ['amount_range' => '1,000,000 - 1,499,999', 'min_amount' => 1000000, 'max_amount' => 1499999, 'display_order' => 5],
            ['amount_range' => '1,500,000 - 3,000,000', 'min_amount' => 1500000, 'max_amount' => 3000000, 'display_order' => 6],
        ];

        foreach ($loanAmounts as $amount) {
            LoanAmount::firstOrCreate(
                ['amount_range' => $amount['amount_range']],
                $amount
            );
        }
    }
}
