Berikut adalah draf lengkap untuk file `README.md` yang disesuaikan dengan struktur proyek Laravel + Vue (Inertia.js) milik Vins Bali. Panduan ini mencakup deskripsi proyek, teknologi yang digunakan, serta langkah-langkah detail untuk instalasi di server (VPS) atau hosting.

Kamu bisa menyalin teks di bawah ini dan menyimpannya sebagai file `README.md` di root folder proyek kamu.

***

```markdown
# Vins Bali - Car Rental & Catalog System

Vins Bali adalah aplikasi berbasis web yang digunakan untuk mengelola katalog mobil, kategori, layanan, dan pengaturan situs rental mobil. Sistem ini dibangun dengan tumpukan teknologi modern (TALL/VILT stack) untuk memberikan performa yang cepat dan antarmuka yang dinamis.

## 🚀 Teknologi yang Digunakan

- **Backend:** [Laravel](https://laravel.com/) (PHP)
- **Frontend:** [Vue.js 3](https://vuejs.org/) dengan Composition API
- **Routing & Jembatan Data:** [Inertia.js](https://inertiajs.com/)
- **Styling:** [Tailwind CSS](https://tailwindcss.com/)
- **Build Tool:** Vite

## 📋 Persyaratan Sistem (Prerequisites)

Sebelum melakukan instalasi di server, pastikan server atau hosting Anda sudah terinstall:
- PHP >= 8.2
- Composer
- Node.js (v18 atau lebih baru) & NPM
- MySQL atau MariaDB
- Ekstensi PHP: BCMath, Ctype, Fileinfo, JSON, Mbstring, OpenSSL, PDO, Tokenizer, XML (Standar kebutuhan Laravel)

---

## 🛠️ Panduan Instalasi di Server / VPS (Ubuntu/Debian)

Langkah-langkah berikut diasumsikan Anda memiliki akses SSH ke server (VPS) Anda.

### 1. Clone Repository
Masuk ke direktori web server Anda (misal: `/var/www/`) dan clone repository ini:
```bash
cd /var/www/
git clone <URL_REPOSITORY_ANDA> vins-bali
cd vins-bali
```

### 2. Setup Environment
Salin file konfigurasi environment bawaan dan sesuaikan dengan kredensial database Anda:
```bash
cp .env.example .env
```
Buka file `.env` (misal dengan `nano .env`) dan atur bagian database serta URL website:
```env
APP_NAME="Vins Bali"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=[https://domainanda.com](https://domainanda.com)

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_database_anda
DB_USERNAME=user_database_anda
DB_PASSWORD=password_database_anda
```

### 3. Install Dependensi PHP (Composer)
Jalankan perintah berikut untuk menginstall semua library backend tanpa dev-dependencies:
```bash
composer install --optimize-autoloader --no-dev
```

### 4. Install Dependensi Frontend & Build Assets
Install modul Node.js dan compile aset frontend menggunakan Vite untuk mode produksi:
```bash
npm install
npm run build
```

### 5. Generate Application Key & Storage Link
Buat kunci aplikasi Laravel dan hubungkan folder storage agar gambar mobil bisa diakses oleh publik:
```bash
php artisan key:generate
php artisan storage:link
```

### 6. Migrasi & Seeding Database
Jalankan migrasi untuk membuat tabel-tabel di database, sekaligus mengisi data awal (seperti akun Admin, Pengaturan Dasar, dll):
```bash
php artisan migrate --seed
```
*(Catatan: Periksa file `database/seeders/AdminSeeder.php` jika Anda ingin mengetahui atau mengubah email dan password default untuk login Admin).*

### 7. Atur Izin Folder (Permissions)
Pastikan web server (misal: `www-data` untuk Nginx/Apache) memiliki hak akses baca dan tulis ke folder `storage` dan `bootstrap/cache`:
```bash
sudo chown -R www-data:www-data /var/www/vins-bali
sudo chmod -R 775 /var/www/vins-bali/storage
sudo chmod -R 775 /var/www/vins-bali/bootstrap/cache
```

### 8. Konfigurasi Web Server (Nginx)
Buat konfigurasi virtual host yang mengarah ke folder `public` dari proyek ini.
Contoh konfigurasi Nginx:
```nginx
server {
    listen 80;
    server_name domainanda.com;
    root /var/www/vins-bali/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```
Restart web server: `sudo systemctl restart nginx`

---

## 🌐 Panduan Instalasi di Shared Hosting (cPanel)

Jika Anda menggunakan Shared Hosting dan tidak memiliki akses Terminal/SSH penuh, ikuti langkah berikut:

1. **Persiapan di Komputer Lokal:**
   - Jalankan `composer install`, `npm install`, dan `npm run build` di komputer lokal Anda.
   - Compress (ZIP) seluruh folder proyek.

2. **Upload ke cPanel:**
   - Buka **File Manager** di cPanel.
   - Buat folder baru di luar `public_html` (misal: `vins-bali-app`).
   - Upload file ZIP tadi ke dalam folder `vins-bali-app` dan ekstrak.

3. **Pindahkan Folder Public:**
   - Masuk ke folder `vins-bali-app/public`.
   - Pindahkan **semua isi** dari folder `public` tersebut ke dalam folder `public_html` (atau folder addon domain Anda).

4. **Edit File `index.php`:**
   - Buka file `index.php` yang sekarang berada di `public_html`.
   - Ubah path yang memanggil file `autoload.php` dan `app.php` agar mengarah ke folder aplikasi Anda.
     ```php
     require __DIR__.'/../vins-bali-app/vendor/autoload.php';
     $app = require_once __DIR__.'/../vins-bali-app/bootstrap/app.php';
     ```

5. **Setup Database:**
   - Buat database dan user MySQL melalui fitur **MySQL Databases** di cPanel.
   - Sesuaikan kredensial di file `.env` yang ada di folder `vins-bali-app`.
   - Export database dari lokal menggunakan phpMyAdmin, lalu Import ke phpMyAdmin di cPanel.
   
6. **Perbaiki Storage Link (Symlink):**
   - Karena Anda tidak bisa menjalankan `php artisan storage:link`, Anda harus membuat symlink secara manual melalui script PHP atau Cron Job.
   - Buat file `symlink.php` di `public_html` dengan isi:
     ```php
     <?php
     symlink('/home/user_cpanel_anda/vins-bali-app/storage/app/public', '/home/user_cpanel_anda/public_html/storage');
     echo "Symlink Created!";
     ```
   - Akses `domainanda.com/symlink.php` melalui browser. Jika berhasil, segera hapus file `symlink.php` tersebut.

---

## 📂 Struktur Utama Proyek
- `app/Models/` : Berisi model database (Car, CarCategory, Service, Setting).
- `app/Http/Controllers/Admin/` : Logic untuk manajemen dashboard admin.
- `resources/js/Pages/` : Halaman tampilan berbasis Vue.js.
- `resources/js/components/` : Komponen UI Vue dan Tailwind yang dapat digunakan kembali.
- `routes/web.php` : Definisi rute utama aplikasi.

## 🛡️ Keamanan
Pastikan `APP_DEBUG` disetel ke `false` di server produksi Anda untuk mencegah kebocoran informasi sensitif.
```