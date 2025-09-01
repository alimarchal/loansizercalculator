<?php

namespace Database\Seeders;

use App\Models\State;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $allowedStates = [
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


        // Create allowed states
        foreach ($allowedStates as $stateCode) {
            State::firstOrCreate(
                ['code' => $stateCode],
                ['is_allowed' => true]
            );
        }

    }
}
