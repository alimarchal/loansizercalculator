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
            // Fix and Flip - Purchase
            ['name' => 'Fix and Flip', 'loan_program' => 'FULL APPRAISAL'],
            ['name' => 'Fix and Flip', 'loan_program' => 'DESKTOP APPRAISAL'],

            // Fix and Flip - Refinance
            ['name' => 'Fix and Flip Refinance', 'loan_program' => 'FULL APPRAISAL'],
            ['name' => 'Fix and Flip Refinance', 'loan_program' => 'DESKTOP APPRAISAL'],

            // New Construction - Purchase
            ['name' => 'New Construction', 'loan_program' => 'EXPERIENCED BUILDER'],
            ['name' => 'New Construction', 'loan_program' => 'NEW BUILDER'],

            // New Construction - Refinance
            ['name' => 'New Construction Refinance', 'loan_program' => 'EXPERIENCED BUILDER'],
            ['name' => 'New Construction Refinance', 'loan_program' => 'NEW BUILDER'],

            // DSCR Rental - Purchase
            ['name' => 'DSCR Rental', 'loan_program' => 'Loan # 1'],

            // DSCR Rental - Refinance
            ['name' => 'DSCR Rental Refinance', 'loan_program' => 'Loan # 2'],
        ];

        foreach ($loanTypes as $loanType) {
            LoanType::firstOrCreate([
                'name' => $loanType['name'],
                'loan_program' => $loanType['loan_program']
            ]);
        }
    }
}
