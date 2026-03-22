<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OfficeRoute extends Model
{
    use SoftDeletes;

    // CHANGED: Added fillable fields matching the rewritten office_routes migration.
    protected $fillable = [
        'name',
        'description',
        'status',
        'created_by',
        'assigned_to',
        'route_date',
        'center_latitude',
        'center_longitude',
        'radius_miles',
        'total_stops',
        'estimated_total_drive_minutes',
        'estimated_total_drive_miles',
        'source_type',
        'import_file_name',
        'notes',
    ];

    // CHANGED: Added casts for dates, decimals, integers, and soft delete timestamp.
    protected $casts = [
        'route_date' => 'date',
        'center_latitude' => 'decimal:7',
        'center_longitude' => 'decimal:7',
        'radius_miles' => 'decimal:2',
        'total_stops' => 'integer',
        'estimated_total_drive_minutes' => 'integer',
        'estimated_total_drive_miles' => 'decimal:2',
        'deleted_at' => 'datetime',
    ];

    // CHANGED: Added relationship to the user who created the reusable route.
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // CHANGED: Added relationship to the user assigned to the reusable route.
    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // CHANGED: Added relationship to the planned route stops.
    public function stops(): HasMany
    {
        return $this->hasMany(OfficeRouteStop::class);
    }

    // CHANGED: Added ordered planned stops helper.
    public function orderedStops(): HasMany
    {
        return $this->hasMany(OfficeRouteStop::class)->orderBy('stop_order');
    }

    // CHANGED: Added relationship to execution runs for this reusable route.
    public function runs(): HasMany
    {
        return $this->hasMany(OfficeRouteRun::class);
    }

    // CHANGED: Added latest run helper relationship.
    public function latestRun(): HasMany
    {
        return $this->hasMany(OfficeRouteRun::class)->latest('id');
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
}
