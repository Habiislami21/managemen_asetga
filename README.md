# 🏢 Sistem Manajemen Aset & Logistik GA (General Affairs)

[![Laravel Version](https://img.shields.io/badge/Laravel-v10.x-red.svg)](https://laravel.com)
[![PHP Version](https://img.shields.io/badge/PHP-v8.1+-blue.svg)](https://php.net)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

**Manajemen Aset GA** adalah platform web kustom yang dirancang khusus untuk memenuhi kebutuhan divisi **General Affairs (GA)** dalam mengelola aset fisik perusahaan/lembaga, pengadaan logistik divisi, aduan kerusakan, audit stok bulanan, hingga peminjaman kendaraan operasional secara terpusat dan efisien.

---

## 🚀 Fitur Utama

### 🚗 1. Sistem Peminjaman Kendaraan (Vehicle Booking System)
*   **Pengajuan Peminjaman**: Formulir pengajuan sewa/pinjam kendaraan operasional (Roda 2 & Roda 4) oleh staf/driver.
*   **Jadwal & Riwayat Real-time**: Kalender jadwal peminjaman kendaraan yang aktif guna menghindari bentrok jadwal.
*   **Persetujuan Token-based**: Admin dapat menyetujui (`Approve`) atau menolak (`Reject`) peminjaman secara instan langsung melalui URL token rahasia yang dikirimkan.
*   **Integrasi WhatsApp Gateway**: Notifikasi otomatis secara *real-time* ke WhatsApp Driver/Peminjam dan WhatsApp Group Admin menggunakan API Gateway **Fonnte** saat pengajuan dibuat, disetujui, atau ditolak.
*   **Statistik & Analitik**: Dashboard statistik peminjaman per bulan/tahun yang dilindungi otentikasi kata sandi.

### 📦 2. Manajemen Stok & Logistik Divisi
*   **Alokasi Stok**: Pelacakan stok di gudang pusat (`StokPusat`) dan stok yang tersebar di setiap divisi (`StokDivisi`).
*   **Pengajuan Stok**: Penanggung Jawab (PJ) Divisi dapat mengajukan permintaan penambahan atau pengisian ulang stok barang secara digital.
*   **Sistem Approval Berjenjang**: Alur persetujuan pengajuan stok yang melibatkan peran multi-level: **General Affairs (GA)**, **Kepala Bagian (Kabag)**, **Admin**, dan **Staf Aset**.

### 🔍 3. Audit berkala (Cek Bulanan)
*   **Rekonsiliasi Stok**: Melakukan verifikasi stok fisik berkala (Stock Take/Opname) secara bulanan per divisi.
*   **Fitur Batch Actions**: Menandai kecocokan stok secara massal (*batch mark match*) untuk mempercepat audit.
*   **Optimalisasi Tampilan Mobile**: Antarmuka web ramah perangkat seluler memudahkan petugas melakukan pemindaian (scan) dan pembaharuan data langsung dari lapangan.
*   **Analitik Prioritas**: Menyoroti barang-barang prioritas atau barang yang kritis/habis.

### 🛠️ 4. Sistem Pengaduan Kerusakan (Aduan Aset)
*   **Pelaporan Instan**: Memungkinkan staf melaporkan kerusakan fasilitas atau aset fisik perusahaan secara detail.
*   **Pelacakan Status**: Dashboard khusus Admin untuk memantau proses perbaikan, mengubah status aduan, dan melakukan tindakan cepat.

### 📊 5. Ekspor Data Dinamis
*   Mendukung ekspor data laporan lengkap ke format **Excel (.xlsx)** secara instan untuk:
    *   Stok barang keseluruhan & stok per divisi.
    *   Riwayat pengaduan kerusakan (Aduan).
    *   Riwayat pengajuan logistik (Ajuan Rutin / Ajuan Final).
    *   Hasil audit cek bulanan.

---

## 🛠️ Spesifikasi Teknologi

*   **Framework Core:** Laravel v10.x
*   **Bahasa Pemrograman:** PHP >= 8.1
*   **Database:** MySQL / MariaDB
*   **Frontend UI:** Vanilla CSS, Bootstrap 5, FontAwesome Icons (Responsive layout)
*   **Ekspor File:** Maatwebsite/Laravel-Excel
*   **WhatsApp Gateway:** Fonnte API Integration

---

## ⚙️ Panduan Instalasi & Konfigurasi

Ikuti langkah-langkah berikut untuk menjalankan proyek ini di lingkungan lokal Anda:

### 1. Kloning Repositori
```bash
git clone https://github.com/Habiislami21/managemen_asetga.git
cd managemen_asetga
```

### 2. Pasang Dependensi PHP
```bash
composer install
```

### 3. Konfigurasi Lingkungan (`.env`)
Salin file konfigurasi contoh `.env.example` menjadi `.env`:
```bash
cp .env.example .env
```
Buka file `.env` yang baru dibuat dan sesuaikan konfigurasi database Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=managemen_asetga
DB_USERNAME=root
DB_PASSWORD=your_password
```

Lengkapi juga konfigurasi **WhatsApp Gateway (Fonnte)** jika ingin mengaktifkan fitur notifikasi otomatis:
```env
FONTEE_API_URL=https://api.fonnte.com/send
FONTEE_API_KEY=your_fonnte_api_key
FONTEE_WHATSAPP_FROM=your_sender_whatsapp_number
FONTEE_WHATSAPP_GROUP_ID=your_admin_group_chat_id
```

### 4. Buat Application Key
```bash
php artisan key:generate
```

### 5. Jalankan Migrasi & Seeder Database
Gunakan perintah berikut untuk membangun struktur database beserta data awal (users, divisi, kendaraan, dll.):
```bash
php artisan migrate --seed
```
*Catatan: Jika Anda ingin me-refresh database secara bersih dan mengisinya kembali dari awal:*
```bash
php artisan migrate:fresh --seed
```

### 6. Jalankan Server Lokal
```bash
php artisan serve
```
Aplikasi Anda kini dapat diakses melalui browser di alamat [http://127.0.0.1:8000](http://127.0.0.1:8000).

---

## 👥 Hak Akses Pengguna (Roles)
Sistem ini menggunakan pembagian otorisasi pengguna berbasis peran (*roles*):
1.  **Admin:** Memiliki kontrol penuh atas seluruh sistem, manajemen pengguna, analisis data, ekspor laporan, dan reset audit bulanan.
2.  **GA (General Affairs) / Kabag:** Berperan dalam menyetujui pengajuan stok barang divisi dan peminjaman aset/kendaraan operasional.
3.  **Aset / Staff:** Mengelola distribusi stok fisik dan meninjau laporan pengaduan kerusakan.
4.  **PJ Divisi (Penanggung Jawab Divisi):** Mengajukan permintaan stok logistik dan mengelola persediaan internal divisi.

---

## 📄 Lisensi
Sistem ini bersifat sumber terbuka (*open-source*) di bawah lisensi [MIT License](LICENSE).
