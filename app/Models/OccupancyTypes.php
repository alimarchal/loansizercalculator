<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OccupancyTypes extends Model
{
    /** @use HasFactory<\Database\Factories\OccupancyTypesFactory> */
    use HasFactory;

    protected $fillable = [
        'name',
        'display_order'
    ];

    public function dscrRateMatrices()
    {
        return $this->hasMany(DscrRateMatrix::class);
    }
}
