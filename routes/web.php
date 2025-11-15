<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoanProgramController;
use App\Http\Controllers\LoanCalculatorController;
use App\Http\Controllers\LoanTypeController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\BorrowersController;

Route::get('/', function () {
    return to_route('login');
});

// Public loan calculator route (no authentication required)
Route::get('/loan-calculator', [LoanCalculatorController::class, 'index'])->name('loan-calculator');

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Routes accessible to both borrowers and superadmins
    Route::resource('borrowers', BorrowersController::class);

    // Routes accessible only to superadmins
    Route::middleware(['role:superadmin'])->group(function () {
        // Loan program restrictions API - must be before resource route
        Route::get('loan-programs/api/restrictions', [LoanProgramController::class, 'getLoanTypeRestrictions'])
            ->name('loan-programs.restrictions');

        // DSCR Matrix update API
        Route::post('loan-programs/api/dscr-matrix/update', [LoanProgramController::class, 'updateDscrMatrixCell'])
            ->name('loan-programs.dscr-matrix.update');

        // Settings Routes
        Route::get('settings', [SettingsController::class, 'index'])
            ->name('settings.index');

        // Loan Types Settings Routes
        Route::get('settings/loan-types', [LoanTypeController::class, 'index'])
            ->name('settings.loan-types.index');
        Route::post('settings/loan-types/api/update', [LoanTypeController::class, 'updateField'])
            ->name('settings.loan-types.api.update');

        // Loan Program Matrix Routes
        Route::resource('loan-programs', LoanProgramController::class)->names([
            'index' => 'loan-programs.index',
            'create' => 'loan-programs.create',
            'store' => 'loan-programs.store',
            'show' => 'loan-programs.show',
            'edit' => 'loan-programs.edit',
            'update' => 'loan-programs.update',
            'destroy' => 'loan-programs.destroy',
        ]);

        // Checklist CRUD Routes
        Route::resource('checklists', \App\Http\Controllers\ChecklistController::class);

        // Borrower Checklist Management Routes (Admin Only)
        Route::post('borrowers/{borrower}/checklists/assign', [\App\Http\Controllers\BorrowerChecklistController::class, 'assign'])
            ->name('borrowers.checklists.assign');
        Route::patch('borrower-checklists/{borrowerChecklist}/status', [\App\Http\Controllers\BorrowerChecklistController::class, 'updateStatus'])
            ->name('borrower-checklists.update-status');
        Route::delete('borrower-checklists/{borrowerChecklist}', [\App\Http\Controllers\BorrowerChecklistController::class, 'destroy'])
            ->name('borrower-checklists.destroy');
    });

    // Borrower Checklist Routes (Accessible to both Admin and Borrower)
    Route::post('borrower-checklists/{borrowerChecklist}/upload', [\App\Http\Controllers\BorrowerChecklistController::class, 'uploadFile'])
        ->name('borrower-checklists.upload');
    Route::get('borrower-checklists/{borrowerChecklist}/download', [\App\Http\Controllers\BorrowerChecklistController::class, 'downloadFile'])
        ->name('borrower-checklists.download');
});
