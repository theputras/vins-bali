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
        Schema::create('cars_category', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->timestamps();
        });

        // Add cars_category_id to cars table
        Schema::table('cars', function (Blueprint $table) {
            $table->foreignId('cars_category_id')->nullable()->constrained('cars_category')->nullOnDelete();
        });

        // Migrate existing category string to cars_category_id
        $cars = \Illuminate\Support\Facades\DB::table('cars')->select('id', 'category')->get();
        $categoriesMap = [];

        foreach ($cars as $car) {
            if (!empty($car->category)) {
                $categoryName = trim($car->category);
                $categorySlug = \Illuminate\Support\Str::slug($categoryName);

                if (!isset($categoriesMap[$categorySlug])) {
                    $existing = \Illuminate\Support\Facades\DB::table('cars_category')->where('slug', $categorySlug)->first();
                    if (!$existing) {
                        $categoryId = \Illuminate\Support\Facades\DB::table('cars_category')->insertGetId([
                            'name' => $categoryName,
                            'slug' => $categorySlug,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    } else {
                        $categoryId = $existing->id;
                    }
                    $categoriesMap[$categorySlug] = $categoryId;
                }

                \Illuminate\Support\Facades\DB::table('cars')
                    ->where('id', $car->id)
                    ->update(['cars_category_id' => $categoriesMap[$categorySlug]]);
            }
        }

        // Drop old category column
        Schema::table('cars', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cars', function (Blueprint $table) {
            $table->string('category')->nullable();
        });

        $cars = \Illuminate\Support\Facades\DB::table('cars')->select('id', 'cars_category_id')->get();
        $categoriesMap = \Illuminate\Support\Facades\DB::table('cars_category')->pluck('name', 'id');

        foreach ($cars as $car) {
            if (!empty($car->cars_category_id) && isset($categoriesMap[$car->cars_category_id])) {
                \Illuminate\Support\Facades\DB::table('cars')
                    ->where('id', $car->id)
                    ->update(['category' => $categoriesMap[$car->cars_category_id]]);
            }
        }

        Schema::table('cars', function (Blueprint $table) {
            $table->dropForeign(['cars_category_id']);
            $table->dropColumn('cars_category_id');
        });

        Schema::dropIfExists('cars_category');
    }
};
