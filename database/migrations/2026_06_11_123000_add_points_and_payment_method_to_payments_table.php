<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedInteger('points')->after('amount_usd')->default(0);
            $table->string('payment_method', 50)->after('points')->default('vodafone_cash');
            $table->string('sender_phone', 20)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['points', 'payment_method']);
            $table->string('sender_phone', 20)->nullable(false)->change();
        });
    }
};
