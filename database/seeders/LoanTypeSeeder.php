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
            ['name' => 'Fix and Flip', 'loan_program' => 'FULL APPRAISAL', 'underwritting_fee' => 1495, 'legal_doc_prep_fee' => 995],
            ['name' => 'Fix and Flip', 'loan_program' => 'DESKTOP APPRAISAL', 'underwritting_fee' => 1495, 'legal_doc_prep_fee' => 0],
            ['name' => 'New Construction', 'loan_program' => 'EXPERIENCED BUILDER', 'underwritting_fee' => 1495, 'legal_doc_prep_fee' => 0],
            ['name' => 'New Construction', 'loan_program' => 'NEW BUILDER', 'underwritting_fee' => 1495, 'legal_doc_prep_fee' => 0],
            ['name' => 'DSCR Rental Loans', 'loan_program' => 'Loan Program #1', 'underwritting_fee' => 1595, 'legal_doc_prep_fee' => 995],
            ['name' => 'DSCR Rental Loans', 'loan_program' => 'Loan Program #2', 'underwritting_fee' => 1999, 'legal_doc_prep_fee' => 0],
            ['name' => 'DSCR Rental Loans', 'loan_program' => 'Loan Program #3', 'underwritting_fee' => 1595, 'legal_doc_prep_fee' => 0],

        ];

        foreach ($loanTypes as $loanType) {
            LoanType::firstOrCreate([
                'name' => $loanType['name'],
                'loan_program' => $loanType['loan_program'],
                'underwritting_fee' => $loanType['underwritting_fee'],
                'legal_doc_prep_fee' => $loanType['legal_doc_prep_fee']
            ]);
        }
    }
}