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

        // DSCR Rental states
        $dscrRentalStates = [
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

        if ($dscrRental) {
            $stateIds = [];
            foreach ($dscrRentalStates as $stateCode) {
                $state = \App\Models\State::where('code', $stateCode)->first();
                if ($state) {
                    $stateIds[] = $state->id;
                }
            }
            $dscrRental->states()->sync($stateIds);
        }
    }
}
