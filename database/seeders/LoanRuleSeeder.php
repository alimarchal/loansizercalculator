<?php

namespace Database\Seeders;

use App\Models\FicoBand;
use App\Models\LoanRule;
use App\Models\Experience;
use App\Models\TransactionType;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class LoanRuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Ensure dependencies exist
        $purchase = TransactionType::where('name', 'Purchase')->firstOrFail();

        $rules = [
            // Experience => FICO => [max_total_loan, max_budget]
            ['experience' => '0', 'fico' => '660-679', 'max_total_loan' => 0, 'max_budget' => 0],
            ['experience' => '0', 'fico' => '680-699', 'max_total_loan' => 1000000, 'max_budget' => 100000],
            ['experience' => '0', 'fico' => '700-719', 'max_total_loan' => 1000000, 'max_budget' => 100000],
            ['experience' => '0', 'fico' => '720-739', 'max_total_loan' => 1000000, 'max_budget' => 100000],
            ['experience' => '0', 'fico' => '740+', 'max_total_loan' => 1000000, 'max_budget' => 100000],

            ['experience' => '1-2', 'fico' => '660-679', 'max_total_loan' => 0, 'max_budget' => 0],
            ['experience' => '1-2', 'fico' => '680-699', 'max_total_loan' => 1000000, 'max_budget' => 100000],
            ['experience' => '1-2', 'fico' => '700-719', 'max_total_loan' => 1000000, 'max_budget' => 100000],
            ['experience' => '1-2', 'fico' => '720-739', 'max_total_loan' => 1000000, 'max_budget' => 100000],
            ['experience' => '1-2', 'fico' => '740+', 'max_total_loan' => 1000000, 'max_budget' => 100000],

            ['experience' => '3-4', 'fico' => '660-679', 'max_total_loan' => 1000000, 'max_budget' => 500000],
            ['experience' => '3-4', 'fico' => '680-699', 'max_total_loan' => 1000000, 'max_budget' => 500000],
            ['experience' => '3-4', 'fico' => '700-719', 'max_total_loan' => 1000000, 'max_budget' => 500000],
            ['experience' => '3-4', 'fico' => '720-739', 'max_total_loan' => 1000000, 'max_budget' => 500000],
            ['experience' => '3-4', 'fico' => '740+', 'max_total_loan' => 1000000, 'max_budget' => 500000],

            ['experience' => '5-9', 'fico' => '660-679', 'max_total_loan' => 1000000, 'max_budget' => 500000],
            ['experience' => '5-9', 'fico' => '680-699', 'max_total_loan' => 1000000, 'max_budget' => 500000],
            ['experience' => '5-9', 'fico' => '700-719', 'max_total_loan' => 1000000, 'max_budget' => 500000],
            ['experience' => '5-9', 'fico' => '720-739', 'max_total_loan' => 1000000, 'max_budget' => 500000],
            ['experience' => '5-9', 'fico' => '740+', 'max_total_loan' => 1000000, 'max_budget' => 500000],

            ['experience' => '10+', 'fico' => '660-679', 'max_total_loan' => 1000000, 'max_budget' => 500000],
            ['experience' => '10+', 'fico' => '680-699', 'max_total_loan' => 1000000, 'max_budget' => 500000],
            ['experience' => '10+', 'fico' => '700-719', 'max_total_loan' => 1000000, 'max_budget' => 500000],
            ['experience' => '10+', 'fico' => '720-739', 'max_total_loan' => 1000000, 'max_budget' => 500000],
            ['experience' => '10+', 'fico' => '740+', 'max_total_loan' => 1000000, 'max_budget' => 500000],
        ];

        foreach ($rules as $row) {
            $exp = Experience::where('experiences_range', $row['experience'])->firstOrFail();
            $fico = FicoBand::where('fico_range', $row['fico'])->firstOrFail();

            LoanRule::updateOrCreate(
                [
                    'experience_id' => $exp->id,
                    'fico_band_id' => $fico->id,
                    'transaction_type_id' => $purchase->id,
                ],
                [
                    'max_total_loan' => $row['max_total_loan'],
                    'max_budget' => $row['max_budget'],
                ]
            );
        }
    }
}
