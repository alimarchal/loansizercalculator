<?php

namespace Database\Seeders;

use App\Models\TransactionType;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TransactionTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            ['name' => 'Purchase'],
            ['name' => 'Refinance No Cash Out'],
            ['name' => 'Refinance Cash Out'],

            ['name' => 'Cash Out Refi 660-679 FICO'],
            ['name' => 'Cash Out Refi 680-699 FICO'],
            ['name' => 'Cash Out Refi 700-720 FICO'],
            ['name' => 'Cash Out Refi 720+ FICO'],

        ];

        TransactionType::upsert($types, ['name']);
    }
}
