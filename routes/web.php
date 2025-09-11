<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoanProgramController;
use App\Http\Controllers\LoanCalculatorController;

Route::get('/', function () {
    return to_route('login');
});

// Public loan calculator route (no authentication required)
Route::get('/loan-calculator', [LoanCalculatorController::class, 'index'])->name('loan-calculator');

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Loan program restrictions API - must be before resource route
    Route::get('loan-programs/api/restrictions', [LoanProgramController::class, 'getLoanTypeRestrictions'])
        ->name('loan-programs.restrictions');

    // DSCR Matrix update API
    Route::post('loan-programs/api/dscr-matrix/update', [LoanProgramController::class, 'updateDscrMatrixCell'])
        ->name('loan-programs.dscr-matrix.update');

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
    ;
});
