<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RealtorCommunicationLog extends Model
{
    /** @use HasFactory<\Database\Factories\RealtorCommunicationLogFactory> */
    use HasFactory;

    protected $fillable = [
        'realtor_id',
        'user_id',
        'communication_method',
        'summary',
        'details',
    ];

    protected $appends = ['formatted_created_at', 'formatted_communication_method'];

    public function realtor()
    {
        return $this->belongsTo(Realtor::class, 'realtor_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Get the formatted creation date.
     */
    public function getFormattedCreatedAtAttribute()
    {
        return Carbon::parse($this->created_at)->timezone('America/New_York')->format('m/d/y g:i a'); // Example: January 25, 2025 3:45 PM
    }

    public function getFormattedCommunicationMethodAttribute()
    {
        return $this->snakeToNormal($this->communication_method);
    }

    private function snakeToNormal($string)
    {
        // Replace underscores with spaces and capitalize words
        return ucwords(str_replace('_', ' ', $string));
    }
}
