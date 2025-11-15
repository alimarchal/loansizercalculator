<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Checklist extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'loan_types',
        'checklist_items',
        'is_active',
        'created_by',
        'updated_by',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'loan_types' => 'array',
        'checklist_items' => 'array',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user who created the checklist.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the user who last updated the checklist.
     */
    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    /**
     * Get all borrower checklists associated with this checklist.
     */
    public function borrowerChecklists()
    {
        return $this->hasMany(BorrowerChecklist::class);
    }

    /**
     * Get all borrowers through borrower checklists.
     */
    public function borrowers()
    {
        return $this->belongsToMany(Borrower::class, 'borrower_checklists')
            ->withPivot('checklist_item_name', 'status', 'file_path', 'uploaded_at', 'assigned_by', 'status_updated_by')
            ->withTimestamps();
    }
}
