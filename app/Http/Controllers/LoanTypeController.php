<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLoanTypeRequest;
use App\Http\Requests\UpdateLoanTypeRequest;
use App\Models\LoanType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LoanTypeController extends Controller
{
    /**
     * Display a listing of the resource - Settings loan types index page
     */
    public function index()
    {
        try {
            Log::info('LoanTypeController index() called');

            // Get all loan types ordered by name
            $loanTypes = LoanType::orderBy('id', 'asc')->get();

            Log::info('Loan Types retrieved', [
                'count' => $loanTypes->count(),
                'first_few' => $loanTypes->take(3)->toArray()
            ]);

            return view('settings.loan-types.index', compact('loanTypes'));

        } catch (\Exception $e) {
            Log::error('Error in LoanTypeController index(): ' . $e->getMessage());
            return back()->with('error', 'Unable to load loan types');
        }
    }

    /**
     * Update a loan type field via AJAX for inline editing
     */
    public function updateField(Request $request)
    {
        try {
            Log::info('LoanTypeController updateField() called', [
                'request_data' => $request->all()
            ]);

            $validator = Validator::make($request->all(), [
                'loan_type_id' => 'required|integer|exists:loan_types,id',
                'field' => 'required|string|in:underwritting_fee,legal_doc_prep_fee,loan_starting_rate',
                'value' => 'required|numeric|min:0'
            ]);

            if ($validator->fails()) {
                Log::warning('Validation failed', ['errors' => $validator->errors()]);
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed: ' . $validator->errors()->first()
                ], 400);
            }

            $loanType = LoanType::findOrFail($request->loan_type_id);
            $field = $request->field;
            $value = (float) $request->value;

            // Update the field
            $oldValue = $loanType->$field;
            $loanType->$field = $value;
            $loanType->save();
            Log::info('Loan Type updated successfully', [
                'loan_type_id' => $loanType->id,
                'field' => $field,
                'old_value' => $oldValue,
                'new_value' => $value
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Loan type updated successfully',
                'data' => [
                    'loan_type_id' => $loanType->id,
                    'field' => $field,
                    'value' => $value,
                    'formatted_value' => $this->formatValue($field, $value)
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Error updating loan type: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update loan type: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Format value for display based on field type
     */
    private function formatValue($field, $value)
    {
        switch ($field) {
            case 'underwritting_fee':
            case 'legal_doc_prep_fee':
                return '$' . number_format($value, 2);
            case 'loan_starting_rate':
                return number_format($value, 3) . '%';
            default:
                return $value;
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreLoanTypeRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(LoanType $loanType)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LoanType $loanType)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLoanTypeRequest $request, LoanType $loanType)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LoanType $loanType)
    {
        //
    }
}
