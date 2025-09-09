<?php

namespace Database\Seeders;

use App\Models\LoanTypesDscr;
use Illuminate\Database\Seeder;

class LoanTypesDscrSeeder extends Seeder
{
    public function run(): void
    {
        $loanTypes = [
            ['loan_type_dscr_name' => '30 Year Fixed', 'display_order' => 1],
            ['loan_type_dscr_name' => '10 Year IO', 'display_order' => 2],
            ['loan_type_dscr_name' => '5/1 ARM', 'display_order' => 3],
        ];

        foreach ($loanTypes as $type) {
            LoanTypesDscr::firstOrCreate(
                ['loan_type_dscr_name' => $type['loan_type_dscr_name']],
                $type
            );
        }
    }
}
