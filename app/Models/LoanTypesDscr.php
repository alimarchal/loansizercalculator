<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoanTypesDscr extends Model
{
    /** @use HasFactory<\Database\Factories\LoanTypesDscrFactory> */
    use HasFactory;

    protected $fillable = [
        'loan_type_dscr_name',
        'display_order'
    ];

    public function dscrRateMatrices()
    {
        return $this->hasMany(DscrRateMatrix::class);
    }

    public function loanTypeDscrLtvAdjustments()
    {
        return $this->hasMany(LoanTypeDscrLtvAdjustments::class, 'dscr_loan_type_id');
    }
}
