<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RehabLimit extends Model
{
    /** @use HasFactory<\Database\Factories\RehabLimitFactory> */
    use HasFactory;
    protected $fillable = ['loan_rule_id', 'rehab_level_id', 'max_ltc', 'max_ltv', 'max_ltfc'];

    public function loanRule()
    {
        return $this->belongsTo(LoanRule::class);
    }

    public function rehabLevel()
    {
        return $this->belongsTo(RehabLevel::class);
    }
}
