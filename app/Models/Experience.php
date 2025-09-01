<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Experience extends Model
{
    /** @use HasFactory<\Database\Factories\ExperienceFactory> */
    use HasFactory;
    protected $fillable = ['experiences_range', 'min_experience', 'max_experience'];

    public function loanRules()
    {
        return $this->hasMany(LoanRule::class);
    }
}
