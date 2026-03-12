<p align="center">
  <img src="https://res.cloudinary.com/workstation-/image/upload/f_auto,q_auto/oemah-bu-liek/bu-liek" alt="Bintang Profile — Homepage Preview" width="720" />
</p>

# 🍱 Oemah Bu Liek - Restaurant Management System

> Sistem Manajemen Terpadu untuk Oemah Bu Liek. Mengelola Inventaris, SDM, Penggajian, dan Laporan Operasional dalam satu platform.

[![Laravel](https://img.shields.io/badge/Laravel-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com/)
[![Bootstrap](https://img.shields.io/badge/Bootstrap-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com/)
[![MySQL](https://img.shields.io/badge/MySQL-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)

## 🌟 Tentang Proyek

**Oemah Bu Liek Restaurant Management System** adalah solusi digital untuk menyederhanakan proses bisnis harian. Aplikasi ini dirancang untuk membantu pemilik usaha dan pegawai dalam memantau stok bahan baku, mengatur jadwal giliran kerja (shift), mencatat presensi, hingga menghasilkan laporan penggajian secara otomatis.

## ✨ Fitur Utama

- **Manajemen Inventaris**: Pantau stok bahan baku, persetujuan penyesuaian stok, dan riwayat mutasi barang.
- **Manajemen SDM & Gaji**:
    - Penjadwalan Shift Pegawai.
    - Sistem Presensi (Kehadiran).
    - Kalkulasi Gaji Pokok & Lembur otomatis.
- **Sistem Laporan**:
    - Laporan Kerugian Bahan Baku.
    - Analisis Bahan Paling Dibutuhkan.
    - Laporan Penerimaan Stok.
- **Multi-Role Access**: Akses khusus untuk Admin (Owner/Manajer) dan Pegawai.
- **Notifikasi Internal**: Pesan dan pengumuman untuk koordinasi tim.

## 📸 Screenshot / Tampilan Layar

### 🏠 Dashboard & Umum

| Dashboard Admin | Dashboard Pegawai |
| :---: | :---: |
| <img src="https://res.cloudinary.com/workstation-/image/upload/f_auto,q_auto/oemah-bu-liek/dashboard-admin" alt="Dashboard Admin" width="720" /> | <img src="https://res.cloudinary.com/workstation-/image/upload/f_auto,q_auto/oemah-bu-liek/dashboard-employee" alt="Dashboard Pegawai" width="720" /> |

### 📦 Manajemen Inventaris

| Data Bahan Baku & Stok | Persetujuan Penyesuaian Stok | Riwayat Mutasi Stok |
| :---: | :---: | :---: |
| <img src="https://res.cloudinary.com/workstation-/image/upload/f_auto,q_auto/oemah-bu-liek/list-of-raw-material" alt="Bahan Baku" width="720" /> | <img src="https://res.cloudinary.com/workstation-/image/upload/f_auto,q_auto/oemah-bu-liek/material-approval" alt="Persetujuan Stok" width="720" /> | <img src="https://res.cloudinary.com/workstation-/image/upload/f_auto,q_auto/oemah-bu-liek/stock-mutation" alt="Riwayat Stok" width="720" /> |

### 👥 Manajemen SDM & Presensi

| Pengelolaan Data User | Penjadwalan Shift Pegawai | Sistem Presensi Pegawai |
| :---: | :---: | :---: |
| <img src="https://res.cloudinary.com/workstation-/image/upload/f_auto,q_auto/oemah-bu-liek/user-data" alt="Data Pegawai" width="720" /> | <img src="https://res.cloudinary.com/workstation-/image/upload/f_auto,q_auto/oemah-bu-liek/employee-schedule" alt="Jadwal Shift" width="720" /> | <img src="https://res.cloudinary.com/workstation-/image/upload/f_auto,q_auto/oemah-bu-liek/performance-form" alt="Presensi Pegawai" width="720" /> |

### 💰 Sistem Penggajian

| Kalkulasi Gaji Pokok | Kalkulasi Gaji Lembur |
| :---: | :---: |
| <img src="https://res.cloudinary.com/workstation-/image/upload/f_auto,q_auto/oemah-bu-liek/salary-management" alt="Gaji Pokok" width="720" /> | <img src="https://res.cloudinary.com/workstation-/image/upload/f_auto,q_auto/oemah-bu-liek/overtime-salary" alt="Gaji Lembur" width="720" /> |

### 📊 Laporan Analitik Operasional

| Laporan Kerugian Bahan Rusak | Laporan Bahan Paling Dibutuhkan |
| :---: | :---: |
| <img src="https://res.cloudinary.com/workstation-/image/upload/f_auto,q_auto/oemah-bu-liek/material-lost" alt="Laporan Kerugian" width="720" /> | <img src="https://res.cloudinary.com/workstation-/image/upload/f_auto,q_auto/oemah-bu-liek/material-use" alt="Laporan Paling Dibutuhkan" width="720" /> |

| Laporan Stok Belum Terpakai | Laporan Stok Masuk Terbanyak |
| :---: | :---: |
| <img src="https://res.cloudinary.com/workstation-/image/upload/f_auto,q_auto/oemah-bu-liek/material-remaining" alt="Laporan Belum Terpakai" width="720" /> | <img src="https://res.cloudinary.com/workstation-/image/upload/f_auto,q_auto/oemah-bu-liek/material-receipt" alt="Laporan Stok Masuk" width="720" /> |


## 💻 Tech Stack

- **Backend:** Laravel 10+
- **Frontend:** Blade Templating, Vanilla CSS, Bootstrap 5
- **Database:** MySQL / MariaDB
- **Tools:** Vite, Composer, NPM

## 🚀 Panduan Instalasi

1. **Clone repositori**
    ```bash
    git clone https://github.com/username-anda/website_oemahbuliek.git
    cd website_oemahbuliek
    ```
2. **Install dependensi**
    ```bash
    composer install
    npm install
    npm run build
    ```
3. **Konfigurasi Environment**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
    _Sesuaikan pengaturan database di file `.env`._
4. **Migrasi & Seed Data**
    ```bash
    php artisan migrate --seed
    ```
5. **Jalankan aplikasi**
    ```bash
    php artisan serve
    ```

## 🔐 Akun Akses Default

Setelah menjalankan seeder, gunakan akun berikut untuk masuk:

- **Admin**: `admin@resto.test` / `password123`
- **Pegawai**: `pegawai@resto.test` / `password123`

## 📜 Lisensi

Berlisensi di bawah [MIT License](LICENSE).
