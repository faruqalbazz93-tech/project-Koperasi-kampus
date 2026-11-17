<?php

class Database {
    private static ?PDO $instance = null;
    private static string $driverUsed = 'mysql';

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $config = require APP_PATH . '/config/database.php';

            // Coba koneksi MySQL terlebih dahulu
            try {
                $dsn = "mysql:host={$config['mysql']['host']};port={$config['mysql']['port']};dbname={$config['mysql']['database']};charset={$config['mysql']['charset']}";
                self::$instance = new PDO($dsn, $config['mysql']['username'], $config['mysql']['password'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_TIMEOUT => 2
                ]);
                self::$driverUsed = 'mysql';
            } catch (Exception $e) {
                // Fallback ke SQLite agar sistem tetap 100% berjalan mandiri
                $sqlitePath = $config['sqlite']['path'];
                $dir = dirname($sqlitePath);
                if (!is_dir($dir)) {
                    mkdir($dir, 0777, true);
                }
                
                self::$instance = new PDO("sqlite:" . $sqlitePath, null, null, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
                ]);
                self::$driverUsed = 'sqlite';
            }

            self::initializeSchema();
        }

        return self::$instance;
    }

    public static function getDriver(): string {
        return self::$driverUsed;
    }

    private static function initializeSchema(): void {
        $db = self::$instance;

        if (self::$driverUsed === 'sqlite') {
            // Skema SQLite
            $db->exec("
                CREATE TABLE IF NOT EXISTS users (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    nickname TEXT NOT NULL,
                    full_name TEXT NOT NULL,
                    email TEXT UNIQUE NOT NULL,
                    phone TEXT UNIQUE NOT NULL,
                    password_hash TEXT NOT NULL,
                    institution TEXT DEFAULT 'Koperasi Kampus STMIK IKMI' NOT NULL,
                    role TEXT NOT NULL DEFAULT 'Mahasiswa',
                    is_email_verified INTEGER DEFAULT 0,
                    is_phone_verified INTEGER DEFAULT 0,
                    is_active INTEGER DEFAULT 0,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                );

                CREATE TABLE IF NOT EXISTS verifications (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    user_id INTEGER NOT NULL,
                    type TEXT NOT NULL,
                    code TEXT NOT NULL,
                    expires_at DATETIME NOT NULL,
                    is_used INTEGER DEFAULT 0,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
                );

                CREATE TABLE IF NOT EXISTS products (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    category TEXT NOT NULL,
                    name TEXT NOT NULL,
                    description TEXT,
                    price REAL NOT NULL,
                    stock INTEGER DEFAULT 0,
                    image_url TEXT,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
                );

                CREATE TABLE IF NOT EXISTS orders (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    order_code TEXT UNIQUE NOT NULL,
                    user_id INTEGER NOT NULL,
                    total_amount REAL NOT NULL,
                    status TEXT DEFAULT 'Pending' NOT NULL,
                    payment_method TEXT,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users(id)
                );

                CREATE TABLE IF NOT EXISTS order_items (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    order_id INTEGER NOT NULL,
                    product_id INTEGER NOT NULL,
                    quantity INTEGER NOT NULL,
                    unit_price REAL NOT NULL,
                    subtotal REAL NOT NULL,
                    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
                    FOREIGN KEY (product_id) REFERENCES products(id)
                );

                CREATE TABLE IF NOT EXISTS quizzes (
                    id INTEGER PRIMARY KEY AUTOINCREMENT,
                    user_id INTEGER NOT NULL,
                    course_name TEXT NOT NULL,
                    topic TEXT NOT NULL,
                    question_type TEXT NOT NULL,
                    raw_payload TEXT NOT NULL,
                    score REAL DEFAULT 0,
                    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users(id)
                );

                CREATE TABLE IF NOT EXISTS settings (
                    key TEXT PRIMARY KEY,
                    value TEXT,
                    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
                );
            ");
        } else {
            // MySQL auto table creation jika database ada tapi belum di-import
            $db->exec("
                CREATE TABLE IF NOT EXISTS users (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    nickname VARCHAR(50) NOT NULL,
                    full_name VARCHAR(150) NOT NULL,
                    email VARCHAR(100) UNIQUE NOT NULL,
                    phone VARCHAR(20) UNIQUE NOT NULL,
                    password_hash VARCHAR(255) NOT NULL,
                    institution VARCHAR(100) DEFAULT 'Koperasi Kampus STMIK IKMI' NOT NULL,
                    role ENUM('Tamu', 'Mahasiswa', 'Dosen', 'Staff', 'Administrator') DEFAULT 'Mahasiswa' NOT NULL,
                    is_email_verified TINYINT(1) DEFAULT 0,
                    is_phone_verified TINYINT(1) DEFAULT 0,
                    is_active TINYINT(1) DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                );

                CREATE TABLE IF NOT EXISTS verifications (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    type ENUM('email_token', 'wa_otp') NOT NULL,
                    code VARCHAR(100) NOT NULL,
                    expires_at DATETIME NOT NULL,
                    is_used TINYINT(1) DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
                );

                CREATE TABLE IF NOT EXISTS products (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    category ENUM('ATK', 'Atribut', 'Buku', 'Lainnya') NOT NULL,
                    name VARCHAR(150) NOT NULL,
                    description TEXT,
                    price DECIMAL(12, 2) NOT NULL,
                    stock INT DEFAULT 0 NOT NULL,
                    image_url VARCHAR(255),
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                );

                CREATE TABLE IF NOT EXISTS orders (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    order_code VARCHAR(30) UNIQUE NOT NULL,
                    user_id INT NOT NULL,
                    total_amount DECIMAL(12, 2) NOT NULL,
                    status ENUM('Pending', 'Lunas', 'Selesai') DEFAULT 'Pending' NOT NULL,
                    payment_method VARCHAR(50),
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users(id)
                );

                CREATE TABLE IF NOT EXISTS order_items (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    order_id INT NOT NULL,
                    product_id INT NOT NULL,
                    quantity INT NOT NULL,
                    unit_price DECIMAL(12, 2) NOT NULL,
                    subtotal DECIMAL(12, 2) NOT NULL,
                    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
                    FOREIGN KEY (product_id) REFERENCES products(id)
                );

                CREATE TABLE IF NOT EXISTS quizzes (
                    id INT AUTO_INCREMENT PRIMARY KEY,
                    user_id INT NOT NULL,
                    course_name VARCHAR(100) NOT NULL,
                    topic VARCHAR(150) NOT NULL,
                    question_type ENUM('Pilihan Ganda', 'Essay') NOT NULL,
                    raw_payload JSON NOT NULL,
                    score DECIMAL(5,2) DEFAULT 0,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users(id)
                );

                CREATE TABLE IF NOT EXISTS settings (
                    `key` VARCHAR(50) PRIMARY KEY,
                    `value` TEXT,
                    `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
                );
            ");
        }

        self::seedInitialData();
    }

    private static function seedInitialData(): void {
        $db = self::$instance;

        // Cek data admin
        $stmt = $db->query("SELECT COUNT(*) as count FROM users WHERE role = 'Administrator'");
        $adminCount = (int) $stmt->fetch()['count'];

        if ($adminCount === 0) {
            $insertUser = $db->prepare("
                INSERT INTO users (nickname, full_name, email, phone, password_hash, institution, role, is_email_verified, is_phone_verified, is_active)
                VALUES (:nickname, :full_name, :email, :phone, :password_hash, :institution, :role, 1, 1, 1)
            ");

            $defaultUsers = [
                ['nickname' => 'Admin IKMI', 'full_name' => 'Administrator Koperasi STMIK IKMI', 'email' => 'admin@ikmi.ac.id', 'phone' => '081234567890', 'pwd' => 'admin123', 'role' => ROLE_ADMIN],
                ['nickname' => 'Pak Budi', 'full_name' => 'Dr. Budi Santoso, M.Kom.', 'email' => 'budi.dosen@ikmi.ac.id', 'phone' => '081234567891', 'pwd' => 'dosen123', 'role' => ROLE_DOSEN],
                ['nickname' => 'Rian', 'full_name' => 'Rian Saputra', 'email' => 'rian.mahasiswa@ikmi.ac.id', 'phone' => '081298765432', 'pwd' => 'mhs123', 'role' => ROLE_MAHASISWA],
                ['nickname' => 'Siti', 'full_name' => 'Siti Nurhaliza, A.Md.', 'email' => 'siti.staff@ikmi.ac.id', 'phone' => '081234567893', 'pwd' => 'staff123', 'role' => ROLE_STAFF],
                ['nickname' => 'Tamu', 'full_name' => 'Pengunjung Tamu Kampus', 'email' => 'guest@ikmi.ac.id', 'phone' => '081234567894', 'pwd' => 'tamu123', 'role' => ROLE_TAMU]
            ];

            foreach ($defaultUsers as $u) {
                $insertUser->execute([
                    ':nickname' => $u['nickname'],
                    ':full_name' => $u['full_name'],
                    ':email' => $u['email'],
                    ':phone' => $u['phone'],
                    ':password_hash' => password_hash($u['pwd'], PASSWORD_BCRYPT),
                    ':institution' => INSTITUTION_NAME,
                    ':role' => $u['role']
                ]);
            }
        }

        // Cek produk koperasi
        $stmt = $db->query("SELECT COUNT(*) as count FROM products");
        $productCount = (int) $stmt->fetch()['count'];

        if ($productCount === 0) {
            $products = [
                // ATK
                ['category' => 'ATK', 'name' => 'Buku Catatan Eksklusif STMIK IKMI A5', 'description' => 'Notebook grid hard cover berlogo resmi STMIK IKMI, isi 100 lembar kertas bookpaper berkualitas.', 'price' => 25000, 'stock' => 120, 'image_url' => 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=500&q=80'],
                ['category' => 'ATK', 'name' => 'Paket Alat Tulis & Pulpen Gel IKMI (Isi 3)', 'description' => 'Paket pulpen gel 0.5mm tinta hitam pekat anti macet dengan grip ergonomis untuk kuliah.', 'price' => 15000, 'stock' => 200, 'image_url' => 'https://images.unsplash.com/photo-1585336261026-7080e77d337f?w=500&q=80'],
                ['category' => 'ATK', 'name' => 'Binder Kuliah B5 26 Ring STMIK IKMI', 'description' => 'Binder kuliah multifungsi dengan pembatas subjek mata kuliah dan logo kampus timbul.', 'price' => 38000, 'stock' => 85, 'image_url' => 'https://images.unsplash.com/photo-1531346878377-a5be20888e57?w=500&q=80'],
                
                // Atribut
                ['category' => 'Atribut', 'name' => 'Jas Almamater Resmi STMIK IKMI', 'description' => 'Jas almamater resmi warna biru khas STMIK IKMI dengan bordir badge komputer & teknologi berkualitas tinggi.', 'price' => 165000, 'stock' => 50, 'image_url' => 'https://images.unsplash.com/photo-1594938298603-c8148c4dae35?w=500&q=80'],
                ['category' => 'Atribut', 'name' => 'Kaos Polo Shirt Kampus STMIK IKMI', 'description' => 'Kaos polo bahan lacoste pique premium, nyaman dipakai saat kegiatan praktikum dan UKM kampus.', 'price' => 75000, 'stock' => 60, 'image_url' => 'https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=500&q=80'],
                ['category' => 'Atribut', 'name' => 'Lanyard + ID Card Holder STMIK IKMI', 'description' => 'Tali lanyard printing dua sisi logo STMIK IKMI dilengkapi card holder akrilik transparan tebal.', 'price' => 20000, 'stock' => 150, 'image_url' => 'https://images.unsplash.com/photo-1563245372-f21724e3856d?w=500&q=80'],
                ['category' => 'Atribut', 'name' => 'Pin Logam & Dasi Formal STMIK IKMI', 'description' => 'Set pin kuningan lapis resin mengkilap dan dasi resmi untuk sidang skripsi atau yudisium.', 'price' => 35000, 'stock' => 90, 'image_url' => 'https://images.unsplash.com/photo-1617137984095-74e4e5e3613f?w=500&q=80'],

                // Buku
                ['category' => 'Buku', 'name' => 'Buku Ajar: Algoritma & Struktur Data Modern', 'description' => 'Panduan komprehensif logika komputasi, array, linked list, tree, graph hingga analisis Big O.', 'price' => 85000, 'stock' => 45, 'image_url' => 'https://images.unsplash.com/photo-1532012164546-f432f2e3777f?w=500&q=80'],
                ['category' => 'Buku', 'name' => 'Buku Praktikum: Rekayasa Perangkat Lunak & MVC', 'description' => 'Pedoman perancangan sistem berbasis UML, arsitektur MVC, REST API, dan testing aplikasi web.', 'price' => 78000, 'stock' => 40, 'image_url' => 'https://images.unsplash.com/photo-1512820790803-83ca734da794?w=500&q=80'],
                ['category' => 'Buku', 'name' => 'Buku Referensi: Basis Data Relasional & NoSQL', 'description' => 'Kajian mendalam pemodelan ERD, normalisasi data 1NF-BCNF, optimasi indeks SQL dan database NoSQL.', 'price' => 92000, 'stock' => 35, 'image_url' => 'https://images.unsplash.com/photo-1544947950-fa07a98d237f?w=500&q=80']
            ];

            $insertStmt = $db->prepare("
                INSERT INTO products (category, name, description, price, stock, image_url)
                VALUES (:category, :name, :description, :price, :stock, :image_url)
            ");

            foreach ($products as $p) {
                $insertStmt->execute([
                    ':category' => $p['category'],
                    ':name' => $p['name'],
                    ':description' => $p['description'],
                    ':price' => $p['price'],
                    ':stock' => $p['stock'],
                    ':image_url' => $p['image_url']
                ]);
            }
        }

        // Cek default settings (API Keys)
        $stmt = $db->query("SELECT COUNT(*) as count FROM settings");
        $settingCount = (int) $stmt->fetch()['count'];
        if ($settingCount === 0) {
            $defaultSettings = [
                'fonnte_token' => '',
                'ai_provider' => 'gemini',
                'ai_api_key' => '',
                'ai_model' => 'gemini-1.5-flash',
                'store_phone' => '081234567890'
            ];
            $settingStmt = $db->prepare("INSERT INTO settings (`key`, `value`) VALUES (:k, :v)");
            foreach ($defaultSettings as $k => $v) {
                $settingStmt->execute([':k' => $k, ':v' => $v]);
            }
        }
    }
}

