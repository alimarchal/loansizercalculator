<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoanMatrixApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// API endpoint for loan matrix calculations
Route::get('/loan-matrix', [LoanMatrixApiController::class, 'getLoanMatrix']);

// API endpoint for DSCR loan matrix data (all three programs)
Route::get('/loan-matrix-dscr', [LoanMatrixApiController::class, 'getLoanMatrixDscr']);

// API endpoint for getting loan type options (property types and states)
Route::get('/loan-type-options', [LoanMatrixApiController::class, 'getLoanTypeOptions']);
