<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            'whatsapp_number' => '6281234567890',
            'company_email' => 'info@vinsbali.com',
            'company_address' => 'Jl. Bypass Ngurah Rai No. 88, Badung, Bali, Indonesia',
            'instagram_url' => 'https://instagram.com/vinsbali',
            'facebook_url' => 'https://facebook.com/vinsbali',
            'terms_and_conditions' => json_encode([
                'Penyewa wajib memiliki KTP/Paspor dan SIM A yang masih berlaku.',
                'Usia minimal penyewa adalah 21 tahun untuk mobil standar.',
                'Penyewa wajib menyerahkan dokumen asli sebagai jaminan selama masa sewa.',
                'Durasi sewa minimal adalah 1 hari (24 jam). Keterlambatan pengembalian dikenakan biaya tambahan per jam.',
                'Harga sudah termasuk PPN dan asuransi dasar kendaraan.',
                'Kendaraan dikembalikan dalam kondisi bersih yang sama saat penyerahan.',
                'Penyewa bertanggung jawab atas segala kerusakan selama masa sewa.',
                'Dilarang keras memakai kendaraan untuk balapan, tindak kriminal, atau dibawa ke luar Bali tanpa izin.',
                'Dilarang merokok di dalam kendaraan. Pelanggaran dikenakan denda pembersihan.',
            ]),
            'home_usps' => json_encode(['Tanpa Deposit', 'PPN Sudah Termasuk', 'Menerima Semua Bentuk Pembayaran', 'Gratis Bensin Penuh', 'Tol Sudah Termasuk']),
            'home_services' => json_encode([
                ['icon' => 'MapPin', 'title' => 'Penjemputan ke Bandara Biasa & VIP', 'description' => 'Hindari antrean panjang. Kami akan menunggu Anda langsung di terminal kedatangan Ngurah Rai (DPS) dan menyerahkan kunci secara personal.'],
                ['icon' => 'ShieldCheck', 'title' => 'Sewa Harian, Mingguan & Bulanan', 'description' => 'Baik untuk liburan singkat, perjalanan bisnis mingguan, atau opsi pengganti leasing mewah bulanan di Bali. Harga fleksibel.'],
                ['icon' => 'Clock', 'title' => 'Layanan dengan Supir (Chauffeur)', 'description' => 'Ingin bersantai sepenuhnya? Pesan kendaraan mewah beserta supir berbahasa Inggris yang profesional dan berpengalaman.']
            ]),
            'rental_requirements' => json_encode([
                'tourist' => ['Paspor Valid & Visa Kunjungan', 'SIM dari negara asal', 'Izin Mengemudi Internasional (IDP) jika diperlukan'],
                'resident' => ['KTP / KITAS yang berlaku', 'SIM A Nasional / Lokal Bali yang berlaku']
            ]),
            'home_hero_title' => 'Luxury Car Rental in',
            'home_hero_highlight' => 'Bali',
            'home_hero_subtitle' => 'Pilih dari 80+ koleksi mobil premium, sports, dan eksklusif. Pengalaman berkendara VIP yang tak tertandingi di Pulau Dewata.',
            'rental_requirements_footer' => 'Umur minimal 21 tahun untuk mobil standar, dan 25 tahun untuk mobil sports. Asuransi dasar, bantuan pinggir jalan, dan PPN sudah kami tanggung. Jarak tempuh standar 250km/hari.',
            'home_brand_logos' => json_encode([
                ['slug' => 'toyota', 'label' => 'Toyota'],
                ['slug' => 'bmw', 'label' => 'BMW'],
                ['slug' => 'mercedes-benz', 'label' => 'Mercedes-Benz'],
                ['slug' => 'porsche', 'label' => 'Porsche'],
                ['slug' => 'lamborghini', 'label' => 'Lamborghini'],
                ['slug' => 'ferrari', 'label' => 'Ferrari'],
                ['slug' => 'rolls-royce', 'label' => 'Rolls-Royce'],
                ['slug' => 'bentley', 'label' => 'Bentley'],
                ['slug' => 'mclaren', 'label' => 'McLaren'],
                ['slug' => 'audi', 'label' => 'Audi'],
            ]),
            'founder_name' => 'I Putu Vinso',
            'founder_title' => 'Pendiri & CEO VINS BALI',
            'founder_text' => 'Selamat datang di VINS BALI. Kami berkomitmen untuk menyajikan pengalaman berkendara mewah dan tak terlupakan di Pulau Dewata. Seluruh armada kami dirawat dengan standar keamanan tertinggi demi menjamin kenyamanan perjalanan Anda selama di Bali. Terima kasih telah mempercayakan perjalanan berharga Anda bersama layanan VIP kami.',
            'founder_photo_path' => '',
            'home_faqs' => json_encode([
                ['question' => 'Apakah penyewaan mobil bisa lepas kunci?', 'answer' => 'Ya, kami menyediakan opsi sewa mobil lepas kunci (self-drive) maupun sewa mobil dengan supir premium untuk menjamin kenyamanan perjalanan Anda di Bali.'],
                ['question' => 'Bagaimana cara konfirmasi dokumen persyaratan sewa?', 'answer' => 'Setelah memilih unit mobil, tim VIP kami akan menghubungi Anda melalui WhatsApp untuk mengonfirmasi foto dokumen persyaratan (seperti Paspor/KTP dan SIM). Proses ini sangat cepat dan praktis tanpa birokrasi berbelit.'],
                ['question' => 'Apakah pengantaran mobil ke bandara gratis?', 'answer' => 'Ya! Layanan pengantaran dan penjemputan unit mobil di Bandara Internasional I Gusti Ngurah Rai (DPS) serta wilayah utama di Bali (Seminyak, Kuta, Nusa Dua, dll) sepenuhnya gratis.'],
                ['question' => 'Bagaimana sistem asuransi kendaraan?', 'answer' => 'Semua armada kami sudah dilengkapi dengan asuransi dasar. Anda dapat meningkatkan ke perlindungan penuh (Full Coverage) saat serah terima unit jika membutuhkan rasa aman ekstra.']
            ]),
            'home_testimonials' => json_encode([
                ['name' => 'Rian Wijaya', 'rating' => 5, 'review' => 'Pelayanan VINS BALI luar biasa! Sewa Porsche Boxster lepas kunci, kondisi mobil sangat bersih dan mulus seperti baru. Proses serah terima di Bandara cepat sekali.'],
                ['name' => 'Sarah Connor', 'rating' => 5, 'review' => 'Very professional luxury car rental in Bali. We booked an Alphard with a driver. The driver was extremely polite, spoke good English, and knew all the best spots.'],
                ['name' => 'Andi Pratama', 'rating' => 5, 'review' => 'Sangat direkomendasikan untuk sewa mobil mewah di Bali. Tanpa deposit ribet, respon WhatsApp admin sangat cepat bahkan di malam hari.']
            ]),
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
