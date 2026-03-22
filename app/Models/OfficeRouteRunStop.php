<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OfficeRouteRunStop extends Model
{
    use HasFactory;
    use SoftDeletes;

    // CHANGED: Added fillable fields matching the office_route_run_stops migration.
    protected $fillable = [
        'office_route_run_id',
        'office_route_stop_id',
        'office_id',
        'planned_stop_order',
        'actual_stop_order',
        'status',
        'parent_company',
        'office_name',
        'street_address',
        'unit_number',
        'city',
        'state',
        'zip_code',
        'latitude',
        'longitude',
        'office_phone',
        'priority',
        'required',
        'estimated_drive_minutes_from_previous',
        'estimated_drive_miles_from_previous',
        'actual_drive_minutes_from_previous',
        'actual_drive_miles_from_previous',
        'started_at',
        'arrived_at',
        'completed_at',
        'skipped_at',
        'cancelled_at',
        'duration_minutes',
        'office_visit_id',
        'outcome',
        'notes',
        'skip_reason',
        'cancel_reason',
        'created_by',
        'updated_by',
    ];

    // CHANGED: Added casts for proper type handling.
    protected $casts = [
        'planned_stop_order' => 'integer',
        'actual_stop_order' => 'integer',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'priority' => 'integer',
        'required' => 'boolean',
        'estimated_drive_minutes_from_previous' => 'integer',
        'estimated_drive_miles_from_previous' => 'decimal:2',
        'actual_drive_minutes_from_previous' => 'integer',
        'actual_drive_miles_from_previous' => 'decimal:2',
        'started_at' => 'datetime',
        'arrived_at' => 'datetime',
        'completed_at' => 'datetime',
        'skipped_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'duration_minutes' => 'integer',
        'deleted_at' => 'datetime',
    ];

    // CHANGED: Added relationship back to the run.
    public function officeRouteRun(): BelongsTo
    {
        return $this->belongsTo(OfficeRouteRun::class);
    }

    // CHANGED: Added relationship back to the reusable planned stop.
    public function officeRouteStop(): BelongsTo
    {
        return $this->belongsTo(OfficeRouteStop::class);
    }

    // CHANGED: Added relationship to the office.
    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    // CHANGED: Added relationship to linked visit created from this run stop.
    public function officeVisit(): BelongsTo
    {
        return $this->belongsTo(OfficeVisit::class);
    }

    // CHANGED: Added audit relationship to creator.
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // CHANGED: Added audit relationship to updater.
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // CHANGED: Added simple status scope.
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    // CHANGED: Added actual-order scope for playback.
    public function scopeActualOrdered($query)
    {
        return $query->orderBy('actual_stop_order')->orderBy('planned_stop_order');
    }

    // CHANGED: Added planned-order fallback scope.
    public function scopePlannedOrdered($query)
    {
        return $query->orderBy('planned_stop_order');
    }
}
