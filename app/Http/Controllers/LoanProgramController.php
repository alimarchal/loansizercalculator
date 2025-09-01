<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

/**
 * LoanProgramController handles displaying the loan matrix data
 * Shows the full loan matrix with rehab levels and pricing tiers
 */
class LoanProgramController extends Controller
{
    /**
     * Display the complete loan matrix
     * 
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        try {
            // Raw SQL query to get the complete loan matrix with pivot data
            $matrixData = DB::select("
                SELECT
                  e.experiences_range AS experience,
                  f.fico_range AS fico,
                  t.name AS transaction_type,
                  r.max_total_loan,
                  r.max_budget,

                  /* Rehab Limits (pivot) */
                  MAX(CASE WHEN rl.name = 'LIGHT REHAB' THEN l.max_ltc END) AS light_ltc,
                  MAX(CASE WHEN rl.name = 'LIGHT REHAB' THEN l.max_ltv END) AS light_ltv,

                  MAX(CASE WHEN rl.name = 'MODERATE REHAB' THEN l.max_ltc END) AS moderate_ltc,
                  MAX(CASE WHEN rl.name = 'MODERATE REHAB' THEN l.max_ltv END) AS moderate_ltv,

                  MAX(CASE WHEN rl.name = 'HEAVY REHAB' THEN l.max_ltc END) AS heavy_ltc,
                  MAX(CASE WHEN rl.name = 'HEAVY REHAB' THEN l.max_ltv END) AS heavy_ltv,

                  MAX(CASE WHEN rl.name = 'EXTENSIVE REHAB' THEN l.max_ltc END) AS extensive_ltc,
                  MAX(CASE WHEN rl.name = 'EXTENSIVE REHAB' THEN l.max_ltv END) AS extensive_ltv,
                  MAX(CASE WHEN rl.name = 'EXTENSIVE REHAB' THEN l.max_ltfc END) AS extensive_ltfc,

                  /* Pricing (pivot) */
                  MAX(CASE WHEN pt.price_range = '<250k' THEN p.interest_rate END) AS ir_lt_250k,
                  MAX(CASE WHEN pt.price_range = '<250k' THEN p.lender_points END) AS lp_lt_250k,

                  MAX(CASE WHEN pt.price_range = '250-500k' THEN p.interest_rate END) AS ir_250_500k,
                  MAX(CASE WHEN pt.price_range = '250-500k' THEN p.lender_points END) AS lp_250_500k,

                  MAX(CASE WHEN pt.price_range = '>=500k' THEN p.interest_rate END) AS ir_gte_500k,
                  MAX(CASE WHEN pt.price_range = '>=500k' THEN p.lender_points END) AS lp_gte_500k

                FROM loan_rules r
                JOIN experiences e ON e.id = r.experience_id
                JOIN fico_bands f ON f.id = r.fico_band_id
                JOIN transaction_types t ON t.id = r.transaction_type_id
                LEFT JOIN rehab_limits l ON l.loan_rule_id = r.id
                LEFT JOIN rehab_levels rl ON rl.id = l.rehab_level_id
                LEFT JOIN pricings p ON p.loan_rule_id = r.id
                LEFT JOIN pricing_tiers pt ON pt.id = p.pricing_tier_id

                GROUP BY r.id, e.experiences_range, f.fico_range, t.name, r.max_total_loan, r.max_budget
                ORDER BY
                  /* Order by experience in your preferred numeric sense, then fico */
                  CASE e.experiences_range
                    WHEN '0' THEN 0 WHEN '1-2' THEN 1 WHEN '3-4' THEN 2 WHEN '5-9' THEN 3 WHEN '10+' THEN 4 ELSE 99
                  END,
                  f.fico_min, f.fico_max
            ");

            return view('loan-programs.index', compact('matrixData'));

        } catch (\Exception $e) {
            // If there's an error, return to dashboard with error message
            return redirect()->route('dashboard')
                ->with('error', 'Failed to load loan matrix data. Please try again.');
        }
    }

    /**
     * Show form to create new loan program entry
     * 
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        // This would show a form to create new loan program rules
        // For now, return to index
        return redirect()->route('loan-programs.index')
            ->with('info', 'Create functionality will be implemented later.');
    }

    /**
     * Show form to edit existing loan program entry
     * 
     * @param int $id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        // This would show a form to edit existing loan program rules
        // For now, return to index
        return redirect()->route('loan-programs.index')
            ->with('info', 'Edit functionality will be implemented later.');
    }
}
