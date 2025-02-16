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
        Schema::create('offices', function (Blueprint $table) {
            $table->id();
            $table->string('parent_company')->nullable();
            $table->string('office_name')->nullable();
            $table->string('office_phone')->nullable();
            $table->string('street_address');
            $table->string('unit_number')->nullable();
            $table->string('city');
            $table->string('state');
            $table->string('county')->nullable();
            $table->string('zip_code');
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->string('place_id');
            $table->string('image')->nullable();
            $table->timestamps();
        
            $table->unique(['place_id', 'unit_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offices');
    }
};
