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
        Schema::table('cars', function (Blueprint $table) {
            $table->integer('discount_3_days')->default(5)->after('acceleration_0_100');
            $table->integer('discount_weekly')->default(10)->after('discount_3_days');
            $table->integer('discount_monthly')->default(20)->after('discount_weekly');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn(['discount_3_days', 'discount_weekly', 'discount_monthly']);
        });
    }
};
