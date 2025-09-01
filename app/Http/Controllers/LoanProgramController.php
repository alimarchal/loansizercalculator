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
     * Display the complete loan matrix with filters
     * 
     * @param Request $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        try {
            // Build query using Laravel Query Builder with Spatie QueryBuilder for filters
            $matrixQuery = QueryBuilder::for(LoanRule::class)
                ->allowedFilters([
                    AllowedFilter::callback('loan_type_id', function ($query, $value) {
                        $query->whereHas('experience.loanType', function ($q) use ($value) {
                            $q->where('id', $value);
                        });
                    }),
                    AllowedFilter::exact('experience_id'),
                    AllowedFilter::exact('fico_band_id'),
                    AllowedFilter::exact('transaction_type_id'),
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

            // Group data by loan type and add rowspan information
            $groupedData = $matrixData->groupBy('loan_type');
            $processedData = [];

            foreach ($groupedData as $loanType => $rows) {
                $processedData[$loanType] = $rows;
            }

            $matrixData = $processedData;            // Get data for filter dropdowns
            $loanTypes = LoanType::orderBy('name')->get(['id', 'name']);
            $ficoBands = FicoBand::orderBy('fico_min')->get(['id', 'fico_range']);
            $transactionTypes = TransactionType::orderBy('name')->get(['id', 'name']);

            return view('loan-programs.index', compact('matrixData', 'loanTypes', 'ficoBands', 'transactionTypes'));

        } catch (\Exception $e) {
            // If there's an error, return to dashboard with error message
            return redirect()->route('dashboard')
                ->with('error', 'Failed to load loan matrix data. Please try again. Error: ' . $e->getMessage());
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
}
