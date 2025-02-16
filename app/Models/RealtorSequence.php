<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RealtorSequence extends Model
{
    /** @use HasFactory<\Database\Factories\RealtorSequenceFactory> */
    use HasFactory;

    protected $fillable = [
        'realtor_id',
        'sequence_id',
        'current_step',
        'start_date',
        'next_step_due',
        'status',
    ];

    protected $appends = ['formatted_status', 'sequence_name', 'formatted_next_step_due', 'formatted_created_at', 'progress', 'is_passed_due', 'is_due_today', 'total_steps'];

    public function getFormattedStatusAttribute()
    {
        return $this->snakeToNormal($this->status);
    }

    public function getSequenceNameAttribute()
    {
        return $this->sequence ? $this->sequence->name : 'N/A';
    }

    public function getFormattedNextStepDueAttribute()
    {
        return Carbon::parse($this->next_step_due)
            // ->timezone('America/New_York')
            ->format('m/d/y');
    }

    public function getFormattedCreatedAtAttribute()
    {
        return Carbon::parse($this->created_at)
            ->timezone('America/New_York') // Convert to EST
            ->format('m/d/y'); // Format the date (e.g., "01/19/25 2:45 PM")
    }

    public function getProgressAttribute()
    {
        // Check if sequence exists
        if ($this->sequence) {
            $totalSteps = $this->sequence->steps->count();
    
            // Ensure totalSteps is not zero to avoid division errors
            return $totalSteps > 0 
                ? "{$this->current_step}/{$totalSteps}" 
                : "{$this->current_step}/0";
        }
    
        // Default to "N/A" if sequence or steps are missing
        return 'N/A';
    }


    public function getIsPassedDueAttribute()
    {
        if ($this->next_step_due) {
            // Compare the due date's start of the day with today's start of the day
            return Carbon::now()->startOfDay()->isAfter(Carbon::parse($this->next_step_due)->startOfDay());
        }
        return false; // Return false if no due date is set
    }

    public function getTotalStepsAttribute()
    {
        return $this->sequence->steps->count();
    }
    
    public function getIsDueTodayAttribute()
    {
        if ($this->next_step_due) {
            // Check if the due date is the same day as today
            return Carbon::now()->startOfDay()->isSameDay(Carbon::parse($this->next_step_due)->startOfDay());
        }
        return false; // Return false if no due date is set
    }

    public function sequence()
    {
        return $this->belongsTo(Sequence::class, 'sequence_id');
    }

    public function realtor()
    {
        return $this->belongsTo(Realtor::class, 'realtor_id');
    }

    public function currentStep()
    {
        return $this->hasOne(SequenceStep::class, 'step_number', 'current_step')
                    ->where('sequence_id', $this->sequence_id);
    }

    public function steps()
    {
        return $this->hasMany(SequenceStep::class, 'sequence_id');
    }

    private function snakeToNormal($string)
    {
        // Replace underscores with spaces and capitalize words
        return ucwords(str_replace('_', ' ', $string));
    }
}
