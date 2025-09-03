<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoanMatrixApiController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// API endpoint for loan matrix calculations
Route::get('/loan-matrix', [LoanMatrixApiController::class, 'getLoanMatrix']);
