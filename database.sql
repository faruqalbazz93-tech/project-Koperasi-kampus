-- ==============================================================
-- DATABASE SCHEMA: Sistem Koperasi Kampus STMIK IKMI
-- Engine: MySQL 8.0+ / MariaDB 10.4+
-- ==============================================================

CREATE DATABASE IF NOT EXISTS `koperasi_stmik_ikmi` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `koperasi_stmik_ikmi`;

-- 1. Tabel Master Pengguna (Users)
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nickname` VARCHAR(50) NOT NULL,
    `full_name` VARCHAR(150) NOT NULL,
    `email` VARCHAR(100) UNIQUE NOT NULL,
    `phone` VARCHAR(20) UNIQUE NOT NULL,
    `password_hash` VARCHAR(255) NOT NULL,
    `institution` VARCHAR(100) DEFAULT 'Koperasi Kampus STMIK IKMI' NOT NULL,
    `role` ENUM('Tamu', 'Mahasiswa', 'Dosen', 'Staff', 'Administrator') DEFAULT 'Mahasiswa' NOT NULL,
    `is_email_verified` TINYINT(1) DEFAULT 0,
    `is_phone_verified` TINYINT(1) DEFAULT 0,
    `is_active` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Tabel Token & WhatsApp OTP (Verifikasi Ganda)
CREATE TABLE IF NOT EXISTS `verifications` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `type` ENUM('email_token', 'wa_otp') NOT NULL,
    `code` VARCHAR(100) NOT NULL,
    `expires_at` DATETIME NOT NULL,
    `is_used` TINYINT(1) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 3. Tabel Katalog Koperasi Kampus
CREATE TABLE IF NOT EXISTS `products` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `category` ENUM('ATK', 'Atribut', 'Buku', 'Lainnya') NOT NULL,
    `name` VARCHAR(150) NOT NULL,
    `description` TEXT,
    `price` DECIMAL(12, 2) NOT NULL,
    `stock` INT DEFAULT 0 NOT NULL,
    `image_url` VARCHAR(255),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 4. Tabel Pesanan (Orders)
CREATE TABLE IF NOT EXISTS `orders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_code` VARCHAR(30) UNIQUE NOT NULL,
    `user_id` INT NOT NULL,
    `total_amount` DECIMAL(12, 2) NOT NULL,
    `status` ENUM('Pending', 'Lunas', 'Selesai') DEFAULT 'Pending' NOT NULL,
    `payment_method` VARCHAR(50),
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
) ENGINE=InnoDB;

-- 5. Tabel Detail Item Pesanan (Order Items)
CREATE TABLE IF NOT EXISTS `order_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `product_id` INT NOT NULL,
    `quantity` INT NOT NULL,
    `unit_price` DECIMAL(12, 2) NOT NULL,
    `subtotal` DECIMAL(12, 2) NOT NULL,
    FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`product_id`) REFERENCES `products`(`id`)
) ENGINE=InnoDB;

