<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RehabLimitsSeeder extends Seeder
{
    public function run()
    {
        // First, let's clear existing data
        DB::table('rehab_limits')->truncate();

        // Get rehab level IDs
        $lightRehab = DB::table('rehab_levels')->where('name', 'LIGHT REHAB')->first();
        $moderateRehab = DB::table('rehab_levels')->where('name', 'MODERATE REHAB')->first();
        $heavyRehab = DB::table('rehab_levels')->where('name', 'HEAVY REHAB')->first();
        $extensiveRehab = DB::table('rehab_levels')->where('name', 'EXTENSIVE REHAB')->first();

        if (!$lightRehab || !$moderateRehab || !$heavyRehab || !$extensiveRehab) {
            echo "Rehab levels not found! Creating them...\n";

            // Create rehab levels if they don't exist
            $lightRehab = (object) ['id' => DB::table('rehab_levels')->insertGetId(['name' => 'LIGHT REHAB'])];
            $moderateRehab = (object) ['id' => DB::table('rehab_levels')->insertGetId(['name' => 'MODERATE REHAB'])];
            $heavyRehab = (object) ['id' => DB::table('rehab_levels')->insertGetId(['name' => 'HEAVY REHAB'])];
            $extensiveRehab = (object) ['id' => DB::table('rehab_levels')->insertGetId(['name' => 'EXTENSIVE REHAB'])];
        }

        // Get all loan rule IDs
        $loanRules = DB::table('loan_rules')->pluck('id');

        echo "Setting up rehab limits for " . $loanRules->count() . " loan rules...\n";

        foreach ($loanRules as $loanRuleId) {
            // Standard Fix and Flip values (most common)
            $lightLTC = 85;    // Light rehab: 85% LTC
            $lightLTV = 75;    // Light rehab: 75% LTV

            $moderateLTC = 85; // Moderate rehab: 85% LTC  
            $moderateLTV = 75; // Moderate rehab: 75% LTV

            $heavyLTC = 85;    // Heavy rehab: 85% LTC
            $heavyLTV = 75;    // Heavy rehab: 75% LTV

            $extensiveLTC = 85;   // Extensive rehab: 85% LTC
            $extensiveLTV = 75;   // Extensive rehab: 75% LTV
            $extensiveLTFC = 90;  // Extensive rehab: 90% LTFC (when rehab > purchase)

            // Insert rehab limits for each level
            DB::table('rehab_limits')->insert([
                [
                    'loan_rule_id' => $loanRuleId,
                    'rehab_level_id' => $lightRehab->id,
                    'max_ltc' => $lightLTC,
                    'max_ltv' => $lightLTV,
                    'max_ltfc' => 0, // Not applicable for light rehab
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'loan_rule_id' => $loanRuleId,
                    'rehab_level_id' => $moderateRehab->id,
                    'max_ltc' => $moderateLTC,
                    'max_ltv' => $moderateLTV,
                    'max_ltfc' => 0, // Not applicable for moderate rehab
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'loan_rule_id' => $loanRuleId,
                    'rehab_level_id' => $heavyRehab->id,
                    'max_ltc' => $heavyLTC,
                    'max_ltv' => $heavyLTV,
                    'max_ltfc' => 0, // Not applicable for heavy rehab
                    'created_at' => now(),
                    'updated_at' => now()
                ],
                [
                    'loan_rule_id' => $loanRuleId,
                    'rehab_level_id' => $extensiveRehab->id,
                    'max_ltc' => $extensiveLTC,
                    'max_ltv' => $extensiveLTV,
                    'max_ltfc' => $extensiveLTFC, // LTFC applies for extensive rehab
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            ]);
        }

        echo "Rehab limits seeded successfully!\n";
    }
}