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
        Schema::create('realtor_sequence_step_notes', function (Blueprint $table) {
            $table->id(); // unsignedBigInteger
            $table->foreignId('realtor_id')->constrained()->cascadeOnDelete(); // FK to realtors
            $table->foreignId('sequence_id')->constrained()->cascadeOnDelete(); // FK to sequences
            $table->foreignId('step_id')->constrained('sequence_steps')->cascadeOnDelete(); // FK to sequence_steps
            $table->string('status');
            $table->string('status_description')->nullable();
            $table->string('next_update');
            $table->text('note');
            $table->date('completion_date')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete(); // FK to users
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('realtor_sequence_step_notes');
    }
};
