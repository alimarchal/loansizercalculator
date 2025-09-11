<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LoanTypeStateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fullAppraisalStates = [
            'AL',
            'AK',
            'AZ',
            'CA',
            'CO',
            'CT',
            'DE',
            'DC',
            'FL',
            'GA',
            'IL',
            'IN',
            'KY',
            'LA',
            'ME',
            'NH',
            'NJ',
            'NM',
            'NY',
            'NC',
            'OH',
            'OK',
            'MD',
            'MA',
            'MI',
            'MS',
            'MO',
            'PA',
            'RI',
            'SC',
            'TN',
            'TX',
            'UT',
            'VA',
            'WA',
            'WV',
            'WI',
            'IA',
            'ID',
            'KS',
            'MN',
            'MT',
            'NE',
            'OR',
            'WY'
        ];

        $desktopAppraisalStates = [
            'AL',
            'AR',
            'CO',
            'CT',
            'DC',
            'FL',
            'GA',
            'IL',
            'IN',
            'KS',
            'KY',
            'MA',
            'MD',
            'MI',
            'MO',
            'NC',
            'OH',
            'OK',
            'OR',
            'PA',
            'SC',
            'TN',
            'TX',
            'VA',
            'WA',
            'WI',
            'WV'
        ];

        $experiencedBuilderStates = [
            'AL',
            'AK',
            'AZ',
            'CA',
            'CO',
            'CT',
            'DE',
            'DC',
            'FL',
            'GA',
            'IL',
            'IN',
            'KY',
            'LA',
            'ME',
            'NH',
            'NJ',
            'NM',
            'NY',
            'NC',
            'OH',
            'OK',
            'MD',
            'MA',
            'MI',
            'MS',
            'MO',
            'PA',
            'RI',
            'SC',
            'TN',
            'TX',
            'UT',
            'VA',
            'WA',
            'WV',
            'WI',
            'IA',
            'ID',
            'KS',
            'MN',
            'MT',
            'NE',
            'OR',
            'WY'
        ];

        // NEW BUILDER uses the same states as EXPERIENCED BUILDER
        $newBuilderStates = [
            'AL',
            'AK',
            'AZ',
            'CA',
            'CO',
            'CT',
            'DE',
            'DC',
            'FL',
            'GA',
            'IL',
            'IN',
            'KY',
            'LA',
            'ME',
            'NH',
            'NJ',
            'NM',
            'NY',
            'NC',
            'OH',
            'OK',
            'MD',
            'MA',
            'MI',
            'MS',
            'MO',
            'PA',
            'RI',
            'SC',
            'TN',
            'TX',
            'UT',
            'VA',
            'WA',
            'WV',
            'WI',
            'IA',
            'ID',
            'KS',
            'MN',
            'MT',
            'NE',
            'OR',
            'WY'
        ];

        // DSCR Rental Loans states - same for all programs
        $dscrRentalLoansStates = [
            'AL',
            'AK',
            'AZ',
            'CA',
            'CO',
            'CT',
            'DE',
            'DC',
            'FL',
            'GA',
            'IL',
            'IN',
            'KY',
            'LA',
            'ME',
            'NH',
            'NJ',
            'NM',
            'NY',
            'NC',
            'OH',
            'OK',
            'MD',
            'MA',
            'MI',
            'MS',
            'MO',
            'PA',
            'RI',
            'SC',
            'TN',
            'TX',
            'UT',
            'VA',
            'WA',
            'WV',
            'WI',
            'IA',
            'ID',
            'KS',
            'MN',
            'MT',
            'NE',
            'OR',
            'WY'
        ];

        // Get loan types
        $fixFlipFull = \App\Models\LoanType::where('name', 'Fix and Flip')->where('loan_program', 'FULL APPRAISAL')->first();
        $fixFlipDesktop = \App\Models\LoanType::where('name', 'Fix and Flip')->where('loan_program', 'DESKTOP APPRAISAL')->first();
        $experiencedBuilder = \App\Models\LoanType::where('name', 'New Construction')->where('loan_program', 'EXPERIENCED BUILDER')->first();
        $newBuilder = \App\Models\LoanType::where('name', 'New Construction')->where('loan_program', 'NEW BUILDER')->first();
        $dscrRental = \App\Models\LoanType::where('name', 'DSCR Rental')->first();

        // Get DSCR Rental Loans programs
        $dscrRentalLoans1 = \App\Models\LoanType::where('name', 'DSCR Rental Loans')->where('loan_program', 'Loan Program #1')->first();
        $dscrRentalLoans2 = \App\Models\LoanType::where('name', 'DSCR Rental Loans')->where('loan_program', 'Loan Program #2')->first();
        $dscrRentalLoans3 = \App\Models\LoanType::where('name', 'DSCR Rental Loans')->where('loan_program', 'Loan Program #3')->first();

        // Attach states to Full Appraisal Fix & Flip
        if ($fixFlipFull) {
            $stateIds = [];
            foreach ($fullAppraisalStates as $stateCode) {
                $state = \App\Models\State::where('code', $stateCode)->first();
                if ($state) {
                    $stateIds[] = $state->id;
                }
            }
            $fixFlipFull->states()->sync($stateIds);
        }

        // Attach states to Desktop Appraisal Fix & Flip
        if ($fixFlipDesktop) {
            $stateIds = [];
            foreach ($desktopAppraisalStates as $stateCode) {
                $state = \App\Models\State::where('code', $stateCode)->first();
                if ($state) {
                    $stateIds[] = $state->id;
                }
            }
            $fixFlipDesktop->states()->sync($stateIds);
        }

        // Attach states to EXPERIENCED BUILDER New Construction
        if ($experiencedBuilder) {
            $stateIds = [];
            foreach ($experiencedBuilderStates as $stateCode) {
                $state = \App\Models\State::where('code', $stateCode)->first();
                if ($state) {
                    $stateIds[] = $state->id;
                }
            }
            $experiencedBuilder->states()->sync($stateIds);
        }

        // NEW BUILDER can use same as EXPERIENCED BUILDER states
        if ($newBuilder) {
            $stateIds = [];
            foreach ($newBuilderStates as $stateCode) {
                $state = \App\Models\State::where('code', $stateCode)->first();
                if ($state) {
                    $stateIds[] = $state->id;
                }
            }
            $newBuilder->states()->sync($stateIds);
        }

        // Attach states to DSCR Rental (legacy)
        if ($dscrRental) {
            $stateIds = [];
            foreach ($dscrRentalLoansStates as $stateCode) {
                $state = \App\Models\State::where('code', $stateCode)->first();
                if ($state) {
                    $stateIds[] = $state->id;
                }
            }
            $dscrRental->states()->sync($stateIds);
        }

        // Attach states to DSCR Rental Loans Program #1
        if ($dscrRentalLoans1) {
            $stateIds = [];
            foreach ($dscrRentalLoansStates as $stateCode) {
                $state = \App\Models\State::where('code', $stateCode)->first();
                if ($state) {
                    $stateIds[] = $state->id;
                }
            }
            $dscrRentalLoans1->states()->sync($stateIds);
        }

        // Attach states to DSCR Rental Loans Program #2
        if ($dscrRentalLoans2) {
            $stateIds = [];
            foreach ($dscrRentalLoansStates as $stateCode) {
                $state = \App\Models\State::where('code', $stateCode)->first();
                if ($state) {
                    $stateIds[] = $state->id;
                }
            }
            $dscrRentalLoans2->states()->sync($stateIds);
        }

        // Attach states to DSCR Rental Loans Program #3
        if ($dscrRentalLoans3) {
            $stateIds = [];
            foreach ($dscrRentalLoansStates as $stateCode) {
                $state = \App\Models\State::where('code', $stateCode)->first();
                if ($state) {
                    $stateIds[] = $state->id;
                }
            }
            $dscrRentalLoans3->states()->sync($stateIds);
        }
    }
}