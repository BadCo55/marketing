<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    /** @use HasFactory<\Database\Factories\TaskFactory> */
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'assigned_to',
        'due_date',
        'status',
        'created_by',
        'priority',
    ];

    protected $appends = ['formatted_description', 'formatted_status', 'formatted_due_date', 'formatted_created_at', 'is_passed_due', 'is_due_today', 'has_due_date'];

    public function getFormattedDescriptionAttribute()
    {
        if ($this->description) {
            return strlen($this->description) > 50
                ? substr($this->description, 0, 50) . '...'
                : $this->description;
        } else {
            return 'N/A';
        }
    }

    public function getFormattedStatusAttribute()
    {
        if ($this->status) {
            return ucfirst($this->snakeToNormal($this->status));
        }
    }

    public function getFormattedDueDateAttribute()
    {
        if ($this->due_date) {
            return Carbon::parse($this->due_date)->format('m/d/y');
        }
    }

    public function getFormattedCreatedAtAttribute()
    {
        return Carbon::parse($this->created_at)
            ->timezone('America/New_York')
            ->format('m/d/y g:i a');
    }

    public function getIsPassedDueAttribute()
    {
        if ($this->due_date) {
            return Carbon::now()->startOfDay()->isAfter(Carbon::parse($this->due_date)->startOfDay());
        }
        return false; // Return false if no due date is set
    }
    
    public function getIsDueTodayAttribute()
    {
        if ($this->due_date) {
            return Carbon::now()->startOfDay()->isSameDay(Carbon::parse($this->due_date)->startOfDay());
        }
        return false; // Return false if no due date is set
    }

    public function getHasDueDateAttribute()
    {
        if ($this->due_date) {
            return true; 
        } else {
            return false; 
        }
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function notes()
    {
        return $this->hasMany(TaskNote::class, 'task_id');
    }

    public function taskable()
    {
        return $this->morphTo();
    }

    private function snakeToNormal($string)
    {
        // Replace underscores with spaces and capitalize words
        return ucwords(str_replace('_', ' ', $string));
    }
}
