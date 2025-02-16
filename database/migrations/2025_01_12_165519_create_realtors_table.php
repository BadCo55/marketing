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
        Schema::create('realtors', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('phone_number');
            $table->string('email')->unique();
            $table->foreignId('office_id')->nullable()->constrained('offices')->nullOnDelete();
            $table->string('avatar')->nullable();
            $table->foreignId('owner')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title')->nullable();
            $table->integer('total_sales_count')->nullable();
            $table->integer('total_sales_value')->nullable();
            $table->json('social_media_links')->nullable();
            $table->date('dob')->nullable();
            $table->string('license_number')->nullable();
            $table->string('primary_region')->nullable();
            $table->json('specializations')->nullable();
            $table->integer('years_in_business')->nullable();
            $table->string('preferred_inspector')->nullable();
            $table->string('preferred_inspection_company')->nullable();
            $table->boolean('bni_member')->nullable();
            $table->string('bni_chapter')->nullable();
            $table->string('acquired_via')->nullable();
            $table->string('client_volume')->nullable();
            $table->json('previous_inspections')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('realtors');
    }
};
