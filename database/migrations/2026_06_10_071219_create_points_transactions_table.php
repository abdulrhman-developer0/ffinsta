<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('points_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->enum('type', ['earn', 'spend', 'coupon', 'referral', 'admin_adjustment']);
            $table->integer('amount'); // positive = credit, negative = debit
            $table->unsignedBigInteger('balance_after');
            $table->string('description');
            $table->string('reference_type', 50)->nullable(); // e.g. 'App\Models\Order'
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete(); // if admin-triggered
            $table->timestamps();

            $table->index(['user_id', 'type']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('points_transactions');
    }
};
