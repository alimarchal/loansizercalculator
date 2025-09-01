<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoanProgramController;

Route::get('/', function () {
    return to_route('login');
});

Route::middleware(['auth:sanctum', config('jetstream.auth_session'), 'verified',])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Loan program restrictions API - must be before resource route
    Route::get('loan-programs/api/restrictions', [LoanProgramController::class, 'getLoanTypeRestrictions'])
        ->name('loan-programs.restrictions');

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
});
