<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RehabLevel extends Model
{
    /** @use HasFactory<\Database\Factories\RehabLevelFactory> */
    use HasFactory;
    protected $fillable = ['name'];

    public function rehabLimits()
    {
        return $this->hasMany(RehabLimit::class);
    }
}
