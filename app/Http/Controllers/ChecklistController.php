<?php

namespace App\Http\Controllers;

use App\Models\Checklist;
use App\Models\LoanType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

/**
 * ChecklistController handles CRUD operations for checklists
 * Only accessible to super admin users
 */
class ChecklistController extends Controller
{
    /**
     * Display a listing of checklists
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $checklists = Checklist::with(['creator', 'updater'])
            ->latest()
            ->paginate(10);

        return view('checklists.index', compact('checklists'));
    }

    /**
     * Show the form for creating a new checklist
     * 
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $loanTypes = LoanType::all();
        return view('checklists.create', compact('loanTypes'));
    }

    /**
     * Store a newly created checklist
     * 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'loan_types' => 'required|array|min:1',
            'loan_types.*' => 'exists:loan_types,id',
            'checklist_items' => 'required|array|min:1',
            'checklist_items.*' => 'required|string|max:500',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            $validated['created_by'] = Auth::id();
            $validated['updated_by'] = Auth::id();
            $validated['is_active'] = $request->has('is_active') ? true : false;

            $checklist = Checklist::create($validated);

            DB::commit();

            return redirect()
                ->route('checklists.index')
                ->with('success', "Checklist '{$checklist->name}' created successfully.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error creating checklist', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to create checklist. Please try again.');
        }
    }

    /**
     * Display the specified checklist
     * 
     * @param Checklist $checklist
     * @return \Illuminate\View\View
     */
    public function show(Checklist $checklist)
    {
        $checklist->load(['creator', 'updater']);
        return view('checklists.show', compact('checklist'));
    }

    /**
     * Show the form for editing the specified checklist
     * 
     * @param Checklist $checklist
     * @return \Illuminate\View\View
     */
    public function edit(Checklist $checklist)
    {
        $loanTypes = LoanType::all();
        return view('checklists.edit', compact('checklist', 'loanTypes'));
    }

    /**
     * Update the specified checklist
     * 
     * @param Request $request
     * @param Checklist $checklist
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Checklist $checklist)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'loan_types' => 'required|array|min:1',
            'loan_types.*' => 'exists:loan_types,id',
            'checklist_items' => 'required|array|min:1',
            'checklist_items.*' => 'required|string|max:500',
            'is_active' => 'boolean',
        ]);

        DB::beginTransaction();

        try {
            $validated['updated_by'] = Auth::id();
            $validated['is_active'] = $request->has('is_active') ? true : false;

            $checklist->update($validated);

            DB::commit();

            return redirect()
                ->route('checklists.index')
                ->with('success', "Checklist '{$checklist->name}' updated successfully.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating checklist', [
                'checklist_id' => $checklist->id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return redirect()->back()
                ->withInput()
                ->with('error', 'Failed to update checklist. Please try again.');
        }
    }

    /**
     * Remove the specified checklist
     * 
     * @param Checklist $checklist
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Checklist $checklist)
    {
        DB::beginTransaction();

        try {
            $name = $checklist->name;
            $checklist->delete();

            DB::commit();

            return redirect()
                ->route('checklists.index')
                ->with('success', "Checklist '{$name}' deleted successfully.");

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting checklist', [
                'checklist_id' => $checklist->id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to delete checklist. Please try again.');
        }
    }
}
