<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number', 30)->unique();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('instagram_account_id')->nullable()->constrained('instagram_accounts')->nullOnDelete();
            $table->string('instagram_username', 100);
            $table->unsignedInteger('requested_qty');
            $table->unsignedInteger('delivered_qty')->default(0);
            $table->unsignedInteger('points_cost')->default(0);
            $table->enum('status', ['pending', 'active', 'completed', 'cancelled'])->default('pending');
            $table->enum('priority', ['normal', 'high'])->default('normal');
            $table->text('admin_notes')->nullable();
            $table->boolean('admin_created')->default(false);
            $table->timestamps();

            $table->index(['user_id', 'status']);
            $table->index('status');
            $table->index('priority');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
