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
            $table->string('category')->nullable()->after('brand');
            $table->integer('horsepower')->nullable()->after('seats');
            $table->string('engine_capacity')->nullable()->after('horsepower');
            $table->decimal('acceleration_0_100', 4, 1)->nullable()->after('engine_capacity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn(['category', 'horsepower', 'engine_capacity', 'acceleration_0_100']);
        });
    }
};
