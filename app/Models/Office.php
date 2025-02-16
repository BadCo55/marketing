<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Office extends Model
{
    use HasFactory;

    protected $fillable = [
        'place_id',
        'parent_company',
        'office_name',
        'office_phone',
        'street_address',
        'unit_number',
        'city',
        'state',
        'county',
        'zip_code',
        'latitude',
        'longitude',
        'image',
    ];

    protected $appends = [
        'realtor_count',
        'image_url',
        'formatted_company_name',
        'total_inspections_scheduled',
        'total_revenue_received',
        'previous_inspections',
        'latest_inspection_date',
        'active_agents_count',
        'inactive_agents_count',
        'highest_value_agent',
        'engaged_agents_count',
        'status'
    ];


    /**
     * Get the total number of inspections scheduled across all realtors in the office.
     */
    public function getTotalInspectionsScheduledAttribute()
    {
        return collect($this->previous_inspections)->count() ?? 0;
    }

    /**
     * Get the total revenue received from all inspections within the office.
     */
    public function getTotalRevenueReceivedAttribute()
    {
        $total = collect($this->previous_inspections)->sum('total_fee') ?? 0;
        return '$' . number_format($total, 2);
    }

    public function getActiveAgentsCountAttribute()
    {
        return $this->realtors ? $this->realtors->where('status', 'Active')->count() : 0;
    }

    public function getInactiveAgentsCountAttribute()
    {
        return $this->realtors ? $this->realtors->where('status', 'Inactive')->count() : 0;
    }

    public function getEngagedAgentsCountAttribute()
    {
        return $this->realtors ? $this->realtors->where('status', 'Engaged')->count() : 0;
    }

    public function getStatusAttribute()
    {
        if ($this->active_agents_count > 0) {
            return 'Active';
        } else if ($this->engaged_agents_count > 0){
            return 'Engaged';
        } else {
            return 'Inactive';
        }
    }

    /**
     * Get all previous inspections from the realtors in this office.
     */
    public function getPreviousInspectionsAttribute()
    {
        return $this->realtors ? $this->realtors->flatMap(function ($realtor) {
            return is_array($realtor->previous_inspections) ? $realtor->previous_inspections : [];
        })->toArray() : [];
    }

    /**`
     * Get the highest value agent in the office (agent with the most revenue generated).
     */
    public function getHighestValueAgentAttribute()
    {
        return $this->realtors && $this->realtors->count() > 0
            ? $this->realtors->sortByDesc(fn ($realtor) => (float) preg_replace('/[^0-9.]/', '', $realtor->value ?? '0'))->first()
            : null;
    }

    /**
     * Get the latest inspection date from the previous inspections.
     */
    public function getLatestInspectionDateAttribute()
    {
        $latestDate = collect($this->previous_inspections)
            ->pluck('created_date')
            ->filter()
            ->max();

        return $latestDate ? Carbon::parse($latestDate)->format('m/d/Y g:i A') : null;
    }

    public function getFormattedCompanyNameAttribute()
    {
        $string = $this->parent_company;
        if ($this->office_name) {
            $string .= ' (' . $this->office_name . ')';
        }
        $string .= ' - ' . $this->street_address;
        if ($this->unit_number) {
            $string .= ' #' . $this->unit_number;
        }
        $string .= ' [' . $this->city . ']';
        return $string;
    }

    public function getImageUrlAttribute()
    {
        return $this->image ? Storage::url($this->image) : '/storage/avatars/default-company.png';
    }

    public function getRealtorCountAttribute()
    {
        return $this->realtors ? $this->realtors->count() : 0;
    }

    public function realtors()
    {
        return $this->hasMany(Realtor::class, 'office_id');
    }

    public function notes()
    {
        return $this->hasMany(OfficeNote::class, 'office_id');
    }

    public function visits()
    {
        return $this->hasMany(OfficeVisit::class, 'office_id');
    }

}
