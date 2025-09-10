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

            'Single Family',
            'Condo',
            'Townhome',
            '2-4 Unit',
        ];

        foreach ($propertyTypes as $propertyType) {
            PropertyType::firstOrCreate(['name' => $propertyType]);
        }
    }
}
