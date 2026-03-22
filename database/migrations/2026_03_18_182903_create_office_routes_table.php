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
        Schema::create('office_routes', function (Blueprint $table) {
            $table->id();

            // CHANGED: Basic reusable route identity.
            $table->string('name');
            $table->text('description')->nullable();

            // CHANGED: Route definition lifecycle only, not execution lifecycle.
            // draft, active, archived
            $table->string('status')->default('draft');

            // CHANGED: Tracks who created the route definition.
            $table->foreignId('created_by')
                ->constrained('users')
                ->cascadeOnDelete();

            // CHANGED: Optional assigned field rep / salesperson for the route plan.
            $table->foreignId('assigned_to')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            // CHANGED: Optional planning date for the route definition.
            $table->date('route_date')->nullable();

            // CHANGED: Optional route-level planning settings.
            $table->decimal('center_latitude', 10, 7)->nullable();
            $table->decimal('center_longitude', 10, 7)->nullable();
            $table->decimal('radius_miles', 8, 2)->nullable();

            // CHANGED: Snapshot planning totals / optimization outputs.
            $table->unsignedInteger('total_stops')->default(0);
            $table->unsignedInteger('estimated_total_drive_minutes')->nullable();
            $table->decimal('estimated_total_drive_miles', 8, 2)->nullable();

            // CHANGED: Import / source tracking.
            // manual, csv, xlsx, mixed, radius
            $table->string('source_type')->nullable();
            $table->string('import_file_name')->nullable();

            // CHANGED: General reusable route notes.
            $table->longText('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // CHANGED: Helpful indexes for route planning screens and filtering.
            $table->index(['status', 'route_date']);
            $table->index(['assigned_to', 'route_date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('office_routes');
    }
};
