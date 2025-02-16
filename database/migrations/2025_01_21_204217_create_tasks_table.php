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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title'); // Title of the task
            $table->text('description')->nullable(); // Detailed description of the task
            $table->string('priority')->default('medium');
            $table->foreignId('assigned_to')->constrained('users')->onDelete('cascade'); // Assign task to a user
            $table->date('due_date')->nullable(); // Optional due date for the task
            $table->enum('status', ['pending', 'in_progress', 'completed', 'cancelled'])->default('pending'); // Task status
            $table->morphs('taskable');
            $table->foreignId('created_by')->constrained('users')->onDelete('cascade'); // Assign task to a user
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
