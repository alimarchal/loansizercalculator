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

        // Full Appraisal property types: Single Family, Condo, 2-4 Unit, Townhome
        $fullAppraisalPropertyTypes = \App\Models\PropertyType::whereIn('name', [
            'Single Family',
            'Condo',
            '2-4 Unit',
            'Townhome'
        ])->get();

        // Desktop Appraisal property types: Single Family, condo, Townhome, 2-4 Unit
        $desktopAppraisalPropertyTypes = \App\Models\PropertyType::whereIn('name', [
            'Single Family',
            'Condo',
            'Townhome',
            '2-4 Unit'
        ])->get();

        // Experienced Builder property types: Single Family, Condo, 2-4 Unit, Townhome
        $experiencedBuilderPropertyTypes = \App\Models\PropertyType::whereIn('name', [
            'Single Family',
            'Condo',
            '2-4 Unit',
            'Townhome'
        ])->get();

        // New Builder property types: Single Family, Condo, 2-4 Unit, Townhome
        $newBuilderPropertyTypes = \App\Models\PropertyType::whereIn('name', [
            'Single Family',
            'Condo',
            '2-4 Unit',
            'Townhome'
        ])->get();

        // DSCR property types: all property types that exist in database
        $dscrPropertyTypes = \App\Models\PropertyType::all();

        // Full Appraisal Fix & Flip - Single Family, Condo, 2-4 Unit, Townhome
        if ($fixFlipFull) {
            $propertyTypeIds = $fullAppraisalPropertyTypes->pluck('id')->toArray();
            $fixFlipFull->propertyTypes()->sync($propertyTypeIds);
        }

        // Desktop Appraisal Fix & Flip - Single Family, condo, Townhome, 2-4 Unit
        if ($fixFlipDesktop) {
            $propertyTypeIds = $desktopAppraisalPropertyTypes->pluck('id')->toArray();
            $fixFlipDesktop->propertyTypes()->sync($propertyTypeIds);
        }

        // EXPERIENCED BUILDER New Construction - Single Family, Condo, 2-4 Unit, Townhome
        if ($experiencedBuilder) {
            $propertyTypeIds = $experiencedBuilderPropertyTypes->pluck('id')->toArray();
            $experiencedBuilder->propertyTypes()->sync($propertyTypeIds);
        }

        // NEW BUILDER New Construction - Single Family, Condo, 2-4 Unit, Townhome
        if ($newBuilder) {
            $propertyTypeIds = $newBuilderPropertyTypes->pluck('id')->toArray();
            $newBuilder->propertyTypes()->sync($propertyTypeIds);
        }

        // DSCR Rental - all property types that exist in database
        if ($dscrRental) {
            $propertyTypeIds = $dscrPropertyTypes->pluck('id')->toArray();
            $dscrRental->propertyTypes()->sync($propertyTypeIds);
        }
    }
}
