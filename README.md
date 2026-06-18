---
title: pusakap-dashboard
emoji: 📚
colorFrom: blue
colorTo: indigo
sdk: docker
pinned: false
---

# 📚 LITERA-SCAN - Live Circulation Dashboard & IoT Smart Library

LITERA-SCAN adalah sistem manajemen sirkulasi perpustakaan pintar berbasis IoT yang mengintegrasikan pembaca kartu RFID (ESP32) dengan sistem backend kustom Laravel. Proyek ini memfasilitasi pencatatan peminjaman dan pengembalian buku secara otomatis melalui pemindaian tag RFID serta visualisasi sirkulasi real-time melalui dashboard interaktif.

---

## 🛠️ Tech Stack & Hardware

### Software Stack
* **Framework Backend:** Laravel 11 (PHP 8.2+)
* **Database:** MySQL
* **Frontend:** Tailwind CSS (Dark Theme, Glassmorphism)
* **Protokol Komunikasi:** REST API (JSON)
* **State Management:** Laravel Cache

### Hardware Stack (IoT)
* **Mikrokontroler:** ESP32 DevKit V1
* **RFID Reader:** MFRC522
* **Display:** SSD1306 OLED Display (128x64 I2C)
* **Indikator Suara/Cahaya:** Active Buzzer & LED

---

## 🚀 Fitur Utama

1. **Sesi Pemindaian Otomatis (10 Detik Timeout):**
   * Mahasiswa menempelkan kartu anggota untuk mengaktifkan sesi sirkulasi (disimpan di cache backend selama 10 detik).
   * Menempelkan kartu yang sama kembali saat sesi aktif akan menutup sesi (`RESET_STANDBY`).
2. **Transaksi Sirkulasi Otomatis:**
   * Jika buku berstatus **Tersedia (`available`)** ditempelkan saat sesi aktif, sistem mencatat transaksi peminjaman (`borrow`) dan mengubah status buku menjadi `borrowed`.
   * Jika buku berstatus **Dipinjam (`borrowed`)** ditempelkan saat sesi aktif, sistem memperbarui transaksi menjadi pengembalian (`return`) dan mengembalikan status buku menjadi `available`.
3. **Live Dashboard:**
   * Menampilkan metrik perpustakaan (Total Anggota, Total Buku, Buku Tersedia, Buku Dipinjam).
   * Log transaksi terkini dengan pembaruan dinamis tanpa memuat ulang halaman (Polling API setiap 1 detik).
   * Panel status sesi real-time yang menampilkan informasi anggota aktif dan hitung mundur sisa waktu sesi.

---

## 💾 Skema Database

Sistem ini menggunakan tiga tabel utama untuk menangani sirkulasi:

### 1. Tabel `students`
| Kolom | Tipe Data | Keterangan |
|---|---|---|
| `id` | BIGINT UNSIGNED | Primary Key, Auto Increment |
| `rfid_uid` | VARCHAR(50) | Unique & Indexed (UID Kartu Anggota) |
| `nim` | VARCHAR(20) | Unique (Nomor Induk Mahasiswa) |
| `name` | VARCHAR(100) | Nama Mahasiswa |
| `major` | VARCHAR(100) | Program Studi |

### 2. Tabel `books`
| Kolom | Tipe Data | Keterangan |
|---|---|---|
| `id` | BIGINT UNSIGNED | Primary Key, Auto Increment |
| `rfid_uid` | VARCHAR(50) | Unique & Indexed (UID Tag Buku) |
| `title` | VARCHAR(150) | Judul Buku |
| `author` | VARCHAR(100) | Penulis Buku |
| `status` | ENUM('available', 'borrowed') | Default: 'available' |

### 3. Tabel `transactions`
| Kolom | Tipe Data | Keterangan |
|---|---|---|
| `id` | BIGINT UNSIGNED | Primary Key, Auto Increment |
| `student_id` | BIGINT UNSIGNED | Foreign Key (relasi ke `students.id`) |
| `book_id` | BIGINT UNSIGNED | Foreign Key (relasi ke `books.id`) |
| `type` | ENUM('borrow', 'return') | Jenis Transaksi |
| `borrowed_at` | TIMESTAMP | Waktu Peminjaman |
| `returned_at` | TIMESTAMP (Nullable) | Waktu Pengembalian |

---

## 📡 Rute API (Endpoint IoT)

Sistem Laravel menyediakan dua endpoint utama yang diakses oleh perangkat ESP32:

### 1. `POST /api/rfid-scan`
Menerima payload JSON dari ESP32 untuk mendeteksi pemindaian kartu/buku:
* **Request Payload:**
  ```json
  {
    "uid": "A2 42 E2 2E"
  }
  ```
* **Respons Sukses (Sesi Mahasiswa Aktif):**
  ```json
  {
    "status": "session_active",
    "line1": "Halo, Ayu!",
    "line2": "Silakan tap buku"
  }
  ```

### 2. `GET /api/session-status`
Digunakan oleh sistem untuk mengecek apakah sesi aktif masih berlaku. Jika timeout (melebihi 10 detik), mengembalikan perintah reset:
* **Response Payload:**
  ```json
  {
    "command": "RESET_STANDBY",
    "status": "timeout"
  }
  ```

---

## ⚙️ Cara Menjalankan Proyek

### 1. Kloning & Persiapan Dependensi
```bash
git clone https://github.com/Pirjakun/LiteraScan.git
cd LiteraScan
composer install
npm install
```

### 2. Konfigurasi Environment (`.env`)
Salin file `.env.example` menjadi `.env`, lalu sesuaikan kredensial MySQL Anda:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=literascan_db
DB_USERNAME=root
DB_PASSWORD=
```

### 3. Migrasi & Seeding Data Awal
Jalankan migrasi database beserta pengisian data simulasi RFID:
```bash
php artisan migrate --seed
```
*Data simulasi yang akan dimasukkan:*
* **Ayu** (RFID: `A2 42 E2 2E`)
* **Budi** (RFID: `C6 A5 E2 2E`)
* **Pirja_Admin** (RFID: `5F E5 97 C2`)
* **Buku: Tips & Trick Excel** (RFID: `04 E5 D7 08 C1 2A 81`)

### 4. Menjalankan Server Lokal
Jalankan server pengembangan Laravel:
```bash
php artisan serve
```
Akses dashboard di browser Anda melalui alamat [http://127.0.0.1:8000](http://127.0.0.1:8000).
