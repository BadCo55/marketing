<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OfficeVisit extends Model
{
    /** @use HasFactory<\Database\Factories\OfficeVisitFactory> */
    use HasFactory;

    protected $fillable = [
        'office_id',
        'user_id',
        'visit_date',
        'duration',
        'rating',
        'purpose',
        'initial_impressions',
        'interest_level',
        'follow_up_required',
        'materials_presented',
        'discussion_topics',
        'notes',
        'next_action',
        'next_action_date',
        'status',
    ];

    protected $casts = [
        'materials_presented' => 'array',
        'follow_up_required' => 'boolean',
    ];

    protected $appends = [
        'formatted_materials_presented',
        'formatted_visit_date',
        'formatted_duration',
        'formatted_purpose',
        'formatted_next_action_date',
        'formatted_follow_up_required',
    ];

    public function getFormattedMaterialsPresentedAttribute()
    {
        // Check if materials are available and apply formatting
        return array_map(function ($item) {
            return ucwords(str_replace('_', ' ', $item));
        }, $this->materials_presented ?? []);
    }

    public function getFormattedVisitDateAttribute()
    {
        if (!$this->visit_date) {
            return null;
        }
        return Carbon::parse($this->visit_date)->format('m/d/Y');
    }

    public function getFormattedRatingAttribute()
    {
        return "{$this->rating}/5";
    }

    public function getFormattedInterestLevelAttribute()
    {
        return "{$this->interest_level}/5";
    }

    public function getFormattedDurationAttribute()
    {
        return "{$this->duration} minutes";
    }

    public function getFormattedPurposeAttribute()
    {
        return ucfirst($this->snakeToNormal($this->purpose));
    }

    public function getFormattedNextActionDateAttribute()
    {
        if (!$this->next_action_date) {
            return 'N/A';
        }
        return Carbon::parse($this->next_action_date)->format('m/d/Y');
    }

    public function getFormattedFollowUpRequiredAttribute()
    {
        return $this->follow_up_required ? 'Yes' : 'No';
    }

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tasks()
    {
        return $this->morphMany(Task::class, 'taskable');
    }

    private function snakeToNormal($string)
    {
        // Replace underscores with spaces and capitalize words
        return ucwords(str_replace('_', ' ', $string));
    }
}
