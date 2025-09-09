<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLoanAmountRequest;
use App\Http\Requests\UpdateLoanAmountRequest;
use App\Models\LoanAmount;

class LoanAmountController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(StoreLoanAmountRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(LoanAmount $loanAmount)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(LoanAmount $loanAmount)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLoanAmountRequest $request, LoanAmount $loanAmount)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(LoanAmount $loanAmount)
    {
        //
    }
}
