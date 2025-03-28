# Sistem Informasi Data Tabungan Siswa

Sistem Informasi Data Tabungan Siswa adalah aplikasi yang dikembangkan untuk mempermudah proses pengelolaan tabungan siswa di Sekolah Dasar Negeri Sukarame. Aplikasi ini dirancang sebagai Progressive Web App (PWA) dan terintegrasi dengan payment gateway untuk memfasilitasi transaksi secara digital.

## Fitur Utama

- **Manajemen Data Siswa:** Menyimpan dan mengelola informasi lengkap mengenai siswa.
- **Pencatatan Transaksi Tabungan:** Mencatat setiap setoran dan penarikan tabungan siswa secara detail.
- **Laporan Keuangan:** Menyediakan laporan keuangan yang dapat diakses oleh admin dan pihak terkait.
- **Integrasi Payment Gateway:** Memungkinkan transaksi digital untuk setoran dan penarikan tabungan.
- **Progressive Web App (PWA):** Aplikasi dapat diakses melalui browser dan dapat diinstal seperti aplikasi native di perangkat pengguna.

## Instalasi

Ikuti langkah-langkah berikut untuk menginstal dan menjalankan aplikasi ini secara lokal:

1. **Kloning Repositori:**
   ```bash
   git clone https://github.com/anggarivian/Tabungan_App_V2.git
   ```

2. **Masuk ke Direktori Proyek:**
   ```bash
   cd Tabungan_App_V2
   ```

3. **Salin File Konfigurasi Lingkungan:**
   ```bash
   cp .env.example .env
   ```

4. **Instal Dependensi Composer:**
   ```bash
   composer install
   ```

5. **Instal Dependensi NPM:**
   ```bash
   npm install
   ```

6. **Generate Application Key:**
   ```bash
   php artisan key:generate
   ```

7. **Konfigurasi Database:**
   Edit file `.env` dan sesuaikan pengaturan database sesuai dengan konfigurasi Anda.

8. **Migrasi dan Seed Database:**
   ```bash
   php artisan migrate --seed
   ```

9. **Jalankan Server Pengembangan:**
   ```bash
   php artisan serve
   ```

10. **Akses Aplikasi:**
    Buka browser dan akses `http://localhost:8000` untuk melihat aplikasi berjalan.

## Kontribusi

Kami menyambut kontribusi dari siapa pun yang tertarik untuk meningkatkan aplikasi ini. Silakan fork repositori ini dan buat pull request dengan perubahan yang Anda usulkan.

## Lisensi

Aplikasi ini dilisensikan di bawah [MIT License](LICENSE).

