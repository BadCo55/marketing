<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OfficeRouteStop extends Model
{
    use SoftDeletes;

    // CHANGED: Updated fillable fields to match the rewritten planning-only office_route_stops migration.
    protected $fillable = [
        'office_route_id',
        'office_id',
        'stop_order',
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
        'estimated_drive_minutes_from_previous',
        'estimated_drive_miles_from_previous',
        'priority',
        'required',
        'notes',
    ];

    // CHANGED: Updated casts to reflect planning-only fields.
    protected $casts = [
        'stop_order' => 'integer',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'estimated_drive_minutes_from_previous' => 'integer',
        'estimated_drive_miles_from_previous' => 'decimal:2',
        'priority' => 'integer',
        'required' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    // CHANGED: Added relationship back to the reusable parent office route.
    public function officeRoute(): BelongsTo
    {
        return $this->belongsTo(OfficeRoute::class);
    }

    // CHANGED: Added relationship to the linked office record.
    public function office(): BelongsTo
    {
        return $this->belongsTo(Office::class);
    }

    // CHANGED: Added relationship to run-specific stop executions.
    public function runStops(): HasMany
    {
        return $this->hasMany(OfficeRouteRunStop::class);
    }

    // CHANGED: Added ordering scope for planned stop order.
    public function scopeOrdered($query)
    {
        return $query->orderBy('stop_order');
    }
}
