<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('instagram_accounts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('username', 100);
            $table->string('instagram_user_id', 100)->nullable();
            $table->text('cookies')->nullable();
            $table->text('user_agent')->nullable();
            $table->string('instagram_password_encrypted')->nullable(); // encrypted
            $table->enum('status', ['active', 'inactive', 'banned'])->default('active');
            $table->boolean('is_default')->default(false);
            $table->timestamp('last_login')->nullable();
            $table->timestamps();

            $table->index(['user_id', 'is_default']);
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('instagram_accounts');
    }
};
