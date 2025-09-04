<?php

namespace Database\Seeders;

use App\Models\Experience;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ExperienceSeeder extends Seeder
{
    public function run(): void
    {
        // Get the loan type IDs for Fix and Flip programs
        $fullAppraisalId = \App\Models\LoanType::where('name', 'Fix and Flip')->where('loan_program', 'FULL APPRAISAL')->first()?->id;
        $desktopAppraisalId = \App\Models\LoanType::where('name', 'Fix and Flip')->where('loan_program', 'DESKTOP APPRAISAL')->first()?->id;

        // Get the loan type IDs for New Construction programs
        $experiencedBuilderId = \App\Models\LoanType::where('name', 'New Construction')->where('loan_program', 'EXPERIENCED BUILDER')->first()?->id;
        $newBuilderId = \App\Models\LoanType::where('name', 'New Construction')->where('loan_program', 'NEW BUILDER')->first()?->id;

        // Get the loan type ID for DSCR Rental program
        $dscrRentalId = \App\Models\LoanType::where('name', 'DSCR Rental')->first()?->id;

        $experienceRanges = [
            ['experiences_range' => '0', 'min_experience' => 0, 'max_experience' => 0],
            ['experiences_range' => '1-2', 'min_experience' => 1, 'max_experience' => 2],
            ['experiences_range' => '3-4', 'min_experience' => 3, 'max_experience' => 4],
            ['experiences_range' => '5-9', 'min_experience' => 5, 'max_experience' => 9],
            ['experiences_range' => '10+', 'min_experience' => 10, 'max_experience' => 50],
        ];

        $data = [];

        // Create experiences for Fix and Flip loan programs
        if ($fullAppraisalId) {
            foreach ($experienceRanges as $range) {
                $data[] = array_merge(['loan_type_id' => $fullAppraisalId], $range);
            }
        }

        if ($desktopAppraisalId) {
            foreach ($experienceRanges as $range) {
                $data[] = array_merge(['loan_type_id' => $desktopAppraisalId], $range);
            }
        }

        // Create experiences for New Construction - Experienced Builder program
        if ($experiencedBuilderId) {
            foreach ($experienceRanges as $range) {
                $data[] = array_merge(['loan_type_id' => $experiencedBuilderId], $range);
            }
        }

        // Create experiences for New Construction - New Builder program
        if ($newBuilderId) {
            foreach ($experienceRanges as $range) {
                $data[] = array_merge(['loan_type_id' => $newBuilderId], $range);
            }
        }

        // Create experiences for DSCR Rental program
        if ($dscrRentalId) {
            foreach ($experienceRanges as $range) {
                $data[] = array_merge(['loan_type_id' => $dscrRentalId], $range);
            }
        }

        if (!empty($data)) {
            Experience::upsert($data, ['loan_type_id', 'experiences_range'], ['min_experience', 'max_experience']);
        }
    }


}
