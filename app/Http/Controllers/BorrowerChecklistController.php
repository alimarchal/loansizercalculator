<?php

namespace App\Http\Controllers;

use App\Models\Borrower;
use App\Models\Checklist;
use App\Models\BorrowerChecklist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use App\Helpers\FileStorageHelper;

/**
 * BorrowerChecklistController handles checklist assignments to borrowers
 * Manages status updates and file uploads
 */
class BorrowerChecklistController extends Controller
{
    /**
     * Assign checklist items to a borrower
     * 
     * @param Request $request
     * @param Borrower $borrower
     * @return \Illuminate\Http\RedirectResponse
     */
    public function assign(Request $request, Borrower $borrower)
    {
        $validated = $request->validate([
            'checklist_id' => 'required|exists:checklists,id',
            'checklist_items' => 'required|array|min:1',
            'checklist_items.*' => 'required|string',
        ]);

        DB::beginTransaction();

        try {
            $checklist = Checklist::findOrFail($validated['checklist_id']);

            foreach ($validated['checklist_items'] as $itemName) {
                // Check if this item is already assigned
                $existing = BorrowerChecklist::where('borrower_id', $borrower->id)
                    ->where('checklist_id', $checklist->id)
                    ->where('checklist_item_name', $itemName)
                    ->first();

                if (!$existing) {
                    BorrowerChecklist::create([
                        'borrower_id' => $borrower->id,
                        'checklist_id' => $checklist->id,
                        'checklist_item_name' => $itemName,
                        'status' => 'Document Pending',
                        'assigned_by' => Auth::id(),
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('borrowers.show', $borrower)
                ->with('success', 'Checklist items assigned successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error assigning checklist', [
                'borrower_id' => $borrower->id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to assign checklist. Please try again.');
        }
    }

    /**
     * Update status of a borrower checklist item
     * 
     * @param Request $request
     * @param BorrowerChecklist $borrowerChecklist
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, BorrowerChecklist $borrowerChecklist)
    {
        $validated = $request->validate([
            'status' => 'required|in:Document Pending,Document Clear',
        ]);

        DB::beginTransaction();

        try {
            $borrowerChecklist->update([
                'status' => $validated['status'],
                'status_updated_by' => Auth::id(),
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', 'Status updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating checklist status', [
                'borrower_checklist_id' => $borrowerChecklist->id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to update status. Please try again.');
        }
    }

    /**
     * Upload file for a borrower checklist item
     * 
     * @param Request $request
     * @param BorrowerChecklist $borrowerChecklist
     * @return \Illuminate\Http\RedirectResponse
     */
    public function uploadFile(Request $request, BorrowerChecklist $borrowerChecklist)
    {
        // Authorization: Borrowers can only upload to their own checklists, admins can upload to any
        $user = Auth::user();

        // If not a superadmin, check if user owns this borrower
        if (!$user->hasRole('superadmin')) {
            $borrowerIds = Borrower::where('user_id', $user->id)->pluck('id')->toArray();

            if (empty($borrowerIds) || !in_array($borrowerChecklist->borrower_id, $borrowerIds)) {
                abort(403, 'You can only upload files to your own checklist items.');
            }
        }

        $validated = $request->validate([
            'file' => 'required|file|mimes:pdf,jpg,jpeg,png,doc,docx|max:10240', // Max 10MB
        ]);

        DB::beginTransaction();
        try {
            // Delete old file if exists
            if ($borrowerChecklist->file_path) {
                Storage::disk('public')->delete($borrowerChecklist->file_path);
            }

            // Store new file
            $folderName = 'BorrowerChecklists/' . $borrowerChecklist->borrower_id;
            $filePath = FileStorageHelper::storeSingleFile(
                $request->file('file'),
                $folderName,
                $borrowerChecklist->checklist_item_name
            );

            $borrowerChecklist->update([
                'file_path' => $filePath,
                'uploaded_at' => now(),
            ]);

            DB::commit();

            return redirect()->back()
                ->with('success', 'File uploaded successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error uploading checklist file', [
                'borrower_checklist_id' => $borrowerChecklist->id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to upload file. Please try again.');
        }
    }

    /**
     * Download file for a borrower checklist item
     * 
     * @param BorrowerChecklist $borrowerChecklist
     */
    public function downloadFile(BorrowerChecklist $borrowerChecklist)
    {
        // Authorization: Borrowers can only download their own files, admins can download any
        $user = Auth::user();

        // If not a superadmin, check if user owns this borrower
        if (!$user->hasRole('superadmin')) {
            $borrowerIds = Borrower::where('user_id', $user->id)->pluck('id')->toArray();

            if (empty($borrowerIds) || !in_array($borrowerChecklist->borrower_id, $borrowerIds)) {
                abort(403, 'You can only download your own files.');
            }
        }

        if (!$borrowerChecklist->file_path || !Storage::disk('public')->exists($borrowerChecklist->file_path)) {
            return redirect()->back()
                ->with('error', 'File not found.');
        }
        return response()->download(
            Storage::disk('public')->path($borrowerChecklist->file_path),
            basename($borrowerChecklist->file_path)
        );
    }

    /**
     * Remove a borrower checklist item
     * 
     * @param BorrowerChecklist $borrowerChecklist
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(BorrowerChecklist $borrowerChecklist)
    {
        DB::beginTransaction();

        try {
            // Delete associated file if exists
            if ($borrowerChecklist->file_path) {
                Storage::disk('public')->delete($borrowerChecklist->file_path);
            }

            $borrowerChecklist->delete();

            DB::commit();

            return redirect()->back()
                ->with('success', 'Checklist item removed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting borrower checklist', [
                'borrower_checklist_id' => $borrowerChecklist->id,
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return redirect()->back()
                ->with('error', 'Failed to remove checklist item. Please try again.');
        }
    }
}
