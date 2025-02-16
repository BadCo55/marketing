<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class Realtor extends Model
{
    /** @use HasFactory<\Database\Factories\RealtorFactory> */
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'phone_number',
        'email',
        'office_id',
        'avatar',
        'owner',
        'acquired_via',
        'bni_member',
        'bni_chapter',
        'dob',
        'social_media_links',
        'specializations',
        'license_number',
        'years_in_business',
        'title',
        'primary_region',
        'preferred_inspection_company',
        'preferred_inspector',
        'previous_inspections'
    ];

    protected $appends = [
        'formatted_full_name',
        'formatted_title',
        'formatted_created_at',
        'status',
        'value',
        'formatted_value',
        'average_sale_value',
        'sale_count'
    ];

    protected $casts = [
        'bni_member' => 'boolean',
        'social_media_links' => 'array', // Ensures JSON data is auto-converted to array
        'specializations' => 'array',
        'primary_region' => 'array',
        'preferred_inspector' => 'array',
        'previous_inspections' => 'array',
    ];

    public function getStatusAttribute(): string
    {
        $createdAt = Carbon::parse($this->created_at);
        $latestDate = collect($this->previous_inspections)
            ->pluck('created_date')
            ->filter()
            ->max();

        // If there's an inspection within the last 30 days, realtor is "Active"
        if ($latestDate) {
            $latestInspectionDate = Carbon::parse($latestDate);
            if ($latestInspectionDate->greaterThanOrEqualTo(now()->subDays(30))) {
                return 'Active';
            }
        }

        // If created within the last 10 days and not active, return "New"
        if ($createdAt->greaterThanOrEqualTo(now()->subDays(10))) {
            return 'New';
        }

        // If the last inspection was within 90 days but not active, mark as "Engaged"
        if ($latestDate) {
            $daysSinceLastInspection = $latestInspectionDate->diffInDays(now());
            if ($daysSinceLastInspection <= 90) {
                return 'Engaged';
            }
        }

        // Default to "Inactive"
        return 'Inactive';
    }

    public function getValueAttribute()
    {
        return collect($this->previous_inspections)
            ->sum('total_fee'); // Sum total_fee directly
    }

    public function getFormattedValueAttribute()
    {
        $total = collect($this->previous_inspections)
            ->sum('total_fee');

        return '$' . number_format($total, 2);
    }

    public function getSaleCountAttribute()
    {
        $count = collect($this->previous_inspections)->count();
        return $count ?: 0;
    }

    public function getAverageSaleValueAttribute()
    {
        // Get total revenue
        $totalValue = collect($this->previous_inspections)->sum('total_fee');

        // Get total number of inspections
        $totalCount = collect($this->previous_inspections)->count();

        // Avoid division by zero
        return $totalCount > 0 ? number_format($totalValue / $totalCount, 2) : 0;
    }

    public function getAvatarUrlAttribute()
    {
        return $this->avatar ? Storage::url($this->avatar) : '/storage/avatars/default-avatar.png';
    }

    public function getFormattedFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function getFormattedCreatedAtAttribute()
    {
        return Carbon::parse($this->created_at)->format('m/d/y');
    }

    public function getFormattedTitleAttribute()
    {
        return snakeToNormal($this->title);
    }

    public function sequences()
    {
        return $this->hasMany(RealtorSequence::class, 'realtor_id');
    }

    public function activeSequences()
    {
        return $this->sequences()->whereNotIn('status', ['cancelled', 'completed']);
    }

    public function notes()
    {
        return $this->hasMany(RealtorNote::class, 'realtor_id');
    }

    public function communications()
    {
        return $this->hasMany(RealtorCommunicationLog::class, 'realtor_id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'owner');
    }

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // private function snakeToNormal($string)
    // {
    //     // Replace underscores with spaces and capitalize words
    //     return ucwords(str_replace('_', ' ', $string));
    // }
}
