# Sistem Koperasi Kampus STMIK IKMI
### E-Commerce Koperasi & AI Academic Engine

Aplikasi web terpadu untuk **Koperasi Kampus STMIK IKMI** yang menggabungkan pengelolaan kebutuhan perkuliahan (ATK, Atribut Almamater, Buku Referensi) dengan akselerator pembelajaran cerdas berbasis AI (**AI Quiz Generator** & **AI Academic Explainer**).

---

## 🏛️ 1. Identitas Institusi & Peran Pengguna
- **Institusi**: Secara otomatis dikunci ke `Koperasi Kampus STMIK IKMI`.
- **Peran Pengguna (User Roles)**:
  - `Tamu` (Guest)
  - `Mahasiswa`
  - `Dosen`
  - `Staff`
  - `Administrator`

---

## 🔐 2. Modul Autentikasi & Verifikasi Ganda (Dual Verification)
- **Data Pendaftaran Wajib**:
  - Nama Panggilan (`nickname`)
  - Nama Lengkap (`full_name`)
  - Email Kampus (`email`)
  - Nomor WhatsApp Aktif (`phone`)
- **Verifikasi WhatsApp via Fonnte Gateway (Tanpa Verifikasi Email)**:
  1. Pengguna mendaftar $\to$ sistem memicu pengiriman kode **WhatsApp OTP (6 digit)** menggunakan **Fonnte API**.
  2. Pengguna menginputkan kode OTP di layar verifikasi.
  3. Akun langsung aktif penuh (`is_active = 1`).
  4. Dilengkapi *Sandbox Simulation Log* jika token Fonnte belum dimasukkan di menu pengaturan.

---

## ⚙️ 3. Pengaturan API Key Dinamis (Admin Settings)
- Halaman admin khusus: `/admin/settings`
- Menyimpan konfigurasi API ke tabel `settings` database secara dinamis:
  - **Fonnte API Token** (WhatsApp Gateway)
  - **AI Provider** (Google Gemini / OpenAI)
  - **AI API Key** (Gemini API Key / OpenAI Secret Key)
  - Dilengkapi fitur pengujian pesan WhatsApp (*Test Sender*) langsung ke nomor ponsel tujuan.

---

## 🤖 4. AI Chatbot Berbasis Database (RAG / Text-to-SQL Grounding)
- Halaman mandiri: `/chatbot`
- Chatbot terhubung langsung ke data operasional riil di basis data MySQL:
  - **Cek Stok & Produk**: Membaca tabel `products` (harga, kategori, sisa stok).
  - **Rekap Transaksi**: Membaca tabel `orders` (omzet lunas, jumlah pesanan pending).
  - **Materi Akademik & Kuis**: Membaca tabel `quizzes` untuk pembahasan soal dan silabus kurikulum.
- Bekerja secara otomatis menggunakan Local Analytical Engine (tanpa API key eksternal) maupun integrasi LLM Gemini (bila API key diisi di settings).

---

## 🛒 5. Modul Koperasi Kampus (E-Commerce) & Notifikasi WA
- **Katalog Produk**:
  - **ATK**: Buku catatan IKMI A5, Paket Pulpen Gel, Binder B5 26 Ring.
  - **Atribut**: Jas Almamater Resmi STMIK IKMI, Kaos Polo, Lanyard ID Card, Pin Logam & Dasi.
  - **Buku**: Buku Algoritma & Struktur Data, Rekayasa Perangkat Lunak MVC, Basis Data Relasional.
- **Notifikasi Otomatis WhatsApp (Fonnte)**:
  - Notifikasi saat pesanan baru dibuat (`Pending`).
  - Notifikasi saat kasir memvalidasi pembayaran (`Lunas`).
  - Notifikasi saat barang resmi diserahkan (`Selesai`).

---

## 🧠 4. Modul Edukasi & AI (Core Features)
- **AI Quiz Generator**:
  - Mahasiswa/Dosen memilih Mata Kuliah, Topik spesifik, dan Tipe Soal (**Pilihan Ganda** atau **Essay**).
  - Menghasilkan butir soal terstruktur, kunci jawaban, dan rubrik penilaian otomatis.
  - Evaluasi kuis real-time dengan penghitungan skor instan.
- **AI Academic Explainer**:
  - Tombol **"Bedah Konsep (AI Explainer)"** pada setiap butir soal kuis.
  - Membedah teori fundamental, analogi sistematis, pembuktian formula/rumus komputasi (misal $O(\log n)$ pada AVL Tree), dan cuplikan algoritma.
  - Fitur mandiri **AI Academic Explainer & Concept Explorer** untuk pencarian konsep bebas.

---

## 💻 5. Panduan Menjalankan Aplikasi

### Akses Melalui Web Server Apache (XAMPP):
1. Pastikan folder berada di: `c:\xampp\htdocs\Koperasi Kampus`
2. Buka browser dan akses:
   ```text
   http://localhost/Koperasi%20Kampus/
   ```
   *(Atau `http://localhost/Koperasi%20Kampus/public/`)*

### Akun Demo Pengujian (Tiap Role):
| Peran (Role) | Nama Pengguna / Panggilan | Email Login | Kata Sandi (Password) | Hak Akses Utama |
| :--- | :--- | :--- | :--- | :--- |
| **Administrator** | Admin IKMI | `admin@ikmi.ac.id` | `admin123` | Dashboard Admin, Ubah Status Order (Pending $\to$ Lunas $\to$ Selesai), Kelola Produk |
| **Dosen** | Dr. Budi Santoso, M.Kom. (Pak Budi) | `budi.dosen@ikmi.ac.id` | `dosen123` | Belanja Koperasi, AI Quiz Generator (Mode Dosen), AI Academic Explainer |
| **Mahasiswa** | Rian Saputra (Rian) | `rian.mahasiswa@ikmi.ac.id` | `mhs123` | Belanja Kebutuhan Kuliah, Checkout, Simulasi Kuis AI Interaktif, Bedah Konsep |
| **Staff** | Siti Nurhaliza, A.Md. (Siti) | `siti.staff@ikmi.ac.id` | `staff123` | Belanja Koperasi, Pelacakan Pesanan, Akses Modul Edukasi |
| **Tamu** | Pengunjung Kampus (Tamu) | `guest@ikmi.ac.id` | `tamu123` | Eksplorasi Katalog Koperasi, Coba AI Quiz & Explainer |

---
**STMIK IKMI Cirebon** &bull; *Smart Campus System Architecture*

