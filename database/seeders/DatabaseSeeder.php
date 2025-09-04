<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // Seed roles and users first
        $this->call([
            RoleSeeder::class,
        ]);

        // Seed lookup tables
        $this->call([
            LoanTypeSeeder::class,
            PropertyTypeSeeder::class,
            StateSeeder::class,
            ExperienceSeeder::class,
            FicoBandSeeder::class,
            TransactionTypeSeeder::class,
            LoanRuleSeeder::class,
            RehabLevelSeeder::class,
            RehabLimitSeeder::class,
            PricingTierSeeder::class,
            PricingSeeder::class,
            DesktopAppraisalLoanRuleSeeder::class, // Desktop Appraisal specific rules
            ExperiencedBuilderLoanRuleSeeder::class, // Experienced Builder specific rules
            NewBuilderLoanRuleSeeder::class, // New Builder specific rules
            DscrRentalLoanRuleSeeder::class, // DSCR Rental specific rules

                // Relationship seeders (run after the base data)
            LoanTypeStateSeeder::class,
            LoanTypePropertyTypeSeeder::class,

            //RateMatrixSeeder::class,
        ]);
    }
}
