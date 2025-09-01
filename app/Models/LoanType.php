<?php

namespace App\Models;

use App\Traits\UserTracking;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LoanType extends Model
{
    /** @use HasFactory<\Database\Factories\LoanTypeFactory> */
    use HasFactory, UserTracking;
}
