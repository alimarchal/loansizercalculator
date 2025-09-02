<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LoanTypePropertyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get loan types
        $fixFlipFull = \App\Models\LoanType::where('name', 'Fix and Flip')->where('loan_program', 'FULL APPRAISAL')->first();
        $fixFlipDesktop = \App\Models\LoanType::where('name', 'Fix and Flip')->where('loan_program', 'DESKTOP APPRAISAL')->first();
        $experiencedBuilder = \App\Models\LoanType::where('name', 'New Construction')->where('loan_program', 'EXPERIENCED BUILDER')->first();
        $newBuilder = \App\Models\LoanType::where('name', 'New Construction')->where('loan_program', 'NEW BUILDER')->first();
        $dscrRental = \App\Models\LoanType::where('name', 'DSCR Rental')->first();

        // Get all property types for Full Appraisal
        $allPropertyTypes = \App\Models\PropertyType::all();

        // Desktop Appraisal and EXPERIENCED BUILDER property types (limited set)
        $limitedPropertyTypes = \App\Models\PropertyType::whereIn('name', [
            'Single Family',
            'Condo',
            'Townhome',
            '2-4 Unit'
        ])->get();

        // Full Appraisal Fix & Flip - all property types
        if ($fixFlipFull) {
            $propertyTypeIds = $allPropertyTypes->pluck('id')->toArray();
            $fixFlipFull->propertyTypes()->syncWithoutDetaching($propertyTypeIds);
        }

        // Desktop Appraisal Fix & Flip - limited property types
        if ($fixFlipDesktop) {
            $propertyTypeIds = $limitedPropertyTypes->pluck('id')->toArray();
            $fixFlipDesktop->propertyTypes()->syncWithoutDetaching($propertyTypeIds);
        }

        // EXPERIENCED BUILDER New Construction - limited property types
        if ($experiencedBuilder) {
            $propertyTypeIds = $limitedPropertyTypes->pluck('id')->toArray();
            $experiencedBuilder->propertyTypes()->syncWithoutDetaching($propertyTypeIds);
        }

        // NEW BUILDER New Construction - all property types
        if ($newBuilder) {
            $propertyTypeIds = $allPropertyTypes->pluck('id')->toArray();
            $newBuilder->propertyTypes()->syncWithoutDetaching($propertyTypeIds);
        }

        if ($dscrRental) {
            $propertyTypeIds = $allPropertyTypes->pluck('id')->toArray();
            $dscrRental->propertyTypes()->syncWithoutDetaching($propertyTypeIds);
        }
    }
}
