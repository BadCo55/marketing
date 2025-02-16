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
        Schema::create('realtor_sequences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('realtor_id')->constrained()->onDelete('cascade');
            $table->foreignId('sequence_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('current_step')->default(0);
            $table->date('start_date');
            $table->date('next_step_due');
            $table->enum('status', ['not_started', 'active', 'paused', 'cancelled', 'completed'])->default('not_started');
            $table->timestamps();

            $table->unique(['realtor_id', 'sequence_id']);
            $table->index(['current_step', 'next_step_due']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('realtor_sequences');
    }
};
