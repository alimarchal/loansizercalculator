<?php

namespace App\Http\Controllers;

use App\Models\LoanRule;
use App\Models\LoanType;
use App\Models\FicoBand;
use App\Models\TransactionType;
use App\Models\Experience;
use App\Models\RehabLevel;
use App\Models\PricingTier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

/**
 * LoanProgramController handles displaying the loan matrix data
 * Shows the full loan matrix with rehab levels and pricing tiers
 */
class LoanProgramController extends Controller
{
    /**
     * Display the DSCR LTV adjustment matrix
     * 
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function dscrMatrix(Request $request)
    {
        try {
            // Get filter parameters
            $loanProgram = $request->get('filter.loan_program', 'Loan Program #1');
            $loanTypeId = $request->get('filter.loan_type_id');
            $ficoBandId = $request->get('filter.fico_band_id');
            $transactionTypeId = $request->get('filter.transaction_type_id');

            // Build the raw SQL query based on your provided SQL
            $sql = "
                SELECT * FROM (
                  /* ---------- FICO × LTV (program-aware) ---------- */
                  SELECT
                    'FICO'                AS row_group,
                    lt.loan_program       AS program,
                    fb.fico_range         AS row_label,
                    MAX(CASE WHEN lr.ratio_range='50% LTV or less' THEN fla.adjustment_pct END) AS `50% LTV or less`,
                    MAX(CASE WHEN lr.ratio_range='55% LTV'         THEN fla.adjustment_pct END) AS `55% LTV`,
                    MAX(CASE WHEN lr.ratio_range='60% LTV'         THEN fla.adjustment_pct END) AS `60% LTV`,
                    MAX(CASE WHEN lr.ratio_range='65% LTV'         THEN fla.adjustment_pct END) AS `65% LTV`,
                    MAX(CASE WHEN lr.ratio_range='70% LTV'         THEN fla.adjustment_pct END) AS `70% LTV`,
                    MAX(CASE WHEN lr.ratio_range='75% LTV'         THEN fla.adjustment_pct END) AS `75% LTV`,
                    MAX(CASE WHEN lr.ratio_range='80% LTV'         THEN fla.adjustment_pct END) AS `80% LTV`
                  FROM fico_bands fb
                  JOIN fico_ltv_adjustments fla ON fla.fico_band_id = fb.id
                  JOIN ltv_ratios lr ON lr.id = fla.ltv_ratio_id
                  LEFT JOIN loan_types lt ON lt.id = fla.loan_type_id
                  WHERE lt.loan_program = ?
                  GROUP BY lt.loan_program, fb.fico_min, fb.fico_range

                  UNION ALL

                  /* ---------- Loan Amount × LTV (program-aware) ---------- */
                  SELECT
                    'Loan Amount'         AS row_group,
                    lt.loan_program       AS program,
                    la.amount_range       AS row_label,
                    MAX(CASE WHEN lr.ratio_range='50% LTV or less' THEN l.adjustment_pct END) AS `50% LTV or less`,
                    MAX(CASE WHEN lr.ratio_range='55% LTV'         THEN l.adjustment_pct END) AS `55% LTV`,
                    MAX(CASE WHEN lr.ratio_range='60% LTV'         THEN l.adjustment_pct END) AS `60% LTV`,
                    MAX(CASE WHEN lr.ratio_range='65% LTV'         THEN l.adjustment_pct END) AS `65% LTV`,
                    MAX(CASE WHEN lr.ratio_range='70% LTV'         THEN l.adjustment_pct END) AS `70% LTV`,
                    MAX(CASE WHEN lr.ratio_range='75% LTV'         THEN l.adjustment_pct END) AS `75% LTV`,
                    MAX(CASE WHEN lr.ratio_range='80% LTV'         THEN l.adjustment_pct END) AS `80% LTV`
                  FROM loan_amounts la
                  JOIN loan_amount_ltv_adjustments l ON l.loan_amount_id = la.id
                  JOIN ltv_ratios lr ON lr.id = l.ltv_ratio_id
                  LEFT JOIN loan_types lt ON lt.id = l.loan_type_id
                  WHERE lt.loan_program = ?
                  GROUP BY lt.loan_program, la.display_order, la.amount_range

                  UNION ALL

                  /* ---------- Property Type × LTV (program-aware) ---------- */
                  SELECT
                    'Property Type'       AS row_group,
                    lt.loan_program       AS program,
                    pt.name               AS row_label,
                    MAX(CASE WHEN lr.ratio_range='50% LTV or less' THEN p.adjustment_pct END) AS `50% LTV or less`,
                    MAX(CASE WHEN lr.ratio_range='55% LTV'         THEN p.adjustment_pct END) AS `55% LTV`,
                    MAX(CASE WHEN lr.ratio_range='60% LTV'         THEN p.adjustment_pct END) AS `60% LTV`,
                    MAX(CASE WHEN lr.ratio_range='65% LTV'         THEN p.adjustment_pct END) AS `65% LTV`,
                    MAX(CASE WHEN lr.ratio_range='70% LTV'         THEN p.adjustment_pct END) AS `70% LTV`,
                    MAX(CASE WHEN lr.ratio_range='75% LTV'         THEN p.adjustment_pct END) AS `75% LTV`,
                    MAX(CASE WHEN lr.ratio_range='80% LTV'         THEN p.adjustment_pct END) AS `80% LTV`
                  FROM property_type_ltv_adjustments p
                  JOIN property_types pt ON pt.id = p.property_type_id
                  JOIN ltv_ratios lr ON lr.id = p.ltv_ratio_id
                  LEFT JOIN loan_types lt ON lt.id = p.loan_type_id
                  WHERE lt.loan_program = ?
                  GROUP BY lt.loan_program, pt.name

                  UNION ALL

                  /* ---------- Occupancy × LTV (program-aware) ---------- */
                  SELECT
                    'Occupancy'           AS row_group,
                    lt.loan_program       AS program,
                    oc.name               AS row_label,
                    MAX(CASE WHEN lr.ratio_range='50% LTV or less' THEN o.adjustment_pct END) AS `50% LTV or less`,
                    MAX(CASE WHEN lr.ratio_range='55% LTV'         THEN o.adjustment_pct END) AS `55% LTV`,
                    MAX(CASE WHEN lr.ratio_range='60% LTV'         THEN o.adjustment_pct END) AS `60% LTV`,
                    MAX(CASE WHEN lr.ratio_range='65% LTV'         THEN o.adjustment_pct END) AS `65% LTV`,
                    MAX(CASE WHEN lr.ratio_range='70% LTV'         THEN o.adjustment_pct END) AS `70% LTV`,
                    MAX(CASE WHEN lr.ratio_range='75% LTV'         THEN o.adjustment_pct END) AS `75% LTV`,
                    MAX(CASE WHEN lr.ratio_range='80% LTV'         THEN o.adjustment_pct END) AS `80% LTV`
                  FROM occupancy_ltv_adjustments o
                  JOIN occupancy_types oc ON oc.id = o.occupancy_type_id
                  JOIN ltv_ratios lr ON lr.id = o.ltv_ratio_id
                  LEFT JOIN loan_types lt ON lt.id = o.loan_type_id
                  WHERE lt.loan_program = ?
                  GROUP BY lt.loan_program, oc.name

                  UNION ALL

                  /* ---------- Transaction Type × LTV (program-aware) ---------- */
                  SELECT
                    'Transaction Type'    AS row_group,
                    lt.loan_program       AS program,
                    tt.name               AS row_label,
                    MAX(CASE WHEN lr.ratio_range='50% LTV or less' THEN t.adjustment_pct END) AS `50% LTV or less`,
                    MAX(CASE WHEN lr.ratio_range='55% LTV'         THEN t.adjustment_pct END) AS `55% LTV`,
                    MAX(CASE WHEN lr.ratio_range='60% LTV'         THEN t.adjustment_pct END) AS `60% LTV`,
                    MAX(CASE WHEN lr.ratio_range='65% LTV'         THEN t.adjustment_pct END) AS `65% LTV`,
                    MAX(CASE WHEN lr.ratio_range='70% LTV'         THEN t.adjustment_pct END) AS `70% LTV`,
                    MAX(CASE WHEN lr.ratio_range='75% LTV'         THEN t.adjustment_pct END) AS `75% LTV`,
                    MAX(CASE WHEN lr.ratio_range='80% LTV'         THEN t.adjustment_pct END) AS `80% LTV`
                  FROM transaction_type_ltv_adjustments t
                  JOIN transaction_types tt ON tt.id = t.transaction_type_id
                  JOIN ltv_ratios lr ON lr.id = t.ltv_ratio_id
                  LEFT JOIN loan_types lt ON lt.id = t.loan_type_id
                  WHERE lt.loan_program = ?
                  GROUP BY lt.loan_program, tt.name

