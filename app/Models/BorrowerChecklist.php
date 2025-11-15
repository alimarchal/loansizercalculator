<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class BorrowerChecklist extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'borrower_id',
        'checklist_id',
        'checklist_item_name',
        'status',
        'file_path',
        'uploaded_at',
        'assigned_by',
        'status_updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'uploaded_at' => 'datetime',
    ];

    /**
     * Get the borrower that owns this checklist item.
     */
    public function borrower()
    {
        return $this->belongsTo(Borrower::class);
    }

    /**
     * Get the checklist that this item belongs to.
     */
    public function checklist()
    {
        return $this->belongsTo(Checklist::class);
    }

    /**
     * Get the user who assigned this checklist.
     */
    public function assignedBy()
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    /**
     * Get the user who last updated the status.
     */
    public function statusUpdatedBy()
    {
        return $this->belongsTo(User::class, 'status_updated_by');
    }
}
