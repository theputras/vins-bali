# Vins Bali - Project Knowledge Base & AI Coding Guidelines

Dokumen ini berfungsi sebagai panduan arsitektur, *tech stack*, dan *coding conventions* (aturan koding) untuk sistem AI (seperti Claude, Cursor, atau sistem lain) yang akan membantu dalam pengembangan website **Vins Bali**.

---

## 1. Tech Stack (Teknologi yang Digunakan)

Proyek ini dibangun menggunakan **Laravel + Vue + Inertia (TALL/VILT Stack varian)** dengan spesifikasi berikut:

### Backend
- **Framework:** Laravel 11+ (PHP 8.3+)
- **Authentication:** Laravel Fortify (Session-based)
- **Database:** MySQL / PostgreSQL
- **Media & Image:** Laravel Storage (Local/Public), dikombinasikan dengan pengunggahan multi-image di form.
- **Sitemap & SEO:** `spatie/laravel-sitemap` terintegrasi.

### Frontend
- **Framework:** Vue 3 (Composition API dengan `<script setup lang="ts">`)
- **Routing & State:** Inertia.js (SPA Navigation, Shared Data via `HandleInertiaRequests.php`)
- **Styling:** Tailwind CSS
- **UI Components:** Kustom komponen ala **Radix/Shadcn UI** (bisa dilihat di `resources/js/components/ui/`)
- **Icons:** `lucide-vue-next`
- **Build Tool:** Vite

---

## 2. Arsitektur Folder & File Utama

- **`app/Http/Controllers/`** 
  - `Admin/`: Controller untuk CMS/Admin Panel (CRUD Mobil, Layanan, Settings).
  - Controller publik (seperti `CatalogController`, `HomeController`) untuk rendering halaman frontend.
- **`app/Http/Middleware/HandleInertiaRequests.php`**
  - Pusat sharing data global. Semua data pengaturan dinamis (seperti konfigurasi Kontak, SEO, Logo Brand) di-inject ke frontend (`$page.props.global_settings`) melalui file ini.
- **`resources/js/pages/`**
  - **`admin/`**: Halaman-halaman untuk Admin Panel (dimulai dari rute `/vbpanel/...`).
  - **`catalog/`**: Halaman frontend untuk list produk (`Index.vue`) dan detail (`Show.vue`).
  - **`Home.vue`**: Halaman Landing Page utama.
- **`resources/js/components/ui/`**
  - Base components (Button, Card, Dialog, dll) yang bersifat *reusable*.

---

## 3. Sistem Pengaturan Dinamis (Settings)

Vins Bali tidak *hardcode* teks di frontend. Kami menggunakan satu tabel `settings` berbentuk *key-value store*. Beberapa keys diubah otomatis menjadi array/objek (JSON) di controller/middleware.

**Kunci Konfigurasi yang Tersedia (via `$page.props.global_settings`):**
- **Umum:** `whatsapp_number`, `company_email`, `company_address`, `instagram_url`, `facebook_url`
- **Homepage:** `home_hero_title`, `home_hero_highlight`, `home_hero_subtitle`, `home_brand_logos`, `home_usps`, `home_services`
- **Rental:** `terms_and_conditions`, `rental_requirements`, `rental_requirements_footer`
- **SEO & Meta (`seo_settings` JSON):** Menyimpan default SEO, keywords, robot rules, Twitter card properties, dan meta dinamis khusus Home/Katalog.

*(Catatan untuk AI: Selalu rujuk ke properti ini jika diminta untuk mengganti teks/logo di layout publik. Jangan hardcode di file `.vue`)*.

---

## 4. Infrastruktur SEO & Indexing Google

Sistem SEO di Vins Bali sangat *advance* dan terstruktur:
1. **Dynamic Meta Tags:** Dikelola menggunakan tag `<Head>` bawaan Inertia di `GuestLayout.vue`, `Home.vue`, `Index.vue`, dan `Show.vue`.
2. **Dynamic XML Sitemap:** Dibuat menggunakan controller `SitemapController` (melalui rute `/sitemap.xml`) yang otomatis me-looping semua data mobil terbaru setiap hari.
3. **Structured Data (JSON-LD):** Pada halaman `Show.vue` (Detail Mobil), terdapat script `application/ld+json` yang memuat schema `Product` dan `Offer`. Ini memicu *Rich Snippets* di Google.

---

## 5. Konvensi Koding (Rules for AI)

Saat menulis atau memodifikasi kode, harap patuhi aturan berikut:

### Frontend (Vue/Inertia)
1. **Selalu gunakan `<script setup lang="ts">`**. Jangan gunakan Options API.
2. **Gunakan Tailwind secara maksimal**. Hindari pembuatan file `.css` khusus kecuali untuk *base layer* (`index.css`). Gunakan utility classes.
3. **Inertia Link:** Gunakan komponen `<Link href="...">` dari `@inertiajs/vue3` untuk navigasi internal, bukan tag `<a>`.
4. **Form Handling:** Selalu gunakan `useForm()` dari Inertia untuk menangani form submission (post, put, delete) untuk mempermudah tracking error, reset form, dan state processing (misalnya: `:disabled="form.processing"`).
5. **Iconografi:** Gunakan `lucide-vue-next` untuk segala kebutuhan icon. Jangan gunakan FontAwesome atau SVG hardcoded yang kotor.
6. **Responsivitas:** Selalu cek class `sm:`, `md:`, `lg:` saat mendesain UI agar tampilan optimal di HP dan Desktop.

### Backend (Laravel/PHP)
1. **Validasi:** Selalu gunakan `$request->validate()` di Controller sebelum memproses data.
2. **Fat Models, Skinny Controllers:** Pindahkan logika query yang rumit ke Model (Scope) atau Service jika controller mulai membesar.
3. **Database Migration:** Selalu pertahankan struktur referensi relasional (`foreignId`, `constrained`, `cascadeOnDelete`).
4. **Inertia Response:** Gunakan `return Inertia::render('Path/To/Component', [ 'data' => $data ]);` alih-alih merender Blade view. Blade hanya digunakan di root `app.blade.php`.

---

## 6. Pola Khusus: Dropdown Pencarian Brand
Pada form pengelolaan (`Form.vue`), kami menggunakan *Custom Headless Combobox* menggunakan Vue Reactive State & Tailwind (tanpa lib external) untuk memilih merk mobil yang mem-filter data dari `global_settings.home_brand_logos`. Pola ini mengedepankan performa DOM yang sangat ringan namun kaya akan *UX* (memiliki pencarian dan icon terintegrasi).

*(End of Claude Rules)*
