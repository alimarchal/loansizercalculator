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
            'AZ',
            'AR',
            'CA',
            'CO',
            'CT',
            'DE',
            'DC',
            'FL',
            'GA',
            'ID',
            'IL',
            'IN',
            'IA',
            'KS',
            'KY',
            'LA',
            'ME',
            'MD',
            'MA',
            'MI',
            'MN',
            'MS',
            'MO',
            'MT',
            'NE',
            'NV',
            'NH',
            'NJ',
            'NM',
            'NY',
            'NC',
            'ND',
            'OH',
            'OK',
            'OR',
            'PA',
            'RI',
            'SC',
            'SD',
            'TN',
            'TX',
            'UT',
            'VT',
            'VA',
            'WA',
            'WV',
            'WI',
            'WY'
        ];

        $desktopAppraisalStates = [
            'AL',
            'AZ',
            'AR',
            'CO',
            'FL',
            'GA',
            'ID',
            'IL',
            'IN',
            'IA',
            'KS',
            'KY',
            'LA',
            'MI',
            'MN',
            'MS',
            'MO',
            'MT',
            'NE',
            'NV',
            'NM',
            'NC',
            'ND',
            'OH',
            'OK',
            'OR',
            'SC',
            'SD',
            'TN',
            'TX',
            'UT',
            'VA',
            'WV',
            'WI',
            'WY'
        ];

        // Get loan types
        $fixFlipFull = \App\Models\LoanType::where('name', 'Fix and Flip')->where('loan_program', 'FULL APPRAISAL')->first();
        $fixFlipDesktop = \App\Models\LoanType::where('name', 'Fix and Flip')->where('loan_program', 'DESKTOP APPRAISAL')->first();
        $newConstruction = \App\Models\LoanType::where('name', 'New Construction')->first();
        $dscrRental = \App\Models\LoanType::where('name', 'DSCR Rental')->first();

        // Attach states to Full Appraisal Fix & Flip
        if ($fixFlipFull) {
            foreach ($fullAppraisalStates as $stateCode) {
                $state = \App\Models\State::where('code', $stateCode)->first();
                if ($state) {
                    $fixFlipFull->states()->attach($state->id);
                }
            }
        }

        // Attach states to Desktop Appraisal Fix & Flip
        if ($fixFlipDesktop) {
            foreach ($desktopAppraisalStates as $stateCode) {
                $state = \App\Models\State::where('code', $stateCode)->first();
                if ($state) {
                    $fixFlipDesktop->states()->attach($state->id);
                }
            }
        }

        // New Construction and DSCR can use same as Full Appraisal states
        if ($newConstruction) {
            foreach ($fullAppraisalStates as $stateCode) {
                $state = \App\Models\State::where('code', $stateCode)->first();
                if ($state) {
                    $newConstruction->states()->attach($state->id);
                }
            }
        }

        if ($dscrRental) {
            foreach ($fullAppraisalStates as $stateCode) {
                $state = \App\Models\State::where('code', $stateCode)->first();
                if ($state) {
                    $dscrRental->states()->attach($state->id);
                }
            }
        }
    }
}
