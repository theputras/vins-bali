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
        ];

        foreach ($settings as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
