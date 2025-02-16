<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OfficeNote extends Model
{
    /** @use HasFactory<\Database\Factories\OfficeNoteFactory> */
    use HasFactory;

    protected $fillable = [
        'office_id',
        'created_by',
        'note',
        'is_pinned',
        'priority',
        'assigned_to',
        'requires_follow_up',
    ];

    public function getPriorityAttribute($value)
    {
        return ucfirst($value);
    }

    public function getFormattedCreatedAtAttribute()
    {
        return Carbon::parse($this->created_at)
            ->timezone('America/New_York') // Convert to EST
            ->format('m/d/y g:i A'); // Format the date (e.g., "01/19/25 2:45 PM")
    }

    public function getFormattedIsPinnedAttribute()
    {
        return $this->attributes['is_pinned'] ? 'Yes' : 'No';
    }

    public function getFormattedRequiresFollowUpAttribute()
    {
        return $this->attributes['requires_follow_up'] ? 'Yes' : 'No';
    }

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }
}
