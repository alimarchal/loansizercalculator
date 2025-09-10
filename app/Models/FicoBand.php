<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FicoBand extends Model
{
    /** @use HasFactory<\Database\Factories\FicoBandFactory> */
    use HasFactory;
    protected $fillable = ['fico_range', 'fico_min', 'fico_max'];

    public function loanRules()
    {
        return $this->hasMany(LoanRule::class);
    }

    public function ficoLtvAdjustments()
    {
        return $this->hasMany(FicoLtvAdjustment::class);
    }
}
