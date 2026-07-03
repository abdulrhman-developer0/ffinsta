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
        Schema::table('follow_tasks', function (Blueprint $table) {
            $table->foreignId('instagram_account_id')->nullable()->after('assigned_user_id')->constrained('instagram_accounts')->nullOnDelete();
            $table->integer('initial_following_count')->nullable()->after('initial_follower_count');
            $table->string('verification_type', 50)->nullable()->after('initial_following_count'); // 'following' or 'follower'
            $table->timestamp('complete_clicked_at')->nullable()->after('lock_expires_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('follow_tasks', function (Blueprint $table) {
            $table->dropForeign(['instagram_account_id']);
            $table->dropColumn([
                'instagram_account_id',
                'initial_following_count',
                'verification_type',
                'complete_clicked_at'
            ]);
        });
    }
};
