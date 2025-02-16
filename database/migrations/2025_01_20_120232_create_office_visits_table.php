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
        Schema::create('office_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('office_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
            $table->date('visit_date');
            $table->integer('duration')->nullable()->comment('Duration in minutes');
            $table->integer('rating')->nullable()->comment('Rating from 1 to 5');
            $table->string('purpose')->nullable();
            $table->text('initial_impressions')->nullable();
            $table->integer('interest_level');
            $table->boolean('follow_up_required');
            $table->json('materials_presented')->nullable();
            $table->text('discussion_topics');
            $table->string('notes')->nullable();
            $table->text('next_action')->nullable();
            $table->date('next_action_date')->nullable();
            $table->string('status')->default('planned');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('office_visits');
    }
};
