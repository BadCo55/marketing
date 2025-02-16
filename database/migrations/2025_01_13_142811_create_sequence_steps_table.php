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
        Schema::create('sequence_steps', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sequence_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->integer('interval')->default(0);
            $table->text('description')->nullable();
            $table->text('template')->nullable();
            $table->integer('step_number')->default(0);
            $table->timestamps();

            $table->index('step_number');
            $table->unique(['sequence_id', 'step_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sequence_steps');
    }
};
