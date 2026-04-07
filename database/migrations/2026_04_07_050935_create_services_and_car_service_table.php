<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Create services table (Master Data for rental durations)
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('duration_days');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // Seed initial services so we can map old durations to the new model
        DB::table('services')->insert([
            ['name' => 'Sewa 3 Hari', 'duration_days' => 3, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sewa Mingguan', 'duration_days' => 7, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Sewa Bulanan', 'duration_days' => 30, 'is_active' => true, 'created_at' => now(), 'updated_at' => now()],
        ]);

        // Fetch IDs to migrate
        $s3Id = DB::table('services')->where('duration_days', 3)->value('id');
        $s7Id = DB::table('services')->where('duration_days', 7)->value('id');
        $s30Id = DB::table('services')->where('duration_days', 30)->value('id');

        // 2. Create car_service pivot table
        Schema::create('car_service', function (Blueprint $table) {
            $table->id();
            $table->foreignId('car_id')->constrained('cars')->cascadeOnDelete();
            $table->foreignId('service_id')->constrained('services')->cascadeOnDelete();
            $table->integer('discount_percentage');
            $table->timestamps();
        });

        // 3. Migrate old discounts into pivot table
        $cars = DB::table('cars')->select('id', 'discount_3_days', 'discount_weekly', 'discount_monthly')->get();
        
        $pivots = [];
        foreach ($cars as $car) {
            if ($car->discount_3_days !== null && $car->discount_3_days >= 0 && $s3Id) {
                $pivots[] = ['car_id' => $car->id, 'service_id' => $s3Id, 'discount_percentage' => $car->discount_3_days, 'created_at' => now(), 'updated_at' => now()];
            }
            if ($car->discount_weekly !== null && $car->discount_weekly >= 0 && $s7Id) {
                $pivots[] = ['car_id' => $car->id, 'service_id' => $s7Id, 'discount_percentage' => $car->discount_weekly, 'created_at' => now(), 'updated_at' => now()];
            }
            if ($car->discount_monthly !== null && $car->discount_monthly >= 0 && $s30Id) {
                $pivots[] = ['car_id' => $car->id, 'service_id' => $s30Id, 'discount_percentage' => $car->discount_monthly, 'created_at' => now(), 'updated_at' => now()];
            }
        }
        
        if (!empty($pivots)) {
            DB::table('car_service')->insert($pivots);
        }

        // 4. Drop the old columns from the cars table
        // We use Schema::table to alter existing table
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn(['discount_3_days', 'discount_weekly', 'discount_monthly']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Re-add columns
        Schema::table('cars', function (Blueprint $table) {
            $table->integer('discount_3_days')->nullable()->default(5);
            $table->integer('discount_weekly')->nullable()->default(10);
            $table->integer('discount_monthly')->nullable()->default(20);
        });

        Schema::dropIfExists('car_service');
        Schema::dropIfExists('services');
    }
};
