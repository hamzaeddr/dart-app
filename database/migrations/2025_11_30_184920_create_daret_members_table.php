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
        Schema::create('daret_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('daret_id')->constrained('darets')->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('position_in_cycle');
            $table->timestamp('joined_at')->nullable();
            $table->timestamps();

            $table->unique(['daret_id', 'user_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('daret_members');
    }
};
