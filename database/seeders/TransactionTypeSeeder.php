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
        ];

        TransactionType::upsert($types, ['name']);
    }
}