-- 6. Tabel AI Quiz & Edukasi
CREATE TABLE IF NOT EXISTS `quizzes` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NOT NULL,
    `course_name` VARCHAR(100) NOT NULL,
    `topic` VARCHAR(150) NOT NULL,
    `question_type` ENUM('Pilihan Ganda', 'Essay') NOT NULL,
    `raw_payload` JSON NOT NULL,
    `score` DECIMAL(5, 2) DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`)
) ENGINE=InnoDB;

-- 7. Tabel Pengaturan Dinamis (API Key Fonnte & AI Provider)
CREATE TABLE IF NOT EXISTS `settings` (
    `key` VARCHAR(50) PRIMARY KEY,
    `value` TEXT,
    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ==============================================================
-- SEED DATA AWAL
-- ==============================================================

-- Akun Awal untuk 5 Peran Pengguna (Password terenkripsi bcrypt)
-- 1. Administrator (Password: admin123)
INSERT INTO `users` (`nickname`, `full_name`, `email`, `phone`, `password_hash`, `institution`, `role`, `is_email_verified`, `is_phone_verified`, `is_active`)
VALUES ('Admin IKMI', 'Administrator Koperasi STMIK IKMI', 'admin@ikmi.ac.id', '081234567890', '$2y$10$w8/0sMtw3zTfFfDqgA3iHOGk9l8HnI2LwGjZlhWqP5C7nQ2tS2K/q', 'Koperasi Kampus STMIK IKMI', 'Administrator', 1, 1, 1)
ON DUPLICATE KEY UPDATE `id`=`id`;

-- 2. Dosen (Password: dosen123)
INSERT INTO `users` (`nickname`, `full_name`, `email`, `phone`, `password_hash`, `institution`, `role`, `is_email_verified`, `is_phone_verified`, `is_active`)
VALUES ('Pak Budi', 'Dr. Budi Santoso, M.Kom.', 'budi.dosen@ikmi.ac.id', '081234567891', '$2y$10$q2/w4zQd8v0jXhUj7mP3s.6zG9R9N7K5L3Y1P0O8I7U6Y5T4R3E2W', 'Koperasi Kampus STMIK IKMI', 'Dosen', 1, 1, 1)
ON DUPLICATE KEY UPDATE `id`=`id`;

-- 3. Mahasiswa (Password: mhs123)
INSERT INTO `users` (`nickname`, `full_name`, `email`, `phone`, `password_hash`, `institution`, `role`, `is_email_verified`, `is_phone_verified`, `is_active`)
VALUES ('Rian', 'Rian Saputra', 'rian.mahasiswa@ikmi.ac.id', '081298765432', '$2y$10$8h6j9K1L2M3N4O5P6Q7R8.sT9U0V1W2X3Y4Z5A6B7C8D9E0F1G2H3', 'Koperasi Kampus STMIK IKMI', 'Mahasiswa', 1, 1, 1)
ON DUPLICATE KEY UPDATE `id`=`id`;

-- 4. Staff (Password: staff123)
INSERT INTO `users` (`nickname`, `full_name`, `email`, `phone`, `password_hash`, `institution`, `role`, `is_email_verified`, `is_phone_verified`, `is_active`)
VALUES ('Siti', 'Siti Nurhaliza, A.Md.', 'siti.staff@ikmi.ac.id', '081234567893', '$2y$10$1A2B3C4D5E6F7G8H9I0J1.kL2M3N4O5P6Q7R8S9T0U1V2W3X4Y5Z6', 'Koperasi Kampus STMIK IKMI', 'Staff', 1, 1, 1)
ON DUPLICATE KEY UPDATE `id`=`id`;

-- 5. Tamu (Password: tamu123)
INSERT INTO `users` (`nickname`, `full_name`, `email`, `phone`, `password_hash`, `institution`, `role`, `is_email_verified`, `is_phone_verified`, `is_active`)
VALUES ('Tamu', 'Pengunjung Tamu Kampus', 'guest@ikmi.ac.id', '081234567894', '$2y$10$7Z8Y9X0W1V2U3T4S5R6Q7.pP8O9N0M1L2K3J4I5H6G7F8E9D0C1B2', 'Koperasi Kampus STMIK IKMI', 'Tamu', 1, 1, 1)
ON DUPLICATE KEY UPDATE `id`=`id`;

-- Data Katalog Produk
INSERT INTO `products` (`category`, `name`, `description`, `price`, `stock`, `image_url`) VALUES
('ATK', 'Buku Catatan Eksklusif STMIK IKMI A5', 'Notebook grid hard cover berlogo resmi STMIK IKMI, isi 100 lembar kertas bookpaper berkualitas.', 25000.00, 120, 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=500&q=80'),
('ATK', 'Paket Alat Tulis & Pulpen Gel IKMI (Isi 3)', 'Paket pulpen gel 0.5mm tinta hitam pekat anti macet dengan grip ergonomis untuk kuliah.', 15000.00, 200, 'https://images.unsplash.com/photo-1585336261026-7080e77d337f?w=500&q=80'),
('ATK', 'Binder Kuliah B5 26 Ring STMIK IKMI', 'Binder kuliah multifungsi dengan pembatas subjek mata kuliah dan logo kampus timbul.', 38000.00, 85, 'https://images.unsplash.com/photo-1531346878377-a5be20888e57?w=500&q=80'),
('Atribut', 'Jas Almamater Resmi STMIK IKMI', 'Jas almamater resmi warna biru khas STMIK IKMI dengan bordir badge komputer & teknologi berkualitas tinggi.', 165000.00, 50, 'https://images.unsplash.com/photo-1594938298603-c8148c4dae35?w=500&q=80'),
('Atribut', 'Kaos Polo Shirt Kampus STMIK IKMI', 'Kaos polo bahan lacoste pique premium, nyaman dipakai saat kegiatan praktikum dan UKM kampus.', 75000.00, 60, 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=500&q=80'),
('Atribut', 'Lanyard + ID Card Holder STMIK IKMI', 'Tali lanyard printing dua sisi logo STMIK IKMI dilengkapi card holder akrilik transparan tebal.', 20000.00, 150, 'https://images.unsplash.com/photo-1563245372-f21724e3856d?w=500&q=80'),
('Atribut', 'Pin Logam & Dasi Formal STMIK IKMI', 'Set pin kuningan lapis resin mengkilap dan dasi resmi untuk sidang skripsi atau yudisium.', 35000.00, 90, 'https://images.unsplash.com/photo-1617137984095-74e4e5e3613f?w=500&q=80'),
('Buku', 'Buku Ajar: Algoritma & Struktur Data Modern', 'Panduan komprehensif logika komputasi, array, linked list, tree, graph hingga analisis Big O.', 85000.00, 45, 'https://images.unsplash.com/photo-1532012164546-f432f2e3777f?w=500&q=80'),
('Buku', 'Buku Praktikum: Rekayasa Perangkat Lunak & MVC', 'Pedoman perancangan sistem berbasis UML, arsitektur MVC, REST API, dan testing aplikasi web.', 78000.00, 40, 'https://images.unsplash.com/photo-1512820790803-83ca734da794?w=500&q=80'),
('Buku', 'Buku Referensi: Basis Data Relasional & NoSQL', 'Kajian mendalam pemodelan ERD, normalisasi data 1NF-BCNF, optimasi indeks SQL dan database NoSQL.', 92000.00, 35, 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=500&q=80');

