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
        Schema::create('office_route_stops', function (Blueprint $table) {
            $table->id();

            // CHANGED: Links each planned stop to the reusable route definition.
            $table->foreignId('office_route_id')
                ->constrained('office_routes')
                ->cascadeOnDelete();

            // CHANGED: Usually tied to an office in your system.
            $table->foreignId('office_id')
                ->nullable()
                ->constrained('offices')
                ->nullOnDelete();

            // CHANGED: Planned order within the route definition.
            $table->unsignedInteger('stop_order');

            // CHANGED: Snapshot fields in case office data changes later.
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

            // CHANGED: Planning / optimization info only.
            $table->integer('estimated_drive_minutes_from_previous')->nullable();
            $table->decimal('estimated_drive_miles_from_previous', 8, 2)->nullable();

            // CHANGED: Optional visit-planning details.
            $table->unsignedTinyInteger('priority')->default(3); // 1 high, 5 low
            $table->boolean('required')->default(false);

            // CHANGED: Route-plan-specific notes only.
            $table->longText('notes')->nullable();

            $table->timestamps();
            $table->softDeletes();

            // CHANGED: Ensure each stop order is unique within a planned route.
            $table->unique(['office_route_id', 'stop_order']);

            // CHANGED: Helpful indexes for stop lookups and route loading.
            $table->index(['office_route_id']);
            $table->index(['office_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('office_route_stops');
    }
};
