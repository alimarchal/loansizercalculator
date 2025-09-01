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
        $newConstruction = \App\Models\LoanType::where('name', 'New Construction')->first();
        $dscrRental = \App\Models\LoanType::where('name', 'DSCR Rental')->first();

        // Get all property types for Full Appraisal
        $allPropertyTypes = \App\Models\PropertyType::all();

        // Desktop Appraisal property types (typically more limited)
        $desktopPropertyTypes = \App\Models\PropertyType::whereIn('name', [
            'Single Family',
            'Condo',
            'Townhome',
            '2-4 Unit'
        ])->get();

        // Full Appraisal Fix & Flip - all property types
        if ($fixFlipFull) {
            foreach ($allPropertyTypes as $propertyType) {
                $fixFlipFull->propertyTypes()->attach($propertyType->id);
            }
        }

        // Desktop Appraisal Fix & Flip - limited property types
        if ($fixFlipDesktop) {
            foreach ($desktopPropertyTypes as $propertyType) {
                $fixFlipDesktop->propertyTypes()->attach($propertyType->id);
            }
        }

        // New Construction and DSCR - all property types
        if ($newConstruction) {
            foreach ($allPropertyTypes as $propertyType) {
                $newConstruction->propertyTypes()->attach($propertyType->id);
            }
        }

        if ($dscrRental) {
            foreach ($allPropertyTypes as $propertyType) {
                $dscrRental->propertyTypes()->attach($propertyType->id);
            }
        }
    }
}