                  UNION ALL

                  /* ---------- DSCR Range × LTV (program-aware) ---------- */
                  SELECT
                    'DSCR'                AS row_group,
                    lt.loan_program       AS program,
                    dr.dscr_range         AS row_label,
                    MAX(CASE WHEN lr.ratio_range='50% LTV or less' THEN d.adjustment_pct END) AS `50% LTV or less`,
                    MAX(CASE WHEN lr.ratio_range='55% LTV'         THEN d.adjustment_pct END) AS `55% LTV`,
                    MAX(CASE WHEN lr.ratio_range='60% LTV'         THEN d.adjustment_pct END) AS `60% LTV`,
                    MAX(CASE WHEN lr.ratio_range='65% LTV'         THEN d.adjustment_pct END) AS `65% LTV`,
                    MAX(CASE WHEN lr.ratio_range='70% LTV'         THEN d.adjustment_pct END) AS `70% LTV`,
                    MAX(CASE WHEN lr.ratio_range='75% LTV'         THEN d.adjustment_pct END) AS `75% LTV`,
                    MAX(CASE WHEN lr.ratio_range='80% LTV'         THEN d.adjustment_pct END) AS `80% LTV`
                  FROM dscr_ltv_adjustments d
                  JOIN dscr_ranges dr ON dr.id = d.dscr_range_id
                  JOIN ltv_ratios lr ON lr.id = d.ltv_ratio_id
                  LEFT JOIN loan_types lt ON lt.id = d.loan_type_id
                  WHERE lt.loan_program = ?
                  GROUP BY lt.loan_program, dr.dscr_range

                  UNION ALL

                  /* ---------- Prepay × LTV (program-aware) ---------- */
                  SELECT
                    'Pre Pay'             AS row_group,
                    lt.loan_program       AS program,
                    pp.prepay_name        AS row_label,
                    MAX(CASE WHEN lr.ratio_range='50% LTV or less' THEN p.adjustment_pct END) AS `50% LTV or less`,
                    MAX(CASE WHEN lr.ratio_range='55% LTV'         THEN p.adjustment_pct END) AS `55% LTV`,
                    MAX(CASE WHEN lr.ratio_range='60% LTV'         THEN p.adjustment_pct END) AS `60% LTV`,
                    MAX(CASE WHEN lr.ratio_range='65% LTV'         THEN p.adjustment_pct END) AS `65% LTV`,
                    MAX(CASE WHEN lr.ratio_range='70% LTV'         THEN p.adjustment_pct END) AS `70% LTV`,
                    MAX(CASE WHEN lr.ratio_range='75% LTV'         THEN p.adjustment_pct END) AS `75% LTV`,
                    MAX(CASE WHEN lr.ratio_range='80% LTV'         THEN p.adjustment_pct END) AS `80% LTV`
                  FROM pre_pay_ltv_adjustments p
                  JOIN prepay_periods pp ON pp.id = p.pre_pay_id
                  JOIN ltv_ratios lr ON lr.id = p.ltv_ratio_id
                  LEFT JOIN loan_types lt ON lt.id = p.loan_type_id
                  WHERE lt.loan_program = ?
                  GROUP BY lt.loan_program, pp.prepay_name

                  UNION ALL

