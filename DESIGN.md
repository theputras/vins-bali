# Vins Bali - Panduan Desain & Sistem UI (DESIGN.md)

Dokumen ini mendokumentasikan sistem desain, skema warna, tipografi, dan aturan estetika visual yang digunakan pada website **Vins Bali**. Panduan ini harus diikuti oleh pengembang dan asisten AI saat memodifikasi UI agar estetika premium tetap terjaga.

---

## 1. Versi Tailwind CSS
Proyek ini menggunakan **Tailwind CSS v4** (dengan integrasi `@tailwindcss/vite` v4). 
Perbedaan utama dibanding v3:
- **CSS-First Configuration**: Konfigurasi tema tidak menggunakan `tailwind.config.js` melainkan ditulis langsung di dalam file `resources/css/app.css` menggunakan directive `@theme inline`.
- **Import Utility**: Menggunakan `@import 'tailwindcss'` di bagian paling atas CSS.
- **Dark Mode**: Didefinisikan menggunakan `@custom-variant dark (&:is(.dark *))` untuk fleksibilitas pendeteksian dark class pada elemen HTML.

---

## 2. Sistem Warna (Color System)
Vins Bali menggunakan palet warna merah premium bernama **Totem Pole** sebagai aksen utama (*primary brand color*), dipadukan dengan skema netral bernuansa gelap (*sleek dark mode*) dan putih bersih untuk menjaga kontras kemewahan.

### A. Palet Warna Aksen: Totem Pole
Berikut adalah skala heksadesimal warna **Totem Pole** yang terdaftar di konfigurasi tema Tailwind:

| Shade | Kode Hex | Kegunaan Utama |
|---|---|---|
| `totem-pole-50` | `#ffefef` | Background light/soft alert, hover light |
| `totem-pole-100` | `#ffdbdc` | Border kemerahan halus |
| `totem-pole-200` | `#ffbcbd` | Highlight halus |
| `totem-pole-300` | `#ff8e90` | Red accent muda |
| `totem-pole-400` | `#ff4d50` | Warna eror / warning |
| `totem-pole-500` | `#ff161a` | State hover terang |
| `totem-pole-600` | `#ff0005` | Primary Red di **Dark Mode** (`hsl(359 100% 50%)`) |
| `totem-pole-700` | `#e30004` | Primary Red di **Light Mode** (`hsl(359 100% 45%)`) |
| `totem-pole-800` | `#bb0004` | Warna merah gelap premium |
| `totem-pole-900` | `#9f0306` | Aksen merah tua (deep luxury red) |
| `totem-pole-950` | `#550002` | Sangat gelap untuk bayangan/kontras tinggi |

### B. Variabel UI Tema (Shadcn UI Mappings)
Warna dipetakan menggunakan variabel CSS HSL di `:root` (Light) dan `.dark` (Dark):
- **Light Mode (`:root`)**:
  - `primary`: `hsl(359 100% 45%)` (Totem Pole 700)
  - `primary-foreground`: `hsl(0 0% 98%)` (Teks putih)
  - `background`: `hsl(0 0% 100%)` (Putih bersih)
  - `foreground`: `hsl(0 0% 3.9%)` (Hitam pekat)
- **Dark Mode (`.dark`)**:
  - `primary`: `hsl(359 100% 50%)` (Totem Pole 600)
  - `primary-foreground`: `hsl(0 0% 98%)` (Teks putih)
  - `background`: `hsl(0 0% 3.9%)` (Hitam pekat)
  - `foreground`: `hsl(0 0% 98%)` (Putih terang)

---

## 3. Tipografi (Typography)
Kemewahan sebuah website sangat ditentukan oleh pemilihan font. Kami menggunakan kombinasi tiga jenis huruf:
1. **Sans-Serif Utama (`font-sans`)**: 
   Menggunakan **Inter** dan **Instrument Sans** untuk keterbacaan teks body, menu navigasi, dan input form yang bersih dan modern.
2. **Signature/Handwriting (`font-signature`)**: 
   Menggunakan font **Great Vibes** dari Google Fonts khusus untuk elemen tanda tangan personal pendiri (*Founder's signature*) untuk menambahkan sentuhan eksklusif buatan tangan.

---

## 4. Animasi & Transisi Visual
Untuk menghadirkan pengalaman pengguna yang dinamis (*dynamic design*), kami menerapkan efek transisi berikut:
- **Scroll Reveal**: Elemen-elemen penting memicu efek transisi saat di-scroll ke area pandang (menggunakan atribut `data-reveal="up|left|right|fade"`).
- **Hover Micro-Animations**:
  * Efek blur glow dengan gradasi: `bg-gradient-to-tr from-primary to-amber-500` dengan opacity rendah.
  * Zoom halus gambar mobil/foto pendiri saat kursor diletakkan di atasnya (`group-hover:scale-105 duration-500`).
- **Autoplay Testimonials Carousel**: Slider testimoni bergerak otomatis setiap 4 detik, dan otomatis terjeda ketika kursor pengguna menyentuh/mengarah ke area slider.

---

## 5. Aturan Layout Responsif (Responsive Rules)
- **Grid Sistem**:
  - Tampilan mobil: 1 Kolom
  - Tampilan desktop: 3 Kolom
- **Navigation Indicators (Dots)**:
  - Jumlah dot indikator pada slider ulasan disesuaikan secara dinamis. Pada desktop, 3 kolom ulasan ditampilkan sekaligus sehingga jumlah dots adalah `Total Testimoni - 2` agar tidak menyisakan ruang kosong di akhir scroll.

*(End of Design Guidelines)*
