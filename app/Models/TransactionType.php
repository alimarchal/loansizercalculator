<?php

namespace App\Models;

use App\Traits\UserTracking;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TransactionType extends Model
{
    /** @use HasFactory<\Database\Factories\TransactionTypeFactory> */
    use HasFactory, UserTracking;
}
