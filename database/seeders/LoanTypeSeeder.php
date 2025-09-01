<?php

namespace Database\Seeders;

use App\Models\LoanType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LoanTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $loanTypes = [
            'Fix and Flip',
            'New Construction',
            'DSCR Rental',
        ];

        foreach ($loanTypes as $loanType) {
            LoanType::firstOrCreate(['name' => $loanType]);
        }
    }
}
