<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('follow_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('requester_order_id')->constrained('orders')->cascadeOnDelete();
            $table->string('requester_instagram_username', 100);
            $table->foreignId('assigned_user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->unsignedInteger('reward_points');
            $table->enum('status', ['available', 'assigned', 'completed', 'failed'])->default('available');
            $table->timestamp('assigned_at')->nullable();
            $table->timestamp('lock_expires_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index(['assigned_user_id', 'status']);
            $table->index('requester_order_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('follow_tasks');
    }
};
