<?php

namespace Database\Seeders;

use App\Models\PropertyType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PropertyTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $propertyTypes = [
            'Vacant Land',
            'Entitled Land (Permit Approved)',
            'Single Family',
            'Condo',
            'Townhome',
            '2-4 Unit',
            'Multi Family Apartment 5+ Units',
            'Mixed Use',
            'Warehouse Industrial',
            'Hospitality',
            'Office',
            'Portfolio Residential',
        ];

        foreach ($propertyTypes as $propertyType) {
            PropertyType::firstOrCreate(['name' => $propertyType]);
        }
    }
}
