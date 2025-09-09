<?php

namespace Database\Seeders;

use App\Models\OccupancyTypes;
use Illuminate\Database\Seeder;

class OccupancyTypesSeeder extends Seeder
{
    public function run(): void
    {
        $occupancyTypes = [
            ['name' => 'Vacant', 'display_order' => 1],
            ['name' => 'Occupied', 'display_order' => 2],
        ];

        foreach ($occupancyTypes as $type) {
            OccupancyTypes::firstOrCreate(
                ['name' => $type['name']],
                $type
            );
        }
    }
}
