<?php

namespace App\Models;

use App\Traits\UserTracking;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class State extends Model
{
    /** @use HasFactory<\Database\Factories\StateFactory> */
    use HasFactory, UserTracking, SoftDeletes;

    protected $fillable = [
        'code',
        'is_allowed',
    ];

    protected $casts = [
        'is_allowed' => 'boolean',
    ];
}
