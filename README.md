# 📌 Sistem Informasi Persuratan Akademik Berbasis Web

<p align="center">
  <img src="https://img.shields.io/badge/Laravel-12-FF2D20?logo=laravel&logoColor=white" />
  <img src="https://img.shields.io/badge/PHP-8.2-777BB4?logo=php&logoColor=white" />
  <img src="https://img.shields.io/badge/TailwindCSS-4-38BDF8?logo=tailwindcss&logoColor=white" />
  <img src="https://img.shields.io/badge/Vite-Build-646CFF?logo=vite&logoColor=white" />
  <img src="https://img.shields.io/badge/MySQL-Database-4479A1?logo=mysql&logoColor=white" />
  <img src="https://img.shields.io/badge/Role%20Management-Spatie-blue" />
  <img src="https://img.shields.io/badge/PHPWord-Document-8892BF" />
  <img src="https://img.shields.io/badge/PDF-DomPDF-red" />
  <img src="https://img.shields.io/badge/QR%20Code-Endroid-green" />
  <img src="https://img.shields.io/badge/Status-Development-F59E0B" />
</p>

---

## 📖 Deskripsi Sistem

<p align="justify">Sistem Informasi Persuratan Akademik Berbasis Web merupakan solusi digital yang dirancang untuk mengelola seluruh proses persuratan akademik secara terpadu dan terstruktur di lingkungan perguruan tinggi di Indonesia.</p>

<p align="justify">Sistem ini dikembangkan untuk menggantikan proses semi-manual yang sebelumnya menggunakan berbagai platform terpisah seperti Google Form, Google Spreadsheet, dan Microsoft Word menjadi satu platform terintegrasi. Sistem ini memfasilitasi proses pengajuan, verifikasi, persetujuan, hingga penerbitan surat akademik secara otomatis dan efisien.</p>

<p align="justify">Dengan adanya sistem ini, seluruh proses persuratan menjadi lebih cepat, transparan, dan terdokumentasi dengan baik dalam satu basis data terpusat.</p>

---

## 🎯 Fitur Utama

- Pengajuan surat akademik secara online
- Workflow persetujuan multi-level (bertingkat)
- Verifikasi dan validasi oleh pihak akademik
- Generate dokumen otomatis (PDF & DOCX)
- Tanda tangan elektronik (TTE)
- QR Code untuk validasi keaslian dokumen
- Tracking status pengajuan secara real-time
- Arsip surat terpusat dan terdokumentasi

---

## 📄 Jenis Surat

- Surat Keterangan Aktif Kuliah
- Surat Permohonan Penelitian
- Surat Dispensasi Perkuliahan
- Surat Dispensasi Mahasiswa

---

## 🚀 Tech Stack

- Laravel 12
- PHP 8.2+
- Tailwind CSS
- Vite
- MySQL / MariaDB

---

## 📦 Package / Library

- spatie/laravel-permission → Role & Permission
- barryvdh/laravel-dompdf → Generate PDF
- endroid/qr-code → QR Code
- phpoffice/phpword → Export DOCX

---

## ⚙️ Requirements

- PHP >= 8.2
- Composer
- Node.js >= 18
- NPM
- MySQL / MariaDB

---

## 🛠️ Cara Instalasi

### 1️⃣ Clone Repository

```bash
git clone https://github.com/username/nama-project.git
cd nama-project
```

---

### 2️⃣ Install Dependency PHP

```bash
composer install
```

---

### 3️⃣ Install Dependency Frontend

```bash
npm install
```

---

### 4️⃣ Konfigurasi Environment

```bash
cp .env.example .env
```

Sesuaikan database di `.env`:

```
DB_DATABASE=nama_database
DB_USERNAME=root
DB_PASSWORD=
```

---

### 5️⃣ Generate Application Key

```bash
php artisan key:generate
```

---

### 6️⃣ Migrasi Database

```bash
php artisan migrate
```

---

### 7️⃣ Jalankan Seeder

```bash
php artisan db:seed
```

---

## ▶️ Menjalankan Aplikasi

### Jalankan Backend

```bash
php artisan serve
```

---

### Jalankan Frontend (Vite)

```bash
npm run dev
```

---

### Akses Aplikasi

```
http://127.0.0.1:8000
```

---

## 🔐 Role & Permission

Sistem menggunakan:

- Spatie Laravel Permission

Role utama dalam sistem:

- Mahasiswa
- Staf Akademik
- Kepala Subbagian
- Pimpinan (Approver)

---

## 🧪 Data Awal (Seeder)

Seeder digunakan untuk mengisi data awal seperti role dan user.

Contoh akun default:

```
Email: administrator@gmail.com
Password: password
```

---

## 🌟 Keunggulan Sistem

- Sistem terintegrasi dalam satu platform
- Mengurangi kesalahan manual
- Proses lebih cepat dan efisien
- Transparansi penuh (real-time tracking)
- Arsip digital terpusat

---

## 👨‍💻 Developer

Nama: Rafio Sadani  
Project: Tugas Akhir / Skripsi

---

## ⚠️ Catatan Penting

- Pastikan database sudah dibuat sebelum menjalankan migration
- Jika terjadi error permission dari Spatie, jalankan ulang seeder
- Gunakan `npm run build` untuk mode production
