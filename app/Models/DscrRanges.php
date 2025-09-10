<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DscrRanges extends Model
{
    /** @use HasFactory<\Database\Factories\DscrRangesFactory> */
    use HasFactory;

    protected $table = 'dscr_ranges';
    protected $fillable = ['dscr_range', 'min_dscr', 'max_dscr', 'display_order'];

    public function dscrLtvAdjustments()
    {
        return $this->hasMany(DscrLtvAdjustments::class, 'dscr_range_id');
    }
}
