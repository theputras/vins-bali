<?php

namespace Database\Seeders;

use App\Models\Car;
use App\Models\CarImage;
use Illuminate\Database\Seeder;

class CarSeeder extends Seeder
{
    /**
     * Seed the cars catalog for VINS Bali.
     */
    public function run(): void
    {
        $cars = [
            [
                'name' => 'BMW Z4',
                'brand' => 'BMW',
                'short_description' => 'Roadster sporty dengan desain aerodinamis dan performa tinggi.',
                'description' => 'BMW Z4 adalah roadster mewah yang menggabungkan desain aerodinamis dengan performa mesin yang bertenaga. Dilengkapi dengan atap lipat otomatis, interior kulit premium, dan teknologi terkini. Cocok untuk menikmati keindahan jalan-jalan di Bali dengan gaya yang elegan dan sporty.',
                'price_per_day' => 3500000,
                'transmission' => 'Automatic',
                'year' => 2023,
                'seats' => 2,
                'fuel_type' => 'Bensin',
                'color' => 'Silver',
                'is_available' => true,
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Mercedes E250',
                'brand' => 'Mercedes-Benz',
                'short_description' => 'Sedan eksekutif dengan kenyamanan kelas dunia.',
                'description' => 'Mercedes-Benz E250 adalah sedan eksekutif yang menawarkan kenyamanan luar biasa dan teknologi canggih. Dengan interior mewah berbahan kulit Nappa, sistem suspensi adaptif, dan mesin turbocharged yang halus, E250 adalah pilihan sempurna untuk perjalanan bisnis maupun liburan di Bali.',
                'price_per_day' => 3000000,
                'transmission' => 'Automatic',
                'year' => 2022,
                'seats' => 5,
                'fuel_type' => 'Bensin',
                'color' => 'Hitam',
                'is_available' => true,
                'is_featured' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Mini Cooper Merah',
                'brand' => 'Mini',
                'short_description' => 'City car ikonik dengan karakter British yang khas.',
                'description' => 'Mini Cooper dengan warna merah ikonik ini adalah pilihan sempurna untuk menjelajahi jalanan Bali dengan gaya. Desain retro-modern yang menawan, handling yang lincah, dan interior yang penuh karakter menjadikan setiap perjalanan menjadi pengalaman yang menyenangkan.',
                'price_per_day' => 2500000,
                'transmission' => 'Automatic',
                'year' => 2023,
                'seats' => 4,
                'fuel_type' => 'Bensin',
                'color' => 'Merah',
                'is_available' => true,
                'is_featured' => true,
                'sort_order' => 3,
            ],
            [
                'name' => 'Mustang Orange',
                'brand' => 'Ford',
                'short_description' => 'Muscle car legendaris dengan tenaga buas dan suara mesin yang menggoda.',
                'description' => 'Ford Mustang dalam balutan warna orange yang mencolok ini siap memberikan pengalaman berkendara yang tak terlupakan di Bali. Dengan mesin V8 yang bertenaga, desain agresif, dan exhaust note yang khas, Mustang ini menjadi pusat perhatian di setiap jalan yang dilaluinya.',
                'price_per_day' => 4000000,
                'transmission' => 'Automatic',
                'year' => 2023,
                'seats' => 4,
                'fuel_type' => 'Bensin',
                'color' => 'Orange',
                'is_available' => true,
                'is_featured' => true,
                'sort_order' => 4,
            ],
            [
                'name' => 'Porsche Grey',
                'brand' => 'Porsche',
                'short_description' => 'Sports car prestisius dengan handling presisi tinggi.',
                'description' => 'Porsche 911 dalam warna grey metalik yang elegan. Dengan mesin boxer bertenaga, handling yang presisi, dan desain timeless yang diakui dunia, mobil ini menawarkan pengalaman berkendara sportif terbaik. Sempurna untuk cruising di sepanjang pantai Bali.',
                'price_per_day' => 5000000,
                'transmission' => 'Automatic',
                'year' => 2024,
                'seats' => 2,
                'fuel_type' => 'Bensin',
                'color' => 'Grey',
                'is_available' => true,
                'is_featured' => true,
                'sort_order' => 5,
            ],
            [
                'name' => 'Porsche Cayman White Carbon',
                'brand' => 'Porsche',
                'short_description' => 'Mid-engine sports car dengan aksen karbon eksklusif.',
                'description' => 'Porsche Cayman edisi spesial dengan aksen White Carbon yang eksklusif. Mid-engine layout memberikan keseimbangan sempurna, sementara detail serat karbon menambah kesan sporty dan premium. Mobil ini adalah perpaduan sempurna antara performa dan estetika.',
                'price_per_day' => 5500000,
                'transmission' => 'Automatic',
                'year' => 2024,
                'seats' => 2,
                'fuel_type' => 'Bensin',
                'color' => 'Putih',
                'is_available' => true,
                'is_featured' => true,
                'sort_order' => 6,
            ],
            [
                'name' => 'Jeep CJ Red',
                'brand' => 'Jeep',
                'short_description' => 'SUV klasik untuk petualangan off-road di Bali.',
                'description' => 'Jeep CJ klasik dengan warna merah yang berani ini siap mengajak Anda berpetualang di medan off-road Bali. Dengan ground clearance tinggi, kemampuan 4WD, dan desain timeless yang ikonik, Jeep CJ adalah teman terbaik untuk menjelajahi sisi liar pulau Dewata.',
                'price_per_day' => 2000000,
                'transmission' => 'Manual',
                'year' => 2022,
                'seats' => 4,
                'fuel_type' => 'Bensin',
                'color' => 'Merah',
                'is_available' => true,
                'is_featured' => true,
                'sort_order' => 7,
            ],
            [
                'name' => 'Alphard 3',
                'brand' => 'Toyota',
                'short_description' => 'MPV premium untuk kenyamanan keluarga dan rombongan.',
                'description' => 'Toyota Alphard generasi ketiga adalah definisi ultimate dari kemewahan MPV. Dengan captain seat berbalut kulit, sistem hiburan layar ganda, suspensi udara yang empuk, dan kabin yang sangat luas, Alphard 3 menjamin kenyamanan maksimal untuk perjalanan keluarga atau rombongan di Bali.',
                'price_per_day' => 3500000,
                'transmission' => 'Automatic',
                'year' => 2024,
                'seats' => 7,
                'fuel_type' => 'Bensin',
                'color' => 'Putih',
                'is_available' => true,
                'is_featured' => true,
                'sort_order' => 8,
            ],
            [
                'name' => 'V-Class Viano',
                'brand' => 'Mercedes-Benz',
                'short_description' => 'Van mewah untuk perjalanan grup dengan standar Mercedes.',
                'description' => 'Mercedes-Benz V-Class Viano menawarkan kemewahan khas Mercedes dalam format van premium. Dengan kabin luas yang dapat menampung hingga 7 penumpang, interior kulit berkualitas tinggi, dan performa mesin diesel yang efisien, Viano adalah pilihan ideal untuk transfer VIP dan perjalanan grup di Bali.',
                'price_per_day' => 4000000,
                'transmission' => 'Automatic',
                'year' => 2023,
                'seats' => 7,
                'fuel_type' => 'Diesel',
                'color' => 'Hitam',
                'is_available' => true,
                'is_featured' => true,
                'sort_order' => 9,
            ],
        ];

        foreach ($cars as $carData) {
            $car = Car::updateOrCreate(
                ['slug' => \Illuminate\Support\Str::slug($carData['name'])],
                $carData
            );

            // Create placeholder images for each car (3 images, first is primary)
            if ($car->images()->count() === 0) {
                $slugName = \Illuminate\Support\Str::slug($carData['name']);

                for ($i = 1; $i <= 3; $i++) {
                    CarImage::create([
                        'car_id' => $car->id,
                        'image_path' => "cars/{$slugName}/{$i}.jpg",
                        'is_primary' => $i === 1,
                        'sort_order' => $i,
                    ]);
                }
            }
        }
    }
}
