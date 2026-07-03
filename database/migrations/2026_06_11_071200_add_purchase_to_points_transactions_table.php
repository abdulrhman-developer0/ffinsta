<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE points_transactions MODIFY COLUMN type ENUM('earn', 'spend', 'coupon', 'referral', 'admin_adjustment', 'purchase') NOT NULL");
    }

    public function down(): void
    {
        // Fall back to old enum (note: database strict mode might complain if values of type 'purchase' exist)
        DB::statement("ALTER TABLE points_transactions MODIFY COLUMN type ENUM('earn', 'spend', 'coupon', 'referral', 'admin_adjustment') NOT NULL");
    }
};
