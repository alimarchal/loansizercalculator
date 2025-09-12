<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SettingsController extends Controller
{
    /**
     * Display the main settings page with cards for different settings sections
     */
    public function index()
    {
        try {
            Log::info('SettingsController index() called');

            // You can add any data gathering here for the settings overview
            $settingsData = [
                'loan_types_count' => \App\Models\LoanType::count(),
                // Add more counts or statistics as needed
            ];

            return view('settings.index', compact('settingsData'));

        } catch (\Exception $e) {
            Log::error('Error in SettingsController index(): ' . $e->getMessage());
            return back()->with('error', 'Unable to load settings page');
        }
    }
}