<?php

namespace App\Http\Controllers;

use App\Models\LoanType;
use App\Models\TransactionType;
use App\Models\PropertyType;
use App\Models\State;
use Illuminate\Http\Request;

class LoanCalculatorController extends Controller
{
    /**
     * Display the loan calculator form
     */
    public function index()
    {
        // Get all dropdown options from database
        $loanTypes = LoanType::select('id', 'name', 'loan_program')->get();
        $transactionTypes = TransactionType::select('id', 'name')->get();
        $propertyTypes = PropertyType::select('id', 'name')->get();
        $states = State::select('id', 'code')->orderBy('code')->get();

        return view('loan-calculator', compact(
            'loanTypes',
            'transactionTypes',
            'propertyTypes',
            'states'
        ));
    }
}
