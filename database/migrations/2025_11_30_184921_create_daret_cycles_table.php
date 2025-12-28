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
        Schema::create('daret_cycles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daret_id')->constrained('darets')->cascadeOnDelete();
            $table->unsignedInteger('cycle_number');
            $table->date('due_date')->nullable();
            $table->foreignId('recipient_id')->constrained('users');
            $table->boolean('is_completed')->default(false);
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['daret_id', 'cycle_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daret_cycles');
    }
};
