<?php

class ChatbotService {
    private PDO $db;
    private Setting $settingModel;

    public function __construct() {
        $this->db = Database::getConnection();
        $this->settingModel = new Setting();
    }

    /**
     * Proses pertanyaan pengguna dengan RAG (Retrieval-Augmented Generation) berbasis Database
     */
    public function ask(string $prompt, ?array $user = null): array {
        $cleanPrompt = trim($prompt);
        if (empty($cleanPrompt)) {
            return [
                'success' => false,
                'message' => 'Pertanyaan tidak boleh kosong.'
            ];
        }

        // 1. Filter Keamanan Anti-Coding (1 Kalimat Singkat)
        if ($this->isCodingRequest($cleanPrompt)) {
            return [
                'success' => true,
                'source' => 'Kebijakan Keamanan Kampus',
                'context_tables' => [],
                'reply' => 'Maaf, saya tidak memiliki izin untuk membaca, memproses, atau menulis kode pemrograman.'
            ];
        }

        // 2. Ekstraksi Konteks Database (RAG Retrieval Layer)
        $retrievedData = $this->retrieveDatabaseContext($cleanPrompt, $user);

        // 3. Cek apakah ada API Key eksternal (Gemini) di pengaturan admin
        $aiApiKey = trim($this->settingModel->get('ai_api_key', '') ?? '');
        $aiProvider = strtolower(trim($this->settingModel->get('ai_provider', 'gemini') ?? 'gemini'));

        if (!empty($aiApiKey)) {
            // Coba panggil LLM Eksternal dengan Grounding Data Real dari Database
            $response = $this->callExternalLlm($aiProvider, $aiApiKey, $cleanPrompt, $retrievedData, $user);
            if ($response['success']) {
                return $response;
            }
        }

        // 4. Core RAG Synthesizer (Respon Simpel, Faktual, & Langsung ke Intinya)
        $synthesis = $this->synthesizeLocalResponse($cleanPrompt, $retrievedData, $user);

        return [
            'success' => true,
            'source' => 'Database RAG Core Engine (STMIK IKMI)',
            'context_tables' => $retrievedData['tables_queried'] ?? ['products'],
            'reply' => $synthesis
        ];
    }

