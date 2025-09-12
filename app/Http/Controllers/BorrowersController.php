<?php

namespace App\Http\Controllers;

use App\Models\Borrower;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class BorrowersController extends Controller
{
    /**
     * Display a listing of borrowers with filters similar to loan programs
     */
    public function index(Request $request)
    {
        try {
            Log::info('BorrowersController index() called', [
                'request_data' => $request->all()
            ]);

            // Build query using Spatie QueryBuilder for filtering
            $borrowers = QueryBuilder::for(Borrower::class)
                ->allowedFilters([
                    AllowedFilter::partial('name', 'first_name'),
                    AllowedFilter::partial('last_name'),
                    AllowedFilter::partial('email'),
                    AllowedFilter::exact('status'),
                    AllowedFilter::exact('property_state'),
                    AllowedFilter::exact('property_type'),
                    AllowedFilter::exact('employment_status'),
                    AllowedFilter::exact('loan_purpose'),
                    AllowedFilter::callback('credit_score_min', function ($query, $value) {
                        $query->where('credit_score', '>=', $value);
                    }),
                    AllowedFilter::callback('credit_score_max', function ($query, $value) {
                        $query->where('credit_score', '<=', $value);
                    }),
                    AllowedFilter::callback('loan_amount_min', function ($query, $value) {
                        $query->where('loan_amount_requested', '>=', $value);
                    }),
                    AllowedFilter::callback('loan_amount_max', function ($query, $value) {
                        $query->where('loan_amount_requested', '<=', $value);
                    }),
                    AllowedFilter::callback('experience_min', function ($query, $value) {
                        $query->where('years_of_experience', '>=', $value);
                    }),
                    AllowedFilter::callback('experience_max', function ($query, $value) {
                        $query->where('years_of_experience', '<=', $value);
                    }),
                ])
                ->allowedSorts(['first_name', 'last_name', 'email', 'credit_score', 'loan_amount_requested', 'created_at'])
                ->defaultSort('-created_at')
                ->paginate(50);

            // Get filter options for dropdowns
            $filterOptions = $this->getFilterOptions();

            // Statistics for display
            $stats = [
                'total_borrowers' => Borrower::count(),
                'active_borrowers' => Borrower::where('status', 'active')->count(),
                'average_credit_score' => Borrower::whereNotNull('credit_score')->avg('credit_score'),
                'total_loan_requests' => Borrower::whereNotNull('loan_amount_requested')->sum('loan_amount_requested'),
            ];

            Log::info('Borrowers retrieved successfully', [
                'count' => $borrowers->total(),
                'stats' => $stats
            ]);

            return view('borrowers.index', compact('borrowers', 'filterOptions', 'stats'));

        } catch (\Exception $e) {
            Log::error('Error in BorrowersController index(): ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'Unable to load borrowers data: ' . $e->getMessage());
        }
    }

    /**
     * Get filter options for dropdown menus
     */
    private function getFilterOptions()
    {
        return [
            'statuses' => [
                'active' => 'Active',
                'inactive' => 'Inactive',
                'pending' => 'Pending'
            ],
            'employment_statuses' => Borrower::whereNotNull('employment_status')
                ->distinct()
                ->pluck('employment_status')
                ->sort()
                ->mapWithKeys(fn($item) => [$item => $item])
                ->toArray(),
            'property_states' => Borrower::whereNotNull('property_state')
                ->distinct()
                ->pluck('property_state')
                ->sort()
                ->mapWithKeys(fn($item) => [$item => $item])
                ->toArray(),
            'property_types' => Borrower::whereNotNull('property_type')
                ->distinct()
                ->pluck('property_type')
                ->sort()
                ->mapWithKeys(fn($item) => [$item => $item])
                ->toArray(),
            'loan_purposes' => Borrower::whereNotNull('loan_purpose')
                ->distinct()
                ->pluck('loan_purpose')
                ->sort()
                ->mapWithKeys(fn($item) => [$item => $item])
                ->toArray(),
        ];
    }

    /**
     * Show the form for creating a new borrower.
     */
    public function create()
    {
        // Implementation for create form
        return view('borrowers.create');
    }

    /**
     * Store a newly created borrower in storage.
     */
    public function store(Request $request)
    {
        // Implementation for storing new borrower
        // This will be implemented later when we add create functionality
    }

    /**
     * Display the specified borrower.
     */
    public function show(Borrower $borrower)
    {
        // Implementation for showing single borrower
        return view('borrowers.show', compact('borrower'));
    }

    /**
     * Show the form for editing the specified borrower.
     */
    public function edit(Borrower $borrower)
    {
        // Implementation for edit form
        return view('borrowers.edit', compact('borrower'));
    }

    /**
     * Update the specified borrower in storage.
     */
    public function update(Request $request, Borrower $borrower)
    {
        // Implementation for updating borrower
        // This will be implemented later when we add edit functionality
    }

    /**
     * Remove the specified borrower from storage.
     */
    public function destroy(Borrower $borrower)
    {
        // Implementation for deleting borrower
        // This will be implemented later when we add delete functionality
    }
}