                  /* ---------- Loan Type × LTV (program-aware) ---------- */
                  SELECT
                    'Loan Type'           AS row_group,
                    lt.loan_program       AS program,
                    ltd.loan_type_dscr_name AS row_label,
                    MAX(CASE WHEN lr.ratio_range='50% LTV or less' THEN l.adjustment_pct END) AS `50% LTV or less`,
                    MAX(CASE WHEN lr.ratio_range='55% LTV'         THEN l.adjustment_pct END) AS `55% LTV`,
                    MAX(CASE WHEN lr.ratio_range='60% LTV'         THEN l.adjustment_pct END) AS `60% LTV`,
                    MAX(CASE WHEN lr.ratio_range='65% LTV'         THEN l.adjustment_pct END) AS `65% LTV`,
                    MAX(CASE WHEN lr.ratio_range='70% LTV'         THEN l.adjustment_pct END) AS `70% LTV`,
                    MAX(CASE WHEN lr.ratio_range='75% LTV'         THEN l.adjustment_pct END) AS `75% LTV`,
                    MAX(CASE WHEN lr.ratio_range='80% LTV'         THEN l.adjustment_pct END) AS `80% LTV`
                  FROM loan_type_dscr_ltv_adjustments l
                  JOIN loan_types_dscrs ltd ON ltd.id = l.dscr_loan_type_id
                  JOIN ltv_ratios lr ON lr.id = l.ltv_ratio_id
                  LEFT JOIN loan_types lt ON lt.id = l.loan_type_id
                  WHERE lt.loan_program = ?
                  GROUP BY lt.loan_program, ltd.loan_type_dscr_name
                ) AS big_matrix
                ORDER BY
                  FIELD(row_group,
                        'FICO','Loan Amount','Property Type','Occupancy',
                        'Transaction Type','DSCR','Pre Pay','Loan Type'),
                  program IS NULL, program, row_label
            ";

            // Execute the query with parameters
            $matrixData = DB::select($sql, array_fill(0, 8, $loanProgram));

            // Group data by row_group for better display
            $groupedData = collect($matrixData)->groupBy('row_group');

            // Get data for filter dropdowns
            $loanTypes = LoanType::where('name', 'LIKE', '%DSCR%')
                ->orderBy('name')
                ->get(['id', 'name', 'loan_program']);

            $ficoBands = FicoBand::orderBy('fico_min')->get(['id', 'fico_range']);
            $transactionTypes = TransactionType::orderBy('name')->get(['id', 'name']);

            // Get DSCR loan programs
            $loanPrograms = LoanType::select('loan_program')
                ->where('name', 'LIKE', '%DSCR%')
                ->distinct()
                ->orderBy('loan_program')
                ->get()
                ->filter(function ($item) {
                    return !empty($item->loan_program);
                })
                ->mapWithKeys(function ($item) {
                    return [$item->loan_program => $item->loan_program];
                });

            return view('loan-programs.dscr-matrix', compact(
                'groupedData',
                'loanTypes',
                'loanPrograms',
                'ficoBands',
                'transactionTypes',
                'loanProgram'
            ));

        } catch (\Exception $e) {
            return redirect()->route('dashboard')
                ->with('error', 'Failed to load DSCR matrix data. Please try again. Error: ' . $e->getMessage());
        }
    }

    /**
     * Display the complete loan matrix with filters
     * 
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        try {
            // Debug logging
            $filters = $request->get('filter', []);
            \Log::info('LoanProgramController index() called', [
                'all_params' => $request->all(),
                'url' => $request->fullUrl(),
                'view_param' => $request->get('view'),
                'filter_array' => $filters,
                'loan_program_from_filter' => $filters['loan_program'] ?? 'not set'
            ]);

            // Check if this is a request for DSCR matrix view
            $filters = $request->get('filter', []);
            $isDscrMatrix = $request->get('view') === 'dscr-matrix';

            if ($isDscrMatrix) {
                return $this->handleDscrMatrix($request);
            }

            // Regular matrix logic
            return $this->handleRegularMatrix($request);

        } catch (\Exception $e) {
            \Log::error('Loan Programs Index Error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            return redirect()->route('dashboard')
                ->with('error', 'Failed to load loan matrix data. Please try again. Error: ' . $e->getMessage());
        }
    }

    /**
     * Handle DSCR Matrix display with enhanced filters
     */
    private function handleDscrMatrix(Request $request)
    {
        try {
            // Get filter parameters with enhanced filtering
            $filters = $request->get('filter', []);
            $loanProgram = $filters['loan_program'] ?? 'Loan Program #1';
            $loanTypeId = $filters['loan_type_id'] ?? null;
            $ficoBandId = $filters['fico_band_id'] ?? null;
            $transactionTypeId = $filters['transaction_type_id'] ?? null;

            // Enhanced filters
            $ficoScore = $request->get('credit_score');
            $loanAmount = $request->get('loan_amount');
            $propertyTypeId = $filters['property_type_id'] ?? null;
            $occupancyTypeId = $filters['occupancy_type_id'] ?? null;
            $dscrRange = $filters['dscr_range'] ?? null;
            $prepayPeriod = $filters['prepay_period'] ?? null;
            $categoryFilter = $filters['category'] ?? null;
            $dscrInput = $request->get('dscr_input');
            $loanTypeDscrId = $filters['loan_type_dscr_id'] ?? null;

            // Build the enhanced SQL query with dynamic WHERE conditions
            $whereConditions = [];
            $parameters = [];

            // Debug logging for DSCR matrix
            \Log::info('DSCR Matrix Parameters', [
                'loan_program' => $loanProgram,
                'category_filter' => $categoryFilter,
                'fico_score' => $ficoScore,
                'loan_amount' => $loanAmount,
                'property_type_id' => $propertyTypeId,
                'occupancy_type_id' => $occupancyTypeId,
                'dscr_range' => $dscrRange,
                'fico_band_id' => $ficoBandId,
                'transaction_type_id' => $transactionTypeId,
                'prepay_period' => $prepayPeriod,
                'dscr_input' => $dscrInput,
                'loan_type_dscr_id' => $loanTypeDscrId,
                'request_all' => $request->all(),
                'filters_array' => $filters
            ]);

            // Add loan program filter
            $whereConditions[] = "lt.loan_program = ?";
            $parameters[] = $loanProgram;

            $whereClause = !empty($whereConditions) ? 'WHERE ' . implode(' AND ', $whereConditions) : '';

            $sql = "
                SELECT * FROM (
                  /* ---------- FICO × LTV (program-aware) ---------- */
                  SELECT
                    'FICO'                AS row_group,
                    lt.loan_program       AS program,
                    fb.fico_range         AS row_label,
                    MAX(CASE WHEN lr.ratio_range='50% LTV or less' THEN fla.adjustment_pct END) AS `50% LTV or less`,
                    MAX(CASE WHEN lr.ratio_range='55% LTV'         THEN fla.adjustment_pct END) AS `55% LTV`,
                    MAX(CASE WHEN lr.ratio_range='60% LTV'         THEN fla.adjustment_pct END) AS `60% LTV`,
                    MAX(CASE WHEN lr.ratio_range='65% LTV'         THEN fla.adjustment_pct END) AS `65% LTV`,
                    MAX(CASE WHEN lr.ratio_range='70% LTV'         THEN fla.adjustment_pct END) AS `70% LTV`,
                    MAX(CASE WHEN lr.ratio_range='75% LTV'         THEN fla.adjustment_pct END) AS `75% LTV`,
                    MAX(CASE WHEN lr.ratio_range='80% LTV'         THEN fla.adjustment_pct END) AS `80% LTV`
                  FROM fico_bands fb
                  JOIN fico_ltv_adjustments fla ON fla.fico_band_id = fb.id
                  JOIN ltv_ratios lr ON lr.id = fla.ltv_ratio_id
                  LEFT JOIN loan_types lt ON lt.id = fla.loan_type_id
                  WHERE lt.loan_program = ?
                  GROUP BY lt.loan_program, fb.fico_min, fb.fico_range

                  UNION ALL

                  /* ---------- Loan Amount × LTV (program-aware) ---------- */
                  SELECT
                    'Loan Amount'         AS row_group,
                    lt.loan_program       AS program,
                    la.amount_range       AS row_label,
                    MAX(CASE WHEN lr.ratio_range='50% LTV or less' THEN l.adjustment_pct END) AS `50% LTV or less`,
                    MAX(CASE WHEN lr.ratio_range='55% LTV'         THEN l.adjustment_pct END) AS `55% LTV`,
                    MAX(CASE WHEN lr.ratio_range='60% LTV'         THEN l.adjustment_pct END) AS `60% LTV`,
                    MAX(CASE WHEN lr.ratio_range='65% LTV'         THEN l.adjustment_pct END) AS `65% LTV`,
                    MAX(CASE WHEN lr.ratio_range='70% LTV'         THEN l.adjustment_pct END) AS `70% LTV`,
                    MAX(CASE WHEN lr.ratio_range='75% LTV'         THEN l.adjustment_pct END) AS `75% LTV`,
                    MAX(CASE WHEN lr.ratio_range='80% LTV'         THEN l.adjustment_pct END) AS `80% LTV`
                  FROM loan_amounts la
                  JOIN loan_amount_ltv_adjustments l ON l.loan_amount_id = la.id
                  JOIN ltv_ratios lr ON lr.id = l.ltv_ratio_id
                  LEFT JOIN loan_types lt ON lt.id = l.loan_type_id
                  WHERE lt.loan_program = ?
                  GROUP BY lt.loan_program, la.display_order, la.amount_range

                  UNION ALL

                  /* ---------- Property Type × LTV (program-aware) ---------- */
                  SELECT
                    'Property Type'       AS row_group,
                    lt.loan_program       AS program,
                    pt.name               AS row_label,
                    MAX(CASE WHEN lr.ratio_range='50% LTV or less' THEN p.adjustment_pct END) AS `50% LTV or less`,
                    MAX(CASE WHEN lr.ratio_range='55% LTV'         THEN p.adjustment_pct END) AS `55% LTV`,
                    MAX(CASE WHEN lr.ratio_range='60% LTV'         THEN p.adjustment_pct END) AS `60% LTV`,
                    MAX(CASE WHEN lr.ratio_range='65% LTV'         THEN p.adjustment_pct END) AS `65% LTV`,
                    MAX(CASE WHEN lr.ratio_range='70% LTV'         THEN p.adjustment_pct END) AS `70% LTV`,
                    MAX(CASE WHEN lr.ratio_range='75% LTV'         THEN p.adjustment_pct END) AS `75% LTV`,
                    MAX(CASE WHEN lr.ratio_range='80% LTV'         THEN p.adjustment_pct END) AS `80% LTV`
                  FROM property_type_ltv_adjustments p
                  JOIN property_types pt ON pt.id = p.property_type_id
                  JOIN ltv_ratios lr ON lr.id = p.ltv_ratio_id
                  LEFT JOIN loan_types lt ON lt.id = p.loan_type_id
                  WHERE lt.loan_program = ?
                  GROUP BY lt.loan_program, pt.name

                  UNION ALL

                  /* ---------- Occupancy × LTV (program-aware) ---------- */
                  SELECT
                    'Occupancy'           AS row_group,
                    lt.loan_program       AS program,
                    oc.name               AS row_label,
                    MAX(CASE WHEN lr.ratio_range='50% LTV or less' THEN o.adjustment_pct END) AS `50% LTV or less`,
                    MAX(CASE WHEN lr.ratio_range='55% LTV'         THEN o.adjustment_pct END) AS `55% LTV`,
                    MAX(CASE WHEN lr.ratio_range='60% LTV'         THEN o.adjustment_pct END) AS `60% LTV`,
                    MAX(CASE WHEN lr.ratio_range='65% LTV'         THEN o.adjustment_pct END) AS `65% LTV`,
                    MAX(CASE WHEN lr.ratio_range='70% LTV'         THEN o.adjustment_pct END) AS `70% LTV`,
                    MAX(CASE WHEN lr.ratio_range='75% LTV'         THEN o.adjustment_pct END) AS `75% LTV`,
                    MAX(CASE WHEN lr.ratio_range='80% LTV'         THEN o.adjustment_pct END) AS `80% LTV`
                  FROM occupancy_ltv_adjustments o
                  JOIN occupancy_types oc ON oc.id = o.occupancy_type_id
                  JOIN ltv_ratios lr ON lr.id = o.ltv_ratio_id
                  LEFT JOIN loan_types lt ON lt.id = o.loan_type_id
                  WHERE lt.loan_program = ?
                  GROUP BY lt.loan_program, oc.name

                  UNION ALL

                  /* ---------- Transaction Type × LTV (program-aware) ---------- */
                  SELECT
                    'Transaction Type'    AS row_group,
                    lt.loan_program       AS program,
                    tt.name               AS row_label,
                    MAX(CASE WHEN lr.ratio_range='50% LTV or less' THEN t.adjustment_pct END) AS `50% LTV or less`,
                    MAX(CASE WHEN lr.ratio_range='55% LTV'         THEN t.adjustment_pct END) AS `55% LTV`,
                    MAX(CASE WHEN lr.ratio_range='60% LTV'         THEN t.adjustment_pct END) AS `60% LTV`,
                    MAX(CASE WHEN lr.ratio_range='65% LTV'         THEN t.adjustment_pct END) AS `65% LTV`,
                    MAX(CASE WHEN lr.ratio_range='70% LTV'         THEN t.adjustment_pct END) AS `70% LTV`,
                    MAX(CASE WHEN lr.ratio_range='75% LTV'         THEN t.adjustment_pct END) AS `75% LTV`,
                    MAX(CASE WHEN lr.ratio_range='80% LTV'         THEN t.adjustment_pct END) AS `80% LTV`
                  FROM transaction_type_ltv_adjustments t
                  JOIN transaction_types tt ON tt.id = t.transaction_type_id
                  JOIN ltv_ratios lr ON lr.id = t.ltv_ratio_id
                  LEFT JOIN loan_types lt ON lt.id = t.loan_type_id
                  WHERE lt.loan_program = ?
                  GROUP BY lt.loan_program, tt.name

                  UNION ALL

                  /* ---------- DSCR Range × LTV (program-aware) ---------- */
                  SELECT
                    'DSCR'                AS row_group,
                    lt.loan_program       AS program,
                    dr.dscr_range         AS row_label,
                    MAX(CASE WHEN lr.ratio_range='50% LTV or less' THEN d.adjustment_pct END) AS `50% LTV or less`,
                    MAX(CASE WHEN lr.ratio_range='55% LTV'         THEN d.adjustment_pct END) AS `55% LTV`,
                    MAX(CASE WHEN lr.ratio_range='60% LTV'         THEN d.adjustment_pct END) AS `60% LTV`,
                    MAX(CASE WHEN lr.ratio_range='65% LTV'         THEN d.adjustment_pct END) AS `65% LTV`,
                    MAX(CASE WHEN lr.ratio_range='70% LTV'         THEN d.adjustment_pct END) AS `70% LTV`,
                    MAX(CASE WHEN lr.ratio_range='75% LTV'         THEN d.adjustment_pct END) AS `75% LTV`,
                    MAX(CASE WHEN lr.ratio_range='80% LTV'         THEN d.adjustment_pct END) AS `80% LTV`
                  FROM dscr_ltv_adjustments d
                  JOIN dscr_ranges dr ON dr.id = d.dscr_range_id
                  JOIN ltv_ratios lr ON lr.id = d.ltv_ratio_id
                  LEFT JOIN loan_types lt ON lt.id = d.loan_type_id
                  WHERE lt.loan_program = ?
                  GROUP BY lt.loan_program, dr.dscr_range

                  UNION ALL

                  /* ---------- Prepay × LTV (program-aware) ---------- */
                  SELECT
                    'Pre Pay'             AS row_group,
                    lt.loan_program       AS program,
                    pp.prepay_name        AS row_label,
                    MAX(CASE WHEN lr.ratio_range='50% LTV or less' THEN p.adjustment_pct END) AS `50% LTV or less`,
                    MAX(CASE WHEN lr.ratio_range='55% LTV'         THEN p.adjustment_pct END) AS `55% LTV`,
                    MAX(CASE WHEN lr.ratio_range='60% LTV'         THEN p.adjustment_pct END) AS `60% LTV`,
                    MAX(CASE WHEN lr.ratio_range='65% LTV'         THEN p.adjustment_pct END) AS `65% LTV`,
                    MAX(CASE WHEN lr.ratio_range='70% LTV'         THEN p.adjustment_pct END) AS `70% LTV`,
                    MAX(CASE WHEN lr.ratio_range='75% LTV'         THEN p.adjustment_pct END) AS `75% LTV`,
                    MAX(CASE WHEN lr.ratio_range='80% LTV'         THEN p.adjustment_pct END) AS `80% LTV`
                  FROM pre_pay_ltv_adjustments p
                  JOIN prepay_periods pp ON pp.id = p.pre_pay_id
                  JOIN ltv_ratios lr ON lr.id = p.ltv_ratio_id
                  LEFT JOIN loan_types lt ON lt.id = p.loan_type_id
                  WHERE lt.loan_program = ?
                  GROUP BY lt.loan_program, pp.prepay_name

                  UNION ALL

                  /* ---------- Loan Type × LTV (program-aware) ---------- */
                  SELECT
                    'Loan Type'           AS row_group,
                    lt.loan_program       AS program,
                    ltd.loan_type_dscr_name AS row_label,
                    MAX(CASE WHEN lr.ratio_range='50% LTV or less' THEN l.adjustment_pct END) AS `50% LTV or less`,
                    MAX(CASE WHEN lr.ratio_range='55% LTV'         THEN l.adjustment_pct END) AS `55% LTV`,
                    MAX(CASE WHEN lr.ratio_range='60% LTV'         THEN l.adjustment_pct END) AS `60% LTV`,
                    MAX(CASE WHEN lr.ratio_range='65% LTV'         THEN l.adjustment_pct END) AS `65% LTV`,
                    MAX(CASE WHEN lr.ratio_range='70% LTV'         THEN l.adjustment_pct END) AS `70% LTV`,
                    MAX(CASE WHEN lr.ratio_range='75% LTV'         THEN l.adjustment_pct END) AS `75% LTV`,
                    MAX(CASE WHEN lr.ratio_range='80% LTV'         THEN l.adjustment_pct END) AS `80% LTV`
                  FROM loan_type_dscr_ltv_adjustments l
                  JOIN loan_types_dscrs ltd ON ltd.id = l.dscr_loan_type_id
                  JOIN ltv_ratios lr ON lr.id = l.ltv_ratio_id
                  LEFT JOIN loan_types lt ON lt.id = l.loan_type_id
                  WHERE lt.loan_program = ?
                  GROUP BY lt.loan_program, ltd.loan_type_dscr_name
                ) AS big_matrix
                ORDER BY
                  FIELD(row_group,
                        'FICO','Loan Amount','Property Type','Occupancy',
                        'Transaction Type','DSCR','Pre Pay','Loan Type'),
                  program IS NULL, program, row_label
            ";

            // Execute the query with parameters (repeat parameters for each UNION query)
            $matrixData = DB::select($sql, array_fill(0, 8, $loanProgram));

            \Log::info('DSCR Matrix Query Result', [
                'query_parameter' => $loanProgram,
                'result_count' => count($matrixData),
                'first_few_results' => array_slice($matrixData, 0, 3)
            ]);

            // Apply additional filtering to the results
            $filteredData = collect($matrixData);

            // Apply category filter if specified
            if ($categoryFilter) {
                $filteredData = $filteredData->filter(function ($row) use ($categoryFilter) {
                    return $row->row_group === $categoryFilter;
                });
            }

            // Apply FICO score filter if specified (only applies to FICO rows)
            if ($ficoScore) {
                $filteredData = $filteredData->filter(function ($row) use ($ficoScore) {
                    if ($row->row_group === 'FICO') {
                        // Extract FICO range from row_label (e.g., "700-719")
                        $label = $row->row_label;
                        if (preg_match('/(\d+)-(\d+)/', $label, $matches)) {
                            $minFico = (int) $matches[1];
                            $maxFico = (int) $matches[2];
                            return $ficoScore >= $minFico && $ficoScore <= $maxFico;
                        }
                        return false; // If pattern doesn't match, exclude this FICO row
                    }
                    // For non-FICO rows, exclude them when FICO filter is applied
                    // This way only the matching FICO row is shown
                    return false;
                });
            }

            // Apply loan amount filter if specified (only applies to Loan Amount rows)
            if ($loanAmount) {
                $filteredData = $filteredData->filter(function ($row) use ($loanAmount) {
                    if ($row->row_group === 'Loan Amount') {
                        // Extract loan amount range from row_label (e.g., "$100,000 - $249,999")
                        $label = $row->row_label;
                        // Remove dollar signs and commas, then extract numbers
                        $cleanLabel = str_replace(['$', ','], '', $label);
                        if (preg_match('/(\d+)\s*-\s*(\d+)/', $cleanLabel, $matches)) {
                            $minAmount = (int) $matches[1];
                            $maxAmount = (int) $matches[2];
                            return $loanAmount >= $minAmount && $loanAmount <= $maxAmount;
                        } elseif (preg_match('/(\d+)\+/', $cleanLabel, $matches)) {
                            // Handle ranges like "500000+" 
                            $minAmount = (int) $matches[1];
                            return $loanAmount >= $minAmount;
                        }
                        return false;
                    }
                    // For non-Loan Amount rows, exclude them when loan amount filter is applied
                    return false;
                });
            }

            // Apply FICO Band filter if specified (only applies to FICO rows)
            if ($ficoBandId) {
                $ficoBand = FicoBand::find($ficoBandId);
                if ($ficoBand) {
                    $filteredData = $filteredData->filter(function ($row) use ($ficoBand) {
                        if ($row->row_group === 'FICO') {
                            return $row->row_label === $ficoBand->fico_range;
                        }
                        return false;
                    });
                }
            }

            // Apply Property Type filter if specified (only applies to Property Type rows)
            if ($propertyTypeId) {
                $propertyType = \App\Models\PropertyType::find($propertyTypeId);
                if ($propertyType) {
                    $filteredData = $filteredData->filter(function ($row) use ($propertyType) {
                        if ($row->row_group === 'Property Type') {
                            return $row->row_label === $propertyType->name;
                        }
                        return false;
                    });
                }
            }

            // Apply Occupancy Type filter if specified (only applies to Occupancy rows)
            if ($occupancyTypeId) {
                $occupancyType = \App\Models\OccupancyTypes::find($occupancyTypeId);
                if ($occupancyType) {
                    $filteredData = $filteredData->filter(function ($row) use ($occupancyType) {
                        if ($row->row_group === 'Occupancy') {
                            return $row->row_label === $occupancyType->name;
                        }
                        return false;
                    });
                }
            }

            // Apply DSCR Range filter if specified (only applies to DSCR rows)
            if ($dscrRange) {
                $dscrRangeModel = \App\Models\DscrRanges::find($dscrRange);
                if ($dscrRangeModel) {
                    $filteredData = $filteredData->filter(function ($row) use ($dscrRangeModel) {
                        if ($row->row_group === 'DSCR') {
                            return $row->row_label === $dscrRangeModel->dscr_range;
                        }
                        return false;
                    });
                }
            }

            // Apply Transaction Type filter if specified (only applies to Transaction Type rows)
            if ($transactionTypeId) {
                $transactionType = TransactionType::find($transactionTypeId);
                if ($transactionType) {
                    $filteredData = $filteredData->filter(function ($row) use ($transactionType) {
                        if ($row->row_group === 'Transaction Type') {
                            return $row->row_label === $transactionType->name;
                        }
                        return false;
                    });
                }
            }

            // Apply Prepay Period filter if specified (only applies to Pre Pay rows)
            if ($prepayPeriod) {
                $prepayPeriodModel = \App\Models\PrepayPeriods::find($prepayPeriod);
                if ($prepayPeriodModel) {
                    $filteredData = $filteredData->filter(function ($row) use ($prepayPeriodModel) {
                        if ($row->row_group === 'Pre Pay') {
                            return $row->row_label === $prepayPeriodModel->prepay_name;
                        }
                        return false;
                    });
                }
            }

            // Apply DSCR Input filter if specified (only applies to DSCR rows)
            if ($dscrInput) {
                $filteredData = $filteredData->filter(function ($row) use ($dscrInput) {
                    if ($row->row_group === 'DSCR') {
                        // Extract DSCR range from row_label (e.g., "1.20 - 1.39")
                        $label = $row->row_label;
                        if (preg_match('/(\d+\.?\d*)\s*-\s*(\d+\.?\d*)/', $label, $matches)) {
                            $minDscr = (float) $matches[1];
                            $maxDscr = (float) $matches[2];
                            return $dscrInput >= $minDscr && $dscrInput <= $maxDscr;
                        } elseif (preg_match('/(\d+\.?\d*)\+/', $label, $matches)) {
                            // Handle ranges like "1.50+"
                            $minDscr = (float) $matches[1];
                            return $dscrInput >= $minDscr;
                        }
                        return false;
                    }
                    return false;
                });
            }

            // Apply Loan Type DSCR filter if specified (only applies to Loan Type rows)
            if ($loanTypeDscrId) {
                $loanTypeDscr = \App\Models\LoanTypesDscr::find($loanTypeDscrId);
                if ($loanTypeDscr) {
                    $filteredData = $filteredData->filter(function ($row) use ($loanTypeDscr) {
                        if ($row->row_group === 'Loan Type') {
                            return $row->row_label === $loanTypeDscr->loan_type_dscr_name;
                        }
                        return false;
                    });
                }
            }

            // Convert back to array for grouping
            $matrixData = $filteredData->all();

            \Log::info('DSCR Matrix After Filtering', [
                'filtered_count' => count($matrixData),
                'category_filter' => $categoryFilter,
                'fico_score' => $ficoScore,
                'loan_amount' => $loanAmount,
                'property_type_id' => $propertyTypeId,
                'occupancy_type_id' => $occupancyTypeId,
                'dscr_range' => $dscrRange,
                'fico_band_id' => $ficoBandId,
                'transaction_type_id' => $transactionTypeId,
                'prepay_period' => $prepayPeriod,
                'dscr_input' => $dscrInput,
                'loan_type_dscr_id' => $loanTypeDscrId,
                'filtered_results' => array_slice($matrixData, 0, 3)
            ]);

            // Group data by row_group for better display
            $groupedData = collect($matrixData)->groupBy('row_group');

            \Log::info('DSCR Grouped Data', [
                'grouped_data_count' => $groupedData->count(),
                'grouped_data_keys' => $groupedData->keys()->toArray(),
                'first_group_count' => $groupedData->first() ? $groupedData->first()->count() : 0
            ]);

            // Get data for filter dropdowns
            $loanTypes = LoanType::where('name', 'LIKE', '%DSCR%')
                ->orderBy('name')
                ->get(['id', 'name', 'loan_program']);

            $ficoBands = FicoBand::orderBy('fico_min')->get(['id', 'fico_range']);
            $transactionTypes = TransactionType::orderBy('name')->get(['id', 'name']);

            // Get additional filter data for DSCR matrix
            $propertyTypes = \App\Models\PropertyType::orderBy('name')->get(['id', 'name']);
            $occupancyTypes = \App\Models\OccupancyTypes::orderBy('name')->get(['id', 'name']);
            $dscrRanges = \App\Models\DscrRanges::orderBy('dscr_range')->get(['id', 'dscr_range']);
            $prepayPeriods = \App\Models\PrepayPeriods::orderBy('prepay_name')->get(['id', 'prepay_name']);
            $loanTypesDscr = \App\Models\LoanTypesDscr::orderBy('display_order')->get(['id', 'loan_type_dscr_name']);

            // Get DSCR loan programs
            $loanPrograms = LoanType::select('loan_program')
                ->where('name', 'LIKE', '%DSCR%')
                ->distinct()
                ->orderBy('loan_program')
                ->get()
                ->filter(function ($item) {
                    return !empty($item->loan_program);
                })
                ->mapWithKeys(function ($item) {
                    return [$item->loan_program => $item->loan_program];
                });

            return view('loan-programs.index', compact(
                'groupedData',
                'loanTypes',
                'loanPrograms',
                'ficoBands',
                'transactionTypes',
                'propertyTypes',
                'occupancyTypes',
                'dscrRanges',
                'prepayPeriods',
                'loanTypesDscr',
                'loanProgram'
            ))->with([
                        'isDscrMatrix' => true,
                        'currentLoanProgram' => $loanProgram,
                        'isQuickSearch' => false,
                        'searchInfo' => [],
                        'matrixData' => []
                    ]);

        } catch (\Exception $e) {
            \Log::error('DSCR Matrix Error: ' . $e->getMessage());
            throw $e; // Re-throw to be caught by parent method
        }
    }

    /**
     * Handle Regular Matrix display
     */
    private function handleRegularMatrix(Request $request)
    {
        try {
            \Log::info('Regular Matrix Processing Started', [
                'request_params' => $request->all()
            ]);

            // Build query using Laravel Query Builder with Spatie QueryBuilder for filters
            $matrixQuery = QueryBuilder::for(LoanRule::class)
                ->allowedFilters([
                    AllowedFilter::callback('loan_type_id', function ($query, $value) {
                        $query->whereHas('experience.loanType', function ($q) use ($value) {
                            $q->where('id', $value);
                        });
                    }),
                    AllowedFilter::callback('loan_program', function ($query, $value) {
                        $query->whereHas('experience.loanType', function ($q) use ($value) {
                            $q->where('loan_program', $value);
                        });
                    }),
                    AllowedFilter::exact('experience_id'),
                    AllowedFilter::exact('fico_band_id'),
                    AllowedFilter::exact('transaction_type_id'),
                    // Add DSCR-specific filters that are ignored for regular matrix
                    AllowedFilter::callback('category', function ($query, $value) {
                        // This filter is only used for DSCR matrix, so ignore it here
                        return $query;
                    }),
                    AllowedFilter::callback('property_type_id', function ($query, $value) {
                        // This filter is only used for DSCR matrix, so ignore it here
                        return $query;
                    }),
                    AllowedFilter::callback('occupancy_type_id', function ($query, $value) {
                        // This filter is only used for DSCR matrix, so ignore it here
                        return $query;
                    }),
                    AllowedFilter::callback('dscr_range', function ($query, $value) {
                        // This filter is only used for DSCR matrix, so ignore it here
                        return $query;
                    }),
                    AllowedFilter::callback('loan_type_dscr_id', function ($query, $value) {
                        // This filter is only used for DSCR matrix, so ignore it here
                        return $query;
                    }),
                    AllowedFilter::callback('prepay_period', function ($query, $value) {
                        // This filter is only used for DSCR matrix, so ignore it here
                        return $query;
                    }),
                ])
                ->with([
                    'experience.loanType',
                    'ficoBand',
                    'transactionType',
                    'rehabLimits.rehabLevel',
                    'pricings.pricingTier'
                ])
                ->join('experiences', 'loan_rules.experience_id', '=', 'experiences.id')
                ->join('fico_bands', 'loan_rules.fico_band_id', '=', 'fico_bands.id')
                ->join('loan_types', 'experiences.loan_type_id', '=', 'loan_types.id')
                // Handle credit score search
                ->when($request->has('credit_score') && $request->credit_score, function ($query) use ($request) {
                    $creditScore = (int) $request->credit_score;
                    $query->where('fico_bands.fico_min', '<=', $creditScore)
                        ->where('fico_bands.fico_max', '>=', $creditScore);
                })
                // Handle experience years search
                ->when($request->has('experience_years') && is_numeric($request->experience_years), function ($query) use ($request) {
                    $experienceYears = (int) $request->experience_years;
                    $query->where('experiences.min_experience', '<=', $experienceYears)
                        ->where('experiences.max_experience', '>=', $experienceYears);
                })
                // Filter by FULL APPRAISAL by default unless loan_program filter is specified or quick search is used
                ->when(!$request->input('filter.loan_program') && !$request->has('credit_score') && !$request->has('experience_years'), function ($query) {
                    $query->where('loan_types.loan_program', 'FULL APPRAISAL');
                })
                ->orderByRaw("
                CASE experiences.experiences_range
                    WHEN '0' THEN 0 
                    WHEN '1-2' THEN 1 
                    WHEN '3-4' THEN 2 
                    WHEN '5-9' THEN 3 
                    WHEN '10+' THEN 4 
                    ELSE 99
                END
            ")
                ->orderBy('fico_bands.fico_min')
                ->orderBy('fico_bands.fico_max')
                ->select('loan_rules.*');

            $loanRules = $matrixQuery->get();

            // Transform the data to match the matrix format
            $matrixData = $loanRules->map(function ($rule) {
                // Get rehab limits grouped by rehab level
                $rehabLimits = $rule->rehabLimits->keyBy('rehabLevel.name');

                // Get pricing data grouped by pricing tier
                $pricings = $rule->pricings->keyBy('pricingTier.price_range');

                return (object) [
                    'loan_rule_id' => $rule->id,
                    'loan_type' => $rule->experience->loanType->name ?? 'N/A',
                    'loan_program' => $rule->experience->loanType->loan_program ?? null,
                    'display_name' => $rule->experience->loanType->loan_program
                        ? ($rule->experience->loanType->name . ' - ' . $rule->experience->loanType->loan_program)
                        : ($rule->experience->loanType->name ?? 'N/A'),
                    'experience' => $rule->experience->experiences_range ?? 'N/A',
                    'fico' => $rule->ficoBand->fico_range ?? 'N/A',
                    'transaction_type' => $rule->transactionType->name ?? 'N/A',
                    'max_total_loan' => $rule->max_total_loan,
                    'max_budget' => $rule->max_budget,

                    // Light Rehab
                    'light_ltc' => $rehabLimits->get('LIGHT REHAB')?->max_ltc,
                    'light_ltv' => $rehabLimits->get('LIGHT REHAB')?->max_ltv,

                    // Moderate Rehab
                    'moderate_ltc' => $rehabLimits->get('MODERATE REHAB')?->max_ltc,
                    'moderate_ltv' => $rehabLimits->get('MODERATE REHAB')?->max_ltv,

                    // Heavy Rehab
                    'heavy_ltc' => $rehabLimits->get('HEAVY REHAB')?->max_ltc,
                    'heavy_ltv' => $rehabLimits->get('HEAVY REHAB')?->max_ltv,

                    // Extensive Rehab
                    'extensive_ltc' => $rehabLimits->get('EXTENSIVE REHAB')?->max_ltc,
                    'extensive_ltv' => $rehabLimits->get('EXTENSIVE REHAB')?->max_ltv,
                    'extensive_ltfc' => $rehabLimits->get('EXTENSIVE REHAB')?->max_ltfc,

                    // Pricing < $250k
                    'ir_lt_250k' => $pricings->get('<250k')?->interest_rate,
                    'lp_lt_250k' => $pricings->get('<250k')?->lender_points,

                    // Pricing $250k-$500k
                    'ir_250_500k' => $pricings->get('250-500k')?->interest_rate,
                    'lp_250_500k' => $pricings->get('250-500k')?->lender_points,

                    // Pricing ≥ $500k
                    'ir_gte_500k' => $pricings->get('>=500k')?->interest_rate,
                    'lp_gte_500k' => $pricings->get('>=500k')?->lender_points,
                ];
            });

            // Group data by display_name (loan type + program) instead of just loan_type
            $groupedData = $matrixData->groupBy('display_name');
            $processedData = [];

            foreach ($groupedData as $displayName => $rows) {
                $processedData[$displayName] = $rows;
            }

            $matrixData = $processedData;

            // Get data for filter dropdowns
            $loanTypes = LoanType::with(['states', 'propertyTypes'])
                ->orderBy('name')
                ->get(['id', 'name', 'loan_program']);
            $ficoBands = FicoBand::orderBy('fico_min')->get(['id', 'fico_range']);
            $transactionTypes = TransactionType::orderBy('name')->get(['id', 'name']);

            // Get unique loan programs for the filter dropdown
            $loanPrograms = LoanType::select('loan_program', 'name')
                ->distinct()
                ->orderBy('loan_program')
                ->get()
                ->filter(function ($item) {
                    return !empty($item->loan_program);
                })
                ->mapWithKeys(function ($item) {
                    // Create a more descriptive display name
                    $displayName = $item->loan_program;
                    if ($item->loan_program === '#1') {
                        $displayName = 'DSCR Rental - Program #1';
                    }
                    return [$item->loan_program => $displayName];
                });

            // Determine current loan program for header
            $currentLoanProgram = $request->input('filter.loan_program', 'FULL APPRAISAL');

            // Check if this is a quick search
            $isQuickSearch = $request->has('credit_score') || $request->has('experience_years');
            $searchInfo = [];
            if ($isQuickSearch) {
                if ($request->credit_score) {
                    $searchInfo['credit_score'] = $request->credit_score;
                }
                if ($request->experience_years !== null && $request->experience_years !== '') {
                    $searchInfo['experience_years'] = $request->experience_years;
                }
            }

            return view('loan-programs.index', compact(
                'matrixData',
                'loanTypes',
                'loanPrograms',
                'ficoBands',
                'transactionTypes',
                'currentLoanProgram',
                'isQuickSearch',
                'searchInfo'
            ))->with([
                        'isDscrMatrix' => false,
                        'groupedData' => collect([]),
                        'propertyTypes' => collect([]),
                        'occupancyTypes' => collect([]),
                        'dscrRanges' => collect([]),
                        'prepayPeriods' => collect([]),
                        'loanProgram' => ''
                    ]);

        } catch (\Exception $e) {
            \Log::error('Regular Matrix Error Details', [
                'error_message' => $e->getMessage(),
                'error_file' => $e->getFile(),
                'error_line' => $e->getLine(),
                'request_params' => $request->all(),
                'request_url' => $request->fullUrl(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e; // Re-throw to be caught by parent method
        }
    }

    /**
     * Show form to create new loan program entry
     * 
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create()
    {
        try {
            // Get data for select options
            $experiences = Experience::with('loanType')->orderBy('experiences_range')->get();
            $ficoBands = FicoBand::orderBy('fico_min')->get();
            $transactionTypes = TransactionType::orderBy('name')->get();
            $rehabLevels = RehabLevel::orderBy('name')->get();
            $pricingTiers = PricingTier::orderBy('price_range')->get();

            return view('loan-programs.create', compact(
                'experiences',
                'ficoBands',
                'transactionTypes',
                'rehabLevels',
                'pricingTiers'
            ));

        } catch (\Exception $e) {
            return redirect()->route('loan-programs.index')
                ->with('error', 'Error loading create form: ' . $e->getMessage());
        }
    }

    /**
     * Store new loan program entry
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'experience_id' => 'required|exists:experiences,id',
            'fico_band_id' => 'required|exists:fico_bands,id',
            'transaction_type_id' => 'required|exists:transaction_types,id',
            'max_total_loan' => 'required|numeric|min:0',
            'max_budget' => 'required|numeric|min:0',

            // Rehab limits validation
            'rehab_limits' => 'array',
            'rehab_limits.*.rehab_level_id' => 'required|exists:rehab_levels,id',
            'rehab_limits.*.max_ltc' => 'nullable|numeric|min:0|max:100',
            'rehab_limits.*.max_ltv' => 'nullable|numeric|min:0|max:100',
            'rehab_limits.*.max_ltfc' => 'nullable|numeric|min:0|max:100',

            // Pricing validation
            'pricings' => 'array',
            'pricings.*.pricing_tier_id' => 'required|exists:pricing_tiers,id',
            'pricings.*.interest_rate' => 'nullable|numeric|min:0|max:50',
            'pricings.*.lender_points' => 'nullable|numeric|min:0|max:10',
        ]);

        try {
            \DB::transaction(function () use ($request) {
                // Check for duplicate loan rule combination
                $existingRule = LoanRule::where([
                    'experience_id' => $request->experience_id,
                    'fico_band_id' => $request->fico_band_id,
                    'transaction_type_id' => $request->transaction_type_id,
                ])->first();

                if ($existingRule) {
                    throw new \Exception('A loan rule with this combination of Experience, FICO Band, and Transaction Type already exists.');
                }

                // Create main loan rule
                $loanRule = LoanRule::create([
                    'experience_id' => $request->experience_id,
                    'fico_band_id' => $request->fico_band_id,
                    'transaction_type_id' => $request->transaction_type_id,
                    'max_total_loan' => $request->max_total_loan,
                    'max_budget' => $request->max_budget,
                ]);

                // Create rehab limits
                if ($request->has('rehab_limits')) {
                    foreach ($request->rehab_limits as $rehabLimit) {
                        if (!empty($rehabLimit['rehab_level_id'])) {
                            $loanRule->rehabLimits()->create([
                                'rehab_level_id' => $rehabLimit['rehab_level_id'],
                                'max_ltc' => $rehabLimit['max_ltc'] ?? null,
                                'max_ltv' => $rehabLimit['max_ltv'] ?? null,
                                'max_ltfc' => $rehabLimit['max_ltfc'] ?? null,
                            ]);
                        }
                    }
                }

                // Create pricings
                if ($request->has('pricings')) {
                    foreach ($request->pricings as $pricing) {
                        if (!empty($pricing['pricing_tier_id'])) {
                            $loanRule->pricings()->create([
                                'pricing_tier_id' => $pricing['pricing_tier_id'],
                                'interest_rate' => $pricing['interest_rate'] ?? null,
                                'lender_points' => $pricing['lender_points'] ?? null,
                            ]);
                        }
                    }
                }
            });

            return redirect()->route('loan-programs.index')
                ->with('success', 'Loan program created successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error creating loan program: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Show form to edit existing loan program entry
     * 
     * @param int $id
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit($id)
    {
        try {
            $loanRule = LoanRule::with([
                'experience.loanType',
                'ficoBand',
                'transactionType',
                'rehabLimits.rehabLevel',
                'pricings.pricingTier'
            ])->findOrFail($id);

            // Get data for select options
            $experiences = Experience::with('loanType')->orderBy('experiences_range')->get();
            $ficoBands = FicoBand::orderBy('fico_min')->get();
            $transactionTypes = TransactionType::orderBy('name')->get();
            $rehabLevels = RehabLevel::orderBy('name')->get();
            $pricingTiers = PricingTier::orderBy('price_range')->get();

            return view('loan-programs.edit', compact(
                'loanRule',
                'experiences',
                'ficoBands',
                'transactionTypes',
                'rehabLevels',
                'pricingTiers'
            ));

        } catch (\Exception $e) {
            return redirect()->route('loan-programs.index')
                ->with('error', 'Loan rule not found or error loading edit form: ' . $e->getMessage());
        }
    }

    /**
     * Update existing loan program entry
     * 
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(\Illuminate\Http\Request $request, $id)
    {
        $request->validate([
            'experience_id' => 'required|exists:experiences,id',
            'fico_band_id' => 'required|exists:fico_bands,id',
            'transaction_type_id' => 'required|exists:transaction_types,id',
            'max_total_loan' => 'required|numeric|min:0',
            'max_budget' => 'required|numeric|min:0',

            // Rehab limits validation
            'rehab_limits' => 'array',
            'rehab_limits.*.rehab_level_id' => 'required|exists:rehab_levels,id',
            'rehab_limits.*.max_ltc' => 'nullable|numeric|min:0|max:100',
            'rehab_limits.*.max_ltv' => 'nullable|numeric|min:0|max:100',
            'rehab_limits.*.max_ltfc' => 'nullable|numeric|min:0|max:100',

            // Pricing validation
            'pricings' => 'array',
            'pricings.*.pricing_tier_id' => 'required|exists:pricing_tiers,id',
            'pricings.*.interest_rate' => 'nullable|numeric|min:0|max:50',
            'pricings.*.lender_points' => 'nullable|numeric|min:0|max:10',
        ]);

        try {
            \DB::transaction(function () use ($request, $id) {
                $loanRule = LoanRule::findOrFail($id);

                // Update main loan rule data
                $loanRule->update([
                    'experience_id' => $request->experience_id,
                    'fico_band_id' => $request->fico_band_id,
                    'transaction_type_id' => $request->transaction_type_id,
                    'max_total_loan' => $request->max_total_loan,
                    'max_budget' => $request->max_budget,
                ]);

                // Update rehab limits
                if ($request->has('rehab_limits')) {
                    // Delete existing rehab limits
                    $loanRule->rehabLimits()->delete();

                    // Create new rehab limits
                    foreach ($request->rehab_limits as $rehabLimit) {
                        if (!empty($rehabLimit['rehab_level_id'])) {
                            $loanRule->rehabLimits()->create([
                                'rehab_level_id' => $rehabLimit['rehab_level_id'],
                                'max_ltc' => $rehabLimit['max_ltc'] ?? null,
                                'max_ltv' => $rehabLimit['max_ltv'] ?? null,
                                'max_ltfc' => $rehabLimit['max_ltfc'] ?? null,
                            ]);
                        }
                    }
                }

                // Update pricings
                if ($request->has('pricings')) {
                    // Delete existing pricings
                    $loanRule->pricings()->delete();

                    // Create new pricings
                    foreach ($request->pricings as $pricing) {
                        if (!empty($pricing['pricing_tier_id'])) {
                            $loanRule->pricings()->create([
                                'pricing_tier_id' => $pricing['pricing_tier_id'],
                                'interest_rate' => $pricing['interest_rate'] ?? null,
                                'lender_points' => $pricing['lender_points'] ?? null,
                            ]);
                        }
                    }
                }
            });

            return redirect()->route('loan-programs.index')
                ->with('success', 'Loan program updated successfully!');

        } catch (\Illuminate\Validation\ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->validator)
                ->withInput();
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Error updating loan program: ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Get loan type restrictions (states and property types)
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLoanTypeRestrictions(Request $request)
    {
        $loanTypeId = $request->query('loan_type_id');

        if (!$loanTypeId) {
            return response()->json(['error' => 'Loan type ID is required'], 400);
        }

        $loanType = LoanType::with(['states', 'propertyTypes'])->find($loanTypeId);

        if (!$loanType) {
            return response()->json(['error' => 'Loan type not found'], 404);
        }

        return response()->json([
            'states' => $loanType->states->map(function ($state) {
                return [
                    'id' => $state->id,
                    'code' => $state->code,
                    'name' => $state->code // Since we only have code, use it as name too
                ];
            }),
            'property_types' => $loanType->propertyTypes->map(function ($propertyType) {
                return [
                    'id' => $propertyType->id,
                    'name' => $propertyType->name
                ];
            }),
            'loan_program' => $loanType->loan_program
        ]);
    }

    /**
     * Update DSCR matrix cell value
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateDscrMatrixCell(Request $request)
    {
        try {
            $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
                'row_group' => 'required|string',
                'row_label' => 'required|string',
                'ltv_column' => 'required|string',
                'program' => 'required|string',
                'value' => 'required|numeric'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 400);
            }

            $rowGroup = $request->row_group;
            $rowLabel = $request->row_label;
            $ltvColumn = $request->ltv_column;
            $program = $request->program;
            $value = $request->value;

            // Map LTV column names to database column names
            $ltvColumnMapping = [
                '50% LTV or less' => '50% LTV or less',
                '55% LTV' => '55% LTV',
                '60% LTV' => '60% LTV',
                '65% LTV' => '65% LTV',
                '70% LTV' => '70% LTV',
                '75% LTV' => '75% LTV',
                '80% LTV' => '80% LTV'
            ];

            if (!isset($ltvColumnMapping[$ltvColumn])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid LTV column'
                ], 400);
            }

            $dbColumn = $ltvColumnMapping[$ltvColumn];

            // Determine which table to update based on row_group
            $updated = false;

            switch ($rowGroup) {
                case 'FICO':
                    $updated = $this->updateFicoLtvAdjustment($rowLabel, $dbColumn, $value, $program);
                    break;
                case 'Loan Amount':
                    $updated = $this->updateLoanAmountLtvAdjustment($rowLabel, $dbColumn, $value, $program);
                    break;
                case 'Property Type':
                    $updated = $this->updatePropertyTypeLtvAdjustment($rowLabel, $dbColumn, $value, $program);
                    break;
                case 'Occupancy':
                    $updated = $this->updateOccupancyLtvAdjustment($rowLabel, $dbColumn, $value, $program);
                    break;
                case 'Transaction Type':
                    $updated = $this->updateTransactionTypeLtvAdjustment($rowLabel, $dbColumn, $value, $program);
                    break;
                case 'DSCR':
                    $updated = $this->updateDscrLtvAdjustment($rowLabel, $dbColumn, $value, $program);
                    break;
                case 'Pre Pay':
                    $updated = $this->updatePrePayLtvAdjustment($rowLabel, $dbColumn, $value, $program);
                    break;
                case 'Loan Type':
                    $updated = $this->updateLoanTypeDscrLtvAdjustment($rowLabel, $dbColumn, $value, $program);
                    break;
                default:
                    return response()->json([
                        'success' => false,
                        'message' => 'Unknown row group: ' . $rowGroup
                    ], 400);
            }

            if ($updated) {
                return response()->json([
                    'success' => true,
                    'message' => 'Value updated successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update value'
                ], 500);
            }

        } catch (\Exception $e) {
            \Log::error('DSCR Matrix Update Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the value',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Update FICO LTV adjustment
     */
    private function updateFicoLtvAdjustment($ficoRange, $ltvColumn, $value, $program)
    {
        return DB::table('fico_ltv_adjustments')
            ->join('fico_bands', 'fico_ltv_adjustments.fico_band_id', '=', 'fico_bands.id')
            ->join('ltv_ratios', 'fico_ltv_adjustments.ltv_ratio_id', '=', 'ltv_ratios.id')
            ->join('loan_types', 'fico_ltv_adjustments.loan_type_id', '=', 'loan_types.id')
            ->where('fico_bands.fico_range', $ficoRange)
            ->where('ltv_ratios.ratio_range', $ltvColumn)
            ->where('loan_types.loan_program', $program)
            ->update(['fico_ltv_adjustments.adjustment_pct' => $value]);
    }

    /**
     * Update Loan Amount LTV adjustment
     */
    private function updateLoanAmountLtvAdjustment($amountRange, $ltvColumn, $value, $program)
    {
        return DB::table('loan_amount_ltv_adjustments')
            ->join('loan_amounts', 'loan_amount_ltv_adjustments.loan_amount_id', '=', 'loan_amounts.id')
            ->join('ltv_ratios', 'loan_amount_ltv_adjustments.ltv_ratio_id', '=', 'ltv_ratios.id')
            ->join('loan_types', 'loan_amount_ltv_adjustments.loan_type_id', '=', 'loan_types.id')
            ->where('loan_amounts.amount_range', $amountRange)
            ->where('ltv_ratios.ratio_range', $ltvColumn)
            ->where('loan_types.loan_program', $program)
            ->update(['loan_amount_ltv_adjustments.adjustment_pct' => $value]);
    }

    /**
     * Update Property Type LTV adjustment
     */
    private function updatePropertyTypeLtvAdjustment($propertyType, $ltvColumn, $value, $program)
    {
        return DB::table('property_type_ltv_adjustments')
            ->join('property_types', 'property_type_ltv_adjustments.property_type_id', '=', 'property_types.id')
            ->join('ltv_ratios', 'property_type_ltv_adjustments.ltv_ratio_id', '=', 'ltv_ratios.id')
            ->join('loan_types', 'property_type_ltv_adjustments.loan_type_id', '=', 'loan_types.id')
            ->where('property_types.name', $propertyType)
            ->where('ltv_ratios.ratio_range', $ltvColumn)
            ->where('loan_types.loan_program', $program)
            ->update(['property_type_ltv_adjustments.adjustment_pct' => $value]);
    }

    /**
     * Update Occupancy LTV adjustment
     */
    private function updateOccupancyLtvAdjustment($occupancyType, $ltvColumn, $value, $program)
    {
        return DB::table('occupancy_ltv_adjustments')
            ->join('occupancy_types', 'occupancy_ltv_adjustments.occupancy_type_id', '=', 'occupancy_types.id')
            ->join('ltv_ratios', 'occupancy_ltv_adjustments.ltv_ratio_id', '=', 'ltv_ratios.id')
            ->join('loan_types', 'occupancy_ltv_adjustments.loan_type_id', '=', 'loan_types.id')
            ->where('occupancy_types.name', $occupancyType)
            ->where('ltv_ratios.ratio_range', $ltvColumn)
            ->where('loan_types.loan_program', $program)
            ->update(['occupancy_ltv_adjustments.adjustment_pct' => $value]);
    }

    /**
     * Update Transaction Type LTV adjustment
     */
    private function updateTransactionTypeLtvAdjustment($transactionType, $ltvColumn, $value, $program)
    {
        return DB::table('transaction_type_ltv_adjustments')
            ->join('transaction_types', 'transaction_type_ltv_adjustments.transaction_type_id', '=', 'transaction_types.id')
            ->join('ltv_ratios', 'transaction_type_ltv_adjustments.ltv_ratio_id', '=', 'ltv_ratios.id')
            ->join('loan_types', 'transaction_type_ltv_adjustments.loan_type_id', '=', 'loan_types.id')
            ->where('transaction_types.name', $transactionType)
            ->where('ltv_ratios.ratio_range', $ltvColumn)
            ->where('loan_types.loan_program', $program)
            ->update(['transaction_type_ltv_adjustments.adjustment_pct' => $value]);
    }

    /**
     * Update DSCR LTV adjustment
     */
    private function updateDscrLtvAdjustment($dscrRange, $ltvColumn, $value, $program)
    {
        return DB::table('dscr_ltv_adjustments')
            ->join('dscr_ranges', 'dscr_ltv_adjustments.dscr_range_id', '=', 'dscr_ranges.id')
            ->join('ltv_ratios', 'dscr_ltv_adjustments.ltv_ratio_id', '=', 'ltv_ratios.id')
            ->join('loan_types', 'dscr_ltv_adjustments.loan_type_id', '=', 'loan_types.id')
            ->where('dscr_ranges.dscr_range', $dscrRange)
            ->where('ltv_ratios.ratio_range', $ltvColumn)
            ->where('loan_types.loan_program', $program)
            ->update(['dscr_ltv_adjustments.adjustment_pct' => $value]);
    }

    /**
     * Update Pre Pay LTV adjustment
     */
    private function updatePrePayLtvAdjustment($prepayPeriod, $ltvColumn, $value, $program)
    {
        return DB::table('pre_pay_ltv_adjustments')
            ->join('prepay_periods', 'pre_pay_ltv_adjustments.pre_pay_id', '=', 'prepay_periods.id')
            ->join('ltv_ratios', 'pre_pay_ltv_adjustments.ltv_ratio_id', '=', 'ltv_ratios.id')
            ->join('loan_types', 'pre_pay_ltv_adjustments.loan_type_id', '=', 'loan_types.id')
            ->where('prepay_periods.prepay_name', $prepayPeriod)
            ->where('ltv_ratios.ratio_range', $ltvColumn)
            ->where('loan_types.loan_program', $program)
            ->update(['pre_pay_ltv_adjustments.adjustment_pct' => $value]);
    }

    /**
     * Update Loan Type DSCR LTV adjustment
     */
    private function updateLoanTypeDscrLtvAdjustment($loanTypeDscr, $ltvColumn, $value, $program)
    {
        return DB::table('loan_type_dscr_ltv_adjustments')
            ->join('loan_types_dscrs', 'loan_type_dscr_ltv_adjustments.dscr_loan_type_id', '=', 'loan_types_dscrs.id')
            ->join('ltv_ratios', 'loan_type_dscr_ltv_adjustments.ltv_ratio_id', '=', 'ltv_ratios.id')
            ->join('loan_types', 'loan_type_dscr_ltv_adjustments.loan_type_id', '=', 'loan_types.id')
            ->where('loan_types_dscrs.loan_type_dscr_name', $loanTypeDscr)
            ->where('ltv_ratios.ratio_range', $ltvColumn)
            ->where('loan_types.loan_program', $program)
            ->update(['loan_type_dscr_ltv_adjustments.adjustment_pct' => $value]);
    }
}
