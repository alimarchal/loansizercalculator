<?php

namespace Database\Seeders;

use App\Models\DscrRanges;
use Illuminate\Database\Seeder;

class DscrRangesSeeder extends Seeder
{
    public function run(): void
    {
        $dscrRanges = [
            ['dscr_range' => '1.20+', 'min_dscr' => 1.20, 'max_dscr' => 9.99, 'display_order' => 1],
            ['dscr_range' => '1.10-1.20', 'min_dscr' => 1.10, 'max_dscr' => 1.20, 'display_order' => 2],
            ['dscr_range' => '1.00-1.10', 'min_dscr' => 1.00, 'max_dscr' => 1.10, 'display_order' => 3],
            ['dscr_range' => '0.80-0.99', 'min_dscr' => 0.80, 'max_dscr' => 0.99, 'display_order' => 4],
        ];

        foreach ($dscrRanges as $range) {
            DscrRanges::firstOrCreate(
                ['dscr_range' => $range['dscr_range']],
                $range
            );
        }
    }
}
