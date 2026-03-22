<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('office_route_run_stops', function (Blueprint $table) {
            $table->id();

            // CHANGED: Links this run stop to a specific route execution.
            $table->foreignId('office_route_run_id')
                ->constrained('office_route_runs')
                ->cascadeOnDelete();

            // CHANGED: Links back to the planned route stop when one exists.
            $table->foreignId('office_route_stop_id')
                ->nullable()
                ->constrained('office_route_stops')
                ->nullOnDelete();

            // CHANGED: Keeps a direct link to the office for easier querying/reporting.
            $table->foreignId('office_id')
                ->nullable()
                ->constrained('offices')
                ->nullOnDelete();

            // CHANGED: Stores planned vs actual order independently.
            $table->unsignedInteger('planned_stop_order')->nullable();
            $table->unsignedInteger('actual_stop_order')->nullable();

            // CHANGED: Main execution status for this stop during this run.
            // pending, in_progress, arrived, completed, skipped, cancelled
            $table->string('status')->default('pending');

            // CHANGED: Snapshot fields so history remains accurate if office data changes later.
            $table->string('parent_company')->nullable();
            $table->string('office_name')->nullable();
            $table->string('street_address')->nullable();
            $table->string('unit_number')->nullable();
            $table->string('city')->nullable();
            $table->string('state', 10)->nullable();
            $table->string('zip_code', 20)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->string('office_phone')->nullable();

            // CHANGED: Carries forward planning values from the route stop if needed.
            $table->unsignedTinyInteger('priority')->default(3);
            $table->boolean('required')->default(false);

            // CHANGED: Stores estimated travel from prior stop at the time the run began.
            $table->integer('estimated_drive_minutes_from_previous')->nullable();
            $table->decimal('estimated_drive_miles_from_previous', 8, 2)->nullable();

            // CHANGED: Stores actual travel/performance values for this run stop.
            $table->integer('actual_drive_minutes_from_previous')->nullable();
            $table->decimal('actual_drive_miles_from_previous', 8, 2)->nullable();

            // CHANGED: Stores execution timestamps for the stop lifecycle.
            $table->timestamp('started_at')->nullable();
            $table->timestamp('arrived_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('skipped_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            // CHANGED: Stores optional dwell time summary in minutes.
            $table->unsignedInteger('duration_minutes')->nullable();

            // CHANGED: Links to the actual visit created from this stop, if any.
            $table->foreignId('office_visit_id')
                ->nullable()
                ->constrained('office_visits')
                ->nullOnDelete();

            // CHANGED: Stores outcome + notes specific to this run stop.
            $table->string('outcome')->nullable();
            $table->longText('notes')->nullable();
            $table->longText('skip_reason')->nullable();
            $table->longText('cancel_reason')->nullable();

            // CHANGED: Standard audit columns.
            $table->foreignId('created_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('updated_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // CHANGED: Helpful indexes for run playback, dashboards, and reporting.
            $table->unique(
                ['office_route_run_id', 'planned_stop_order'],
                'ors_run_id_order_unique' // CHANGED: shortened index name to avoid MySQL 64-char limit
            );
            $table->index(['office_route_run_id', 'status']);
            $table->index(['office_route_run_id', 'actual_stop_order'], 'ors_run_id_actual_order_idx');
            $table->index(['office_id']);
            $table->index(['office_visit_id'], 'ors_visit_id_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('office_route_run_stops');
    }
};
