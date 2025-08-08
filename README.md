## Mini ERP - Solusi ERP Cerdas untuk UKM Masa Kini 🚀

Apa itu Mini ERP?

Mini ERP adalah aplikasi web berbasis **Laravel 12** yang diciptakan khusus untuk usaha kecil dan menengah (UKM) yang pengen digitalisasi proses bisnis mereka. Dari **manajemen stok**, **pembelian**, **penjualan**, sampai **data pelanggan** — semua terintegrasi dalam satu platform yang simpel tapi powerful. 

Dengan dukungan **Filament Admin Panel**, antarmuka yang kamu dapatkan itu modern, responsif, dan super gampang dipakai, baik di desktop maupun hape. Plus, ada integrasi AI optional pakai **OpenRouter API** buat kamu yang suka teknologi cutting-edge!

---

Fitur Unggulan Mini ERP 🎯

- **User-friendly & responsif** — siap dipakai di desktop dan mobile tanpa ribet  
- **Role-based Access Control** — ada peran Admin dan Staff, tiap orang dapat hak akses sesuai tugasnya  
- **Manajemen stok real-time** — stok barang ter-update otomatis, enggak bakal salah hitung lagi  
- **Otomatisasi proses pembelian & penjualan** — hemat waktu, minim kesalahan  
- **Dashboard pintar** — monitoring bisnis jadi lebih gampang dan cepat  
- **Integrasi AI via OpenRouter API** — fitur cerdas untuk support keputusan bisnis (opsional)  

---

Teknologi Keren yang Dipakai 🔧

- **Laravel 12** (framework backend terkini dan powerful)  
- **PHP 8.2** (versi terbaru biar performa ngebut)  
- **Filament Admin Panel** (UI admin modern dan mudah di-customize)  
- **OpenRouter API** (untuk fitur AI, bisa aktifkan kalau mau, daftar di https://openrouter.ai)  

---

Spesifikasi Sistem 🖥️

- PHP minimal 8.2  
- Composer (package manager PHP)  
- Database: MySQL / PostgreSQL / SQLite  
- Web server: Apache / Nginx / atau Laravel built-in server  

---

Cara Instalasi & Setup 🛠️

1. Clone repo ini:
   ```bash
   git clone https://github.com/FadliBilal/Filament-Mini-ERP.git
   cd Filament-Mini-ERP
   ```

2. Install dependencies:
   ```bash
   composer install
   ```

3. Copy file environment dan sesuaikan konfigurasi:
   ```bash
   cp .env.example .env
   ```

4. Edit file `.env`, atur koneksi database dan masukkan API key OpenRouter 

5. Generate app key Laravel:
   ```bash
   php artisan key:generate
   ```

6. Migrasi database sekaligus isi data awal:
   ```bash
   php artisan migrate --seed
   ```

7. Jalankan server lokal Laravel:
   ```bash
   php artisan serve
   ```

8. Buka browser dan akses:
   ```
   http://localhost:8000
   ```

---

Cara Pakai & Tips 💡

- Login dengan akun **Admin** untuk akses penuh semua fitur  
- Buat akun **Staff** untuk kebutuhan operasional harian  
- Pantau stok dan transaksi melalui dashboard real-time  
- Aktifkan fitur AI via OpenRouter untuk insight bisnis yang lebih pintar  
- Rutin backup database supaya data bisnis kamu aman  

---

Catatan Penting ⚠️

- Pastikan PHP dan Composer sudah terinstall dengan versi terbaru  
- Daftar API key di https://openrouter.ai kalau mau pakai fitur AI  
- Gunakan web server yang mendukung PHP 8.2+ untuk performa optimal  
- Backup data secara berkala, ya!  

---

Butuh Bantuan? 🤝

Kalau ada pertanyaan, bug, atau mau kontribusi, langsung aja buka **Issues** di repo ini atau kontak saya lewat email di profil GitHub.

---

**Terima kasih sudah pakai Mini ERP!**  
Semoga ini jadi solusi tepat untuk bikin bisnis UKM kamu makin maju dan efisien. ✨