    /**
     * Retrieval Layer: Membaca tabel-tabel relevan di database secara komprehensif
     */
    private function retrieveDatabaseContext(string $query, ?array $user): array {
        $q = strtolower(trim($query));
        $context = [
            'intent' => 'fallback',
            'tables_queried' => [],
            'products' => [],
            'categories' => [],
            'orders_summary' => [],
            'latest_orders' => [],
            'my_orders' => [],
            'quizzes' => [],
            'users_summary' => [],
            'db_overview' => [],
            'user_profile' => null,
            'search_term' => '',
            'not_found' => false
        ];

        // =========================================================================
        // A. Intent: Identitas Asisten Bot ("siapa anda", "siapa kamu", "kamu siapa")
        // =========================================================================
        if (preg_match('/(siapa\s+(anda|kamu|bot|lu)|(anda|kamu|bot|lu)\s+siapa|identitas\s+(anda|kamu)|apa\s+(tugas|peran|fungsi)\s+(anda|kamu)|siapakah\s+(anda|kamu))/i', $q)) {
            $context['intent'] = 'who_are_you';
            $context['tables_queried'] = ['system'];
            return $context;
        }

        // =========================================================================
        // B. Intent: Identitas Pengguna ("siapa saya", "akun saya", "profil saya")
        // =========================================================================
        if (preg_match('/(siapa\s+(saya|aku|gw|gue)|(saya|aku|gw|gue)\s+siapa|identitas\s+(saya|aku)|profil\s+(saya|aku)|akun\s+(saya|aku)|data\s+diri\s+(saya|aku)|nama\s+saya|role\s+saya)/i', $q)) {
            $context['intent'] = 'who_am_i';
            $context['tables_queried'] = ['users'];

            if ($user && isset($user['id'])) {
                $stmt = $this->db->prepare("SELECT id, nickname, full_name, email, phone, role, institution FROM users WHERE id = :id LIMIT 1");
                $stmt->execute([':id' => $user['id']]);
                $context['user_profile'] = $stmt->fetch();
            }
            return $context;
        }

        // =========================================================================
        // C. Intent: Ringkasan Seluruh Database & Tabel (Database Overview)
        // =========================================================================
        if (preg_match('/\b(database|basis data|tabel|schema|skema|isi database|struktur database|tabel database|cek database)\b/i', $q) && !preg_match('/buku\s+basis\s+data/i', $q)) {
            $context['intent'] = 'database_overview';
            $context['tables_queried'] = ['products', 'orders', 'order_items', 'users', 'quizzes', 'settings'];

            $context['db_overview'] = [
                'products' => (int) $this->db->query("SELECT COUNT(*) FROM products")->fetchColumn(),
                'orders' => (int) $this->db->query("SELECT COUNT(*) FROM orders")->fetchColumn(),
                'order_items' => (int) $this->db->query("SELECT COUNT(*) FROM order_items")->fetchColumn(),
                'users' => (int) $this->db->query("SELECT COUNT(*) FROM users")->fetchColumn(),
                'quizzes' => (int) $this->db->query("SELECT COUNT(*) FROM quizzes")->fetchColumn(),
                'settings' => (int) $this->db->query("SELECT COUNT(*) FROM settings")->fetchColumn()
            ];
            return $context;
        }

        // =========================================================================
        // C. Intent: Riwayat Pesanan Pribadi Pengguna (Personal Orders)
        // =========================================================================
        if (preg_match('/(pesanan saya|order saya|riwayat saya|belanjaan saya|transaksi saya|daftar belanja saya)/i', $q)) {
            $context['intent'] = 'personal_orders';
            $context['tables_queried'] = ['orders', 'order_items'];

            if ($user && isset($user['id'])) {
                $stmt = $this->db->prepare("
                    SELECT o.*, 
                           (SELECT COUNT(*) FROM order_items oi WHERE oi.order_id = o.id) as total_items
                    FROM orders o
                    WHERE o.user_id = :uid
                    ORDER BY o.id DESC
                    LIMIT 5
                ");
                $stmt->execute([':uid' => $user['id']]);
                $context['my_orders'] = $stmt->fetchAll();
            }
            return $context;
        }

        // =========================================================================
        // D. Intent: Rekapitulasi Transaksi, Keuangan, & Omzet (Orders Analytics)
        // =========================================================================
        if (preg_match('/(transaksi|pesanan|order|rekap|omzet|omset|pendapatan|penjualan|pembelian|uang masuk)/i', $q) && !preg_match('/(cara pesan|buat pesanan)/i', $q)) {
            $context['intent'] = 'orders_summary';
            $context['tables_queried'] = ['orders', 'users'];

            $summaryStmt = $this->db->query("
                SELECT status, COUNT(*) as count, COALESCE(SUM(total_amount), 0) as total_amount
                FROM orders
                GROUP BY status
            ");
            $context['orders_summary'] = $summaryStmt->fetchAll();

            $latestStmt = $this->db->query("
                SELECT o.order_code, o.total_amount, o.status, o.payment_method, o.created_at, u.full_name, u.role
                FROM orders o
                JOIN users u ON o.user_id = u.id
                ORDER BY o.id DESC LIMIT 5
            ");
            $context['latest_orders'] = $latestStmt->fetchAll();
            return $context;
        }

        // =========================================================================
        // E. Intent: Sivitas Pengguna & Anggota Kampus (Users Analytics)
        // =========================================================================
        if (preg_match('/(anggota|pengguna|user|mahasiswa|dosen|staff|civitas|sivitas|berapa orang|total akun)/i', $q)) {
            $context['intent'] = 'users_summary';
            $context['tables_queried'] = ['users'];

            $userStmt = $this->db->query("
                SELECT role, COUNT(*) as count, 
                       SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_count,
                       SUM(CASE WHEN is_phone_verified = 1 THEN 1 ELSE 0 END) as wa_verified
                FROM users
                GROUP BY role
                ORDER BY count DESC
            ");
            $context['users_summary'] = $userStmt->fetchAll();
            return $context;
        }

        // =========================================================================
        // F. Intent: Kuis & Materi Edukasi Akademik (Quizzes Analytics)
        // =========================================================================
        if (preg_match('/(kuis|quiz|soal|ujian|materi|avl|tree|algoritma|3nf|mvc|struktur data)/i', $q) && !preg_match('/(harga|beli|jual|stok|buku)/i', $q)) {
            $context['intent'] = 'quizzes';
            $context['tables_queried'] = ['quizzes'];

            $quizStmt = $this->db->query("
                SELECT id, course_name, topic, question_type, score, created_at, raw_payload
                FROM quizzes
                ORDER BY id DESC LIMIT 5
            ");
            $quizzes = $quizStmt->fetchAll();
            foreach ($quizzes as &$qz) {
                $qz['payload'] = json_decode($qz['raw_payload'] ?? '{}', true);
                unset($qz['raw_payload']);
            }
            $context['quizzes'] = $quizzes;
            return $context;
        }

        // =========================================================================
        // G. Intent: Kategori Produk (Categories Overview)
        // =========================================================================
        if (preg_match('/(kategori|jenis)\s+(produk|barang)/i', $q) || preg_match('/^(kategori|kategori apa saja|ada kategori apa)/i', $q)) {
            $context['intent'] = 'categories';
            $context['tables_queried'] = ['products'];

            $catStmt = $this->db->query("
                SELECT category, COUNT(*) as item_count, SUM(stock) as total_stock, 
                       MIN(price) as min_price, MAX(price) as max_price
                FROM products
                GROUP BY category
                ORDER BY category ASC
            ");
            $context['categories'] = $catStmt->fetchAll();
            return $context;
        }

        // =========================================================================
        // H. Intent: Filter Stok Menipis / Habis (Low Stock Alert)
        // =========================================================================
        if (preg_match('/(habis|kosong|menipis|sedikit|kritis|hampir habis|restock)/i', $q) && preg_match('/(stok|produk|barang)/i', $q)) {
            $context['intent'] = 'low_stock';
            $context['tables_queried'] = ['products'];

            $stmt = $this->db->query("
                SELECT id, category, name, price, stock, description 
                FROM products 
                WHERE stock <= 20 
                ORDER BY stock ASC
            ");
            $context['products'] = $stmt->fetchAll();
            return $context;
        }

        // =========================================================================
        // I. Intent: Produk & Katalog Koperasi (Katalog, Stok, Pencarian Barang)
        // =========================================================================
        if (preg_match('/(produk|barang|katalog|stok|stock|harga|beli|jual|tersedia|ada|punya|cari|atk|atribut|jas|almamater|buku|lanyard|polo|binder|dasi|pin|pulpen|alat tulis)/i', $q)) {
            $context['tables_queried'] = ['products'];

            // Stopwords yang tidak dijadikan kata kunci nama barang
            $stopwords = [
                'stok', 'stock', 'produk', 'katalog', 'barang', 'apa', 'aja', 'saja', 
                'berapa', 'harga', 'ada', 'adakah', 'apakah', 'daftar', 'list', 'semua', 
                'koperasi', 'tampilkan', 'lihat', 'cek', 'mohon', 'tolong', 'info', 
                'informasi', 'tentang', 'bisa', 'beli', 'sistem', 'ikmi', 'stmik', 
                'unit', 'mau', 'dong', 'ya', 'kan', 'nya', 'yang', 'dan', 'di', 'ke', 
                'dari', 'untuk', 'pada', 'dengan', 'ini', 'itu', 'kategori', 'jenis',
                'tersedia', 'punya', 'cari', 'jual', 'bagaimana'
            ];

            $words = explode(' ', preg_replace('/[^a-zA-Z0-9\s]/', '', $q));
            $terms = array_values(array_filter($words, fn($w) => strlen($w) >= 2 && !in_array($w, $stopwords)));

            // 1. Jika pertanyaan umum ("stok produk apa aja", "katalog apa saja", dll) -> Tampilkan semua produk
            if (empty($terms)) {
                $context['intent'] = 'all_products';
                $stmt = $this->db->query("
                    SELECT id, category, name, price, stock, description 
                    FROM products 
                    ORDER BY category ASC, name ASC
                ");
                $context['products'] = $stmt->fetchAll();
                return $context;
            }

            // 2. Filter kategori spesifik
            if (count($terms) === 1) {
                $t = $terms[0];
                if (in_array($t, ['buku'])) {
                    $context['intent'] = 'category_filtered';
                    $stmt = $this->db->query("SELECT id, category, name, price, stock, description FROM products WHERE category = 'Buku' ORDER BY name ASC");
                    $context['products'] = $stmt->fetchAll();
                    $context['search_term'] = 'Kategori Buku';
                    return $context;
                } elseif (in_array($t, ['atk'])) {
                    $context['intent'] = 'category_filtered';
                    $stmt = $this->db->query("SELECT id, category, name, price, stock, description FROM products WHERE category = 'ATK' ORDER BY name ASC");
                    $context['products'] = $stmt->fetchAll();
                    $context['search_term'] = 'Kategori ATK';
                    return $context;
                } elseif (in_array($t, ['atribut'])) {
                    $context['intent'] = 'category_filtered';
                    $stmt = $this->db->query("SELECT id, category, name, price, stock, description FROM products WHERE category = 'Atribut' ORDER BY name ASC");
                    $context['products'] = $stmt->fetchAll();
                    $context['search_term'] = 'Kategori Atribut';
                    return $context;
                }
            }

            // 3. Pencarian nama barang spesifik
            $context['intent'] = 'specific_product';
            $context['search_term'] = implode(' ', $terms);

            $conditions = [];
            $params = [];
            foreach ($terms as $idx => $term) {
                $conditions[] = "(name LIKE :term{$idx} OR description LIKE :term{$idx} OR category LIKE :term{$idx})";
                $params[":term{$idx}"] = "%{$term}%";
            }

            $sql = "SELECT id, category, name, price, stock, description FROM products WHERE " . implode(' OR ', $conditions) . " ORDER BY stock DESC LIMIT 8";
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            $context['products'] = $stmt->fetchAll();

            if (empty($context['products'])) {
                $context['not_found'] = true;
            }

            return $context;
        }

        // Default Fallback
        return $context;
    }

    /**
     * Core RAG Synthesizer: Respon Simpel, Faktual, & Langsung pada Inti Data
     */
    private function synthesizeLocalResponse(string $query, array $context, ?array $user): string {
        $intent = $context['intent'] ?? 'fallback';

        // 1. Identitas Asisten Bot ("siapa anda", "siapa kamu", "kamu siapa")
        if ($intent === 'who_are_you') {
            return "Saya adalah **AI Asisten Database Koperasi Kampus STMIK IKMI** yang bertugas melayani informasi stok produk, harga barang, rekap transaksi, dan data operasional koperasi secara real-time.";
        }

        // 2. Informasi Identitas Pengguna ("siapa saya")
        if ($intent === 'who_am_i') {
            $prof = $context['user_profile'] ?? $user;
            if ($prof && !empty($prof['full_name'])) {
                $phone = !empty($prof['phone']) ? $prof['phone'] : '-';
                $email = !empty($prof['email']) ? $prof['email'] : '-';
                return "Anda terdaftar sebagai **{$prof['full_name']}**, peran: **{$prof['role']}**, email: `{$email}`, No. WA: `{$phone}`.";
            }
            return "Data profil pengguna tidak ditemukan di sesi database saat ini.";
        }

        // 2. Pencarian Produk Spesifik (1-2 Kalimat Singkat)
        if ($intent === 'specific_product') {
            if (!empty($context['products'])) {
                $lines = [];
                foreach ($context['products'] as $p) {
                    $stock = (int) $p['stock'];
                    $status = ($stock > 10) ? 'Tersedia' : (($stock > 0) ? 'Menipis' : 'Habis');
                    $priceFormatted = 'Rp ' . number_format($p['price'], 0, ',', '.');
                    $lines[] = "- **{$p['name']}** ({$p['category']}): Harga **{$priceFormatted}**, sisa stok **{$stock} unit** ({$status}).";
                }
                return implode("\n", $lines);
            }
            return "Produk **\"{$context['search_term']}\"** tidak ditemukan di database koperasi.";
        }

        // 3. Seluruh Katalog / Stok Produk (Poin Ringkas)
        if ($intent === 'all_products' || ($intent === 'category_filtered' && !empty($context['products']))) {
            $lines = ["Daftar produk dan sisa stok koperasi saat ini:"];
            $totalStock = 0;
            foreach ($context['products'] as $p) {
                $stock = (int) $p['stock'];
                $totalStock += $stock;
                $priceFormatted = 'Rp ' . number_format($p['price'], 0, ',', '.');
                $lines[] = "- **{$p['name']}**: {$priceFormatted} (Stok: {$stock} unit)";
            }
            $lines[] = "Total: " . count($context['products']) . " produk ({$totalStock} unit stok fisik).";
            return implode("\n", $lines);
        }

        // 4. Rekap Transaksi & Keuangan
        if ($intent === 'orders_summary') {
            $breakdown = [];
            $totalAmount = 0;
            $totalOrders = 0;
            foreach ($context['orders_summary'] as $os) {
                $cnt = (int) $os['count'];
                $amt = (float) $os['total_amount'];
                $totalOrders += $cnt;
                $totalAmount += $amt;
                $breakdown[] = "{$os['status']}: {$cnt} pesanan (Rp " . number_format($amt, 0, ',', '.') . ")";
            }
            $summaryStr = implode(', ', $breakdown);
            $totalStr = 'Rp ' . number_format($totalAmount, 0, ',', '.');
            return "Rekap pesanan koperasi: {$summaryStr}. Total: **{$totalOrders} transaksi** bernilai **{$totalStr}**.";
        }

        // 5. Riwayat Pesanan Pribadi
        if ($intent === 'personal_orders') {
            if (!$user) {
                return "Silakan login terlebih dahulu untuk melihat data pesanan Anda.";
            }
            if (empty($context['my_orders'])) {
                return "Anda belum memiliki riwayat pesanan di koperasi.";
            }
            $lines = ["Riwayat pesanan Anda:"];
            foreach ($context['my_orders'] as $mo) {
                $date = date('d/m/Y', strtotime($mo['created_at']));
                $amt = 'Rp ' . number_format($mo['total_amount'], 0, ',', '.');
                $lines[] = "- `{$mo['order_code']}` ({$date}): {$amt} - Status: **{$mo['status']}**";
            }
            return implode("\n", $lines);
        }

        // 6. Ringkasan Database & Tabel
        if ($intent === 'database_overview') {
            $ov = $context['db_overview'];
            return "Status database koperasi: `products` ({$ov['products']} barang), `orders` ({$ov['orders']} order), `order_items` ({$ov['order_items']} item), `users` ({$ov['users']} user), `quizzes` ({$ov['quizzes']} kuis), `settings` ({$ov['settings']} config).";
        }

        // 7. Data Pengguna / Sivitas
        if ($intent === 'users_summary') {
            $roles = [];
            $total = 0;
            foreach ($context['users_summary'] as $us) {
                $cnt = (int) $us['count'];
                $total += $cnt;
                $roles[] = "{$us['role']} ({$cnt} orang)";
            }
            return "Data pengguna terdaftar: " . implode(', ', $roles) . ". Total: {$total} akun.";
        }

        // 8. Stok Menipis
        if ($intent === 'low_stock') {
            if (empty($context['products'])) {
                return "Tidak ada produk dengan stok menipis (seluruh stok di atas 20 unit).";
            }
            $lines = ["Produk dengan stok menipis:"];
            foreach ($context['products'] as $p) {
                $lines[] = "- **{$p['name']}**: Sisa {$p['stock']} unit (Rp " . number_format($p['price'], 0, ',', '.') . ")";
            }
            return implode("\n", $lines);
        }

        // 9. Kategori Produk
        if ($intent === 'categories') {
            $cats = [];
            foreach ($context['categories'] as $c) {
                $cats[] = "{$c['category']} ({$c['item_count']} item, stok: {$c['total_stock']} unit)";
            }
            return "Kategori produk koperasi: " . implode(', ', $cats) . ".";
        }

        // 10. Kuis & Soal Edukasi
        if ($intent === 'quizzes') {
            if (empty($context['quizzes'])) {
                return "Belum ada riwayat kuis di database.";
            }
            $lines = ["Data kuis di database:"];
            foreach ($context['quizzes'] as $qz) {
                $lines[] = "- **{$qz['course_name']}** ({$qz['topic']}): Skor {$qz['score']}/100 ({$qz['question_type']})";
            }
            return implode("\n", $lines);
        }

        // Default 1 Kalimat Inti Tanpa Basa-Basi
        return "Ada yang bisa saya bantu terkait cek stok produk, harga barang, atau data transaksi koperasi?";
    }

    /**
     * Integrasi Eksternal LLM (Gemini API) jika API Key diisi oleh Admin & terhubung internet
     */
    private function callExternalLlm(string $provider, string $apiKey, string $prompt, array $dbData, ?array $user): array {
        if ($provider === 'gemini') {
            $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . urlencode($apiKey);

            $systemInstruction = "ATURAN UTAMA GAYA BAHASA & RESPON (SIMPEL & AKURAT):\n"
                               . "1. Peran: Anda adalah AI Asisten Database Koperasi Kampus STMIK IKMI.\n"
                               . "2. LARANGAN KERAS BERBICARA PANJANG: DILARANG menggunakan kalimat sambutan, sapaan basa-basi, perkenalan diri, atau daftar menu panduan ('Halo Kak...', 'Saya adalah AI...', 'Saya dapat membantu Anda...'). Langsung berikan jawaban intinya!\n"
                               . "3. RESPON BERBASIS DATA REAL: Jawab pertanyaan pengguna secara singkat, padat, dan akurat berdasarkan data dari database yang tersedia di konteks.\n"
                               . "4. FORMAT JAWABAN:\n"
                               . "   - Jika ditanya 'siapa anda' atau 'siapa kamu', langsung jawab: 'Saya adalah AI Asisten Database Koperasi Kampus STMIK IKMI yang bertugas melayani informasi stok produk, harga barang, rekap transaksi, dan data operasional koperasi.'\n"
                               . "   - Jika ditanya informasi pengguna (seperti 'siapa saya'), langsung sebutkan nama, peran (role), dan email/no WA dari data database secara singkat.\n"
                               . "   - Jika ditanya produk/stok, langsung sebutkan nama barang, harga, dan sisa stoknya dalam 1-2 kalimat atau bentuk poin ringkas.\n"
                               . "5. ANTI-CODING: Dilarang keras membahas atau menulis kode pemrograman (Python, PHP, JavaScript, dll). Jika ada permintaan coding, tolak dengan 1 kalimat singkat: 'Maaf, saya tidak memiliki izin untuk membaca, memproses, atau menulis kode pemrograman.'\n\n"
                               . "Konteks Data Database Terkini:\n"
                               . json_encode($dbData, JSON_UNESCAPED_UNICODE);

            $payload = [
                'contents' => [
                    [
                        'role' => 'user',
                        'parts' => [
                            ['text' => "Konteks Database:\n" . json_encode($dbData) . "\n\nPertanyaan: " . $prompt]
                        ]
                    ]
                ],
                'systemInstruction' => [
                    'parts' => [['text' => $systemInstruction]]
                ]
            ];

            $ch = curl_init($url);
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_POST => true,
                CURLOPT_HTTPHEADER => ['Content-Type: application/json'],
                CURLOPT_POSTFIELDS => json_encode($payload),
                CURLOPT_TIMEOUT => 15
            ]);
            $res = curl_exec($ch);
            $err = curl_error($ch);
            curl_close($ch);

            if (!$err && $res) {
                $data = json_decode($res, true);
                $replyText = $data['candidates'][0]['content']['parts'][0]['text'] ?? null;
                if ($replyText) {
                    return [
                        'success' => true,
                        'source' => 'Google Gemini LLM (Database Grounded)',
                        'context_tables' => $dbData['tables_queried'] ?? ['products'],
                        'reply' => trim($replyText)
                    ];
                }
            }
        }

        return ['success' => false];
    }

    /**
     * Filter Keamanan: Memeriksa apakah permintaan mengandung atau menanyakan kode pemrograman
     */
    private function isCodingRequest(string $prompt): bool {
        $p = strtolower($prompt);

        $blockedPatterns = [
            '/\bpython\b/i',
            '/\bdef\s+[a-z_][a-z0-9_]*\s*\(/i',
            '/\bimport\s+[a-z0-9_]+/i',
            '/\bfrom\s+[a-z0-9_]+\s+import/i',
            '/\bpip\s+install/i',
            '/\bprint\s*\(/i',
            '/\b(koding|coding|source code|skrip|script|sintaks|syntax|pemrograman)\b/i',
            '/\b(javascript|php|sql mentah|c\+\+|java|golang|bash)\b/i',
            '/```[a-z]*[\s\S]*?```/i'
        ];

        foreach ($blockedPatterns as $pattern) {
            if (preg_match($pattern, $p)) {
                return true;
            }
        }

        return false;
    }
}
