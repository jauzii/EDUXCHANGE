# 🎓 EDUXCHANGE

> Learn, Share, Grow

EDUXCHANGE adalah platform pembelajaran digital yang membantu siswa dan mahasiswa meningkatkan keterampilan melalui kursus online yang fleksibel, terjangkau, dan berkualitas.

---

## 🚀 Tentang Proyek

EDUXCHANGE dikembangkan sebagai platform edukasi digital yang memungkinkan pengguna untuk:

- Mencari kursus sesuai kebutuhan
- Melakukan pembelian kursus secara online
- Mengakses materi pembelajaran
- Mengikuti pembelajaran secara fleksibel
- Mendapatkan sertifikat setelah menyelesaikan kursus

---

## ✨ Fitur Utama

### 👤 User

- Registrasi akun
- Login dan Logout
- Melihat daftar kursus
- Melihat detail kursus
- Melakukan pembelian kursus
- Mengakses kursus yang telah dibeli
- Melihat progres pembelajaran
- Mendapatkan sertifikat kursus

### 👨‍💼 Admin

- Mengelola data kursus
- Mengelola data tutor
- Mengelola transaksi
- Mengelola materi pembelajaran
- Memantau aktivitas pengguna

---
## 🛠️ Teknologi yang Digunakan

### Backend

- Laravel 12
- PHP 8.2

### Frontend

- Blade Template
- Tailwind CSS
- Vite
- JavaScript

### Database

- MySQL

### Tools

- Git
- GitHub
- Visual Studio Code
- Composer
- Node.js

---

## 📂 Struktur Database

Tabel utama yang digunakan:

- users
- tutors
- courses
- materials
- enrollments
- transactions
- questions
- quiz_answers
- certificates

---

## ⚙️ Cara Instalasi

### 1. Clone Repository

```bash
git clone https://github.com/USERNAME/REPOSITORY.git
```

### 2. Masuk ke Folder Project

```bash
cd PROJECT-1-TI
```

### 3. Install Dependency PHP

```bash
composer install
```

### 4. Install Dependency Frontend

```bash
npm install
```

### 5. Copy File Environment

```bash
copy .env.example .env
```

atau

```bash
cp .env.example .env
```

### 6. Generate Application Key

```bash
php artisan key:generate
```

### 7. Konfigurasi Database

Edit file:

```env
.env
```

Sesuaikan konfigurasi database:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=eduxchange
DB_USERNAME=root
DB_PASSWORD=
```

### 8. Jalankan Migrasi

```bash
php artisan migrate
```

### 9. Jalankan Vite

```bash
npm run dev
```

### 10. Jalankan Laravel

```bash
php artisan serve
```

Akses aplikasi melalui:

```text
http://127.0.0.1:8000
```

---

## 📱 Cara Penggunaan

1. Registrasi akun
2. Login ke sistem
3. Pilih kursus yang diinginkan
4. Lakukan pembayaran
5. Akses materi pembelajaran
6. Selesaikan kursus
7. Dapatkan sertifikat

---

## 🎯 Visi

Menjadi platform pembelajaran digital terpercaya yang membantu generasi muda mengembangkan potensi dan keterampilan untuk menghadapi era digital.

---

## 👨‍💻 Pengembang

**Mochamad Putrana Yudhistira Maulidan**

Project Mata Kuliah Pemrograman Web

---

## 📄 Lisensi

Project ini dibuat untuk kebutuhan pembelajaran dan pengembangan akademik.

Terimkasih!