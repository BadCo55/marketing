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
        Schema::create('office_route_runs', function (Blueprint $table) {
            $table->id();

            // CHANGED: Links this execution record back to the reusable planned route.
            $table->foreignId('office_route_id')
                ->constrained('office_routes')
                ->cascadeOnDelete();

            // CHANGED: Tracks the user assigned to perform the run, if applicable.
            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // CHANGED: Tracks who actually started the route run.
            $table->foreignId('started_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // CHANGED: Tracks who completed the route run, if completed.
            $table->foreignId('completed_by')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // CHANGED: Main execution lifecycle status for the run.
            // not_started, in_progress, paused, completed, cancelled
            $table->string('status')->default('not_started');

            // CHANGED: Planned/scheduled date for this run instance.
            $table->date('run_date')->nullable();

            // CHANGED: Simple progress helpers so the current stop can be identified quickly.
            $table->unsignedInteger('current_stop_order')->nullable();
            $table->unsignedInteger('planned_stop_count')->default(0);
            $table->unsignedInteger('completed_stop_count')->default(0);
            $table->unsignedInteger('skipped_stop_count')->default(0);
            $table->unsignedInteger('cancelled_stop_count')->default(0);

            // CHANGED: Actual execution timestamps.
            $table->timestamp('started_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();

            // CHANGED: Optional start location snapshot for the run.
            $table->decimal('start_latitude', 10, 7)->nullable();
            $table->decimal('start_longitude', 10, 7)->nullable();

            // CHANGED: Optional finish location snapshot for the run.
            $table->decimal('end_latitude', 10, 7)->nullable();
            $table->decimal('end_longitude', 10, 7)->nullable();

            // CHANGED: Aggregate actual time / distance values from execution.
            $table->unsignedInteger('actual_total_drive_minutes')->nullable();
            $table->unsignedInteger('actual_total_duration_minutes')->nullable();
            $table->decimal('actual_total_drive_miles', 8, 2)->nullable();

            // CHANGED: Freeform execution notes for this specific run.
            $table->text('notes')->nullable();

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

            // CHANGED: Helpful indexes for execution dashboards and filtering.
            $table->index(['office_route_id', 'status']);
            $table->index(['assigned_to', 'run_date']);
            $table->index(['started_by', 'started_at']);
            $table->index(['status', 'current_stop_order']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('office_route_runs');
    }
};
