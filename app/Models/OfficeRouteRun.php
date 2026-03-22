<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OfficeRouteRun extends Model
{
    use HasFactory;
    use SoftDeletes;

    // CHANGED: Added fillable fields matching the rewritten office_route_runs migration.
    protected $fillable = [
        'office_route_id',
        'assigned_to',
        'started_by',
        'completed_by',
        'status',
        'run_date',
        'current_stop_order',
        'planned_stop_count',
        'completed_stop_count',
        'skipped_stop_count',
        'cancelled_stop_count',
        'started_at',
        'completed_at',
        'cancelled_at',
        'start_latitude',
        'start_longitude',
        'end_latitude',
        'end_longitude',
        'actual_total_drive_minutes',
        'actual_total_duration_minutes',
        'actual_total_drive_miles',
        'notes',
        'created_by',
        'updated_by',
    ];

    // CHANGED: Added casts for dates, timestamps, decimals, integers, and soft delete timestamp.
    protected $casts = [
        'run_date' => 'date',
        'current_stop_order' => 'integer',
        'planned_stop_count' => 'integer',
        'completed_stop_count' => 'integer',
        'skipped_stop_count' => 'integer',
        'cancelled_stop_count' => 'integer',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'start_latitude' => 'decimal:7',
        'start_longitude' => 'decimal:7',
        'end_latitude' => 'decimal:7',
        'end_longitude' => 'decimal:7',
        'actual_total_drive_minutes' => 'integer',
        'actual_total_duration_minutes' => 'integer',
        'actual_total_drive_miles' => 'decimal:2',
        'deleted_at' => 'datetime',
    ];

    // CHANGED: Added relationship back to the reusable office route.
    public function officeRoute(): BelongsTo
    {
        return $this->belongsTo(OfficeRoute::class);
    }

    // CHANGED: Added relationship to assigned user for the run.
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // CHANGED: Added relationship to the user who started the run.
    public function startedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'started_by');
    }

    // CHANGED: Added relationship to the user who completed the run.
    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
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

    // CHANGED: Added relationship to run stop records.
    public function stops(): HasMany
    {
        return $this->hasMany(OfficeRouteRunStop::class);
    }

    // CHANGED: Added ordered run stops helper.
    public function orderedStops(): HasMany
    {
        return $this->hasMany(OfficeRouteRunStop::class)->orderBy('actual_stop_order')->orderBy('planned_stop_order');
    }

    // CHANGED: Added status filtering scope.
    public function scopeStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    // CHANGED: Added assigned user filtering scope.
    public function scopeAssignedTo($query, int $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    // CHANGED: Added active run scope for convenience.
    public function scopeActive($query)
    {
        return $query->whereIn('status', ['not_started', 'in_progress', 'paused']);
    }
}
