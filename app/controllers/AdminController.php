<?php

class AdminController extends Controller {
    private Order $orderModel;
    private Product $productModel;
    private User $userModel;
    private Setting $settingModel;
    private FonnteService $fonnteService;

    public function __construct() {
        $this->requireRole(ROLE_ADMIN);
        $this->orderModel = new Order();
        $this->productModel = new Product();
        $this->userModel = new User();
        $this->settingModel = new Setting();
        $this->fonnteService = new FonnteService();
    }

    /**
     * Dashboard Utama Administrator
     */
    public function index(): void {
        $db = Database::getConnection();

        $totalUsers = (int) $db->query("SELECT COUNT(*) as c FROM users")->fetch()['c'];
        $totalOrders = (int) $db->query("SELECT COUNT(*) as c FROM orders")->fetch()['c'];
        $pendingOrders = (int) $db->query("SELECT COUNT(*) as c FROM orders WHERE status = 'Pending'")->fetch()['c'];
        $totalRevenue = (float) $db->query("SELECT COALESCE(SUM(total_amount), 0) as s FROM orders WHERE status IN ('Lunas', 'Selesai')")->fetch()['s'];

        $recentOrders = array_slice($this->orderModel->getAllOrders(), 0, 5);
        $users = $this->userModel->getAllUsers();

        $this->view('admin/dashboard', [
            'title' => 'Dashboard Administrator - ' . INSTITUTION_NAME,
            'stats' => [
                'users' => $totalUsers,
                'orders' => $totalOrders,
                'pending' => $pendingOrders,
                'revenue' => $totalRevenue
            ],
            'recentOrders' => $recentOrders,
            'users' => $users
        ]);
    }

    /**
     * Manajemen Pesanan Koperasi
     */
    public function orders(): void {
        $orders = $this->orderModel->getAllOrders();

        $this->view('admin/orders', [
            'title' => 'Manajemen Transaksi Koperasi - ' . INSTITUTION_NAME,
            'orders' => $orders
        ]);
    }

    /**
     * Perbarui Status Transaksi (Pending -> Lunas -> Selesai) + WhatsApp Notification via Fonnte
     */
    public function updateOrderStatus(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $orderId = (int) ($_POST['order_id'] ?? 0);
            $status = $_POST['status'] ?? '';

            if ($this->orderModel->updateStatus($orderId, $status)) {
                $_SESSION['flash_success'] = "Status transaksi #{$orderId} berhasil diubah menjadi: {$status}.";

                // Kirim notifikasi WhatsApp via Fonnte otomatis ke pembeli
                $db = Database::getConnection();
                $stmt = $db->prepare("
                    SELECT o.order_code, o.total_amount, u.full_name, u.phone 
                    FROM orders o
                    JOIN users u ON o.user_id = u.id
                    WHERE o.id = :id LIMIT 1
                ");
                $stmt->execute([':id' => $orderId]);
                $orderData = $stmt->fetch();

                if ($orderData && !empty($orderData['phone'])) {
                    $this->fonnteService->sendOrderNotification(
                        $orderData['phone'],
                        $orderData['order_code'],
                        (float) $orderData['total_amount'],
                        $status,
                        $orderData['full_name']
                    );
                }
            } else {
                $_SESSION['flash_error'] = "Gagal memperbarui status transaksi.";
            }
        }

        $this->redirect('admin/orders');
    }

    /**
     * Halaman Pengaturan API Key (Fonnte WA & AI Provider)
     */
    public function settings(): void {
        $settings = $this->settingModel->getAll();

        $this->view('admin/settings', [
            'title' => 'Pengaturan API Key & Gateway - ' . INSTITUTION_NAME,
            'settings' => $settings
        ]);
    }

    /**
     * Simpan Pengaturan API Key secara Dinamis ke Database
     */
    public function saveSettings(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $fonnteToken = trim($_POST['fonnte_token'] ?? '');
            $aiProvider = trim($_POST['ai_provider'] ?? 'gemini');
            $aiApiKey = trim($_POST['ai_api_key'] ?? '');
            $storePhone = trim($_POST['store_phone'] ?? '');

            $this->settingModel->set('fonnte_token', $fonnteToken);
            $this->settingModel->set('ai_provider', $aiProvider);
            $this->settingModel->set('ai_api_key', $aiApiKey);
            $this->settingModel->set('store_phone', $storePhone);

            $_SESSION['flash_success'] = 'Konfigurasi API Key (Fonnte & AI) berhasil disimpan ke database.';
        }

        $this->redirect('admin/settings');
    }

    /**
     * Endpoint AJAX: Tes Koneksi AI API Key (Google Gemini / OpenAI)
     */
    public function testAiConnection(): void {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->json(['success' => false, 'message' => 'Metode request tidak valid.'], 405);
        }

        $provider = strtolower(trim($_POST['ai_provider'] ?? 'gemini'));
        $apiKey = trim($_POST['ai_api_key'] ?? '');

        // Jika user tidak mengetikkan key baru di input, gunakan key yang tersimpan di database
        if (empty($apiKey)) {
            $apiKey = trim($this->settingModel->get('ai_api_key', '') ?? '');
        }

        $providerName = ($provider === 'openai') ? 'OpenAI' : 'Google Gemini';

        if (empty($apiKey)) {
            $this->json([
                'success' => false,
                'provider' => $providerName,
                'message' => "API Key {$providerName} masih kosong. Silakan masukkan atau simpan API Key terlebih dahulu."
            ], 400);
        }

        if ($provider === 'openai') {
            $result = $this->pingOpenAi($apiKey);
        } else {
            $result = $this->pingGemini($apiKey);
        }

        $this->json($result, $result['success'] ? 200 : 400);
    }

    /**
     * Ping uji coba ke Google Gemini API (Endpoint generateContent)
     */
    private function pingGemini(string $apiKey): array {
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash:generateContent?key=" . urlencode($apiKey);

        $payload = json_encode([
            'contents' => [
                [
                    'parts' => [
                        ['text' => 'ping test']
                    ]
                ]
            ]
        ]);

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $payload,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => ['Content-Type: application/json']
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($error) {
            return [
                'success' => false,
                'provider' => 'Google Gemini',
                'http_code' => $httpCode,
                'message' => "Gagal menghubungi server Google Gemini: {$error}. Pastikan koneksi internet aktif."
            ];
        }

        $data = json_decode($response, true);

        if ($httpCode === 200 && isset($data['candidates'][0]['content'])) {
            return [
                'success' => true,
                'provider' => 'Google Gemini',
                'http_code' => 200,
                'message' => "Koneksi Berhasil! API Key Google Gemini (gemini-1.5-flash) valid, terverifikasi aktif, dan siap melayani Chatbot & Quiz AI."
            ];
        }

        // Tangani pesan error spesifik dari Google Gemini
        $detail = $data['error']['message'] ?? '';
        if ($httpCode === 400) {
            $message = "API Key Google Gemini tidak valid. Pastikan Anda menyalin kunci dari Google AI Studio dengan benar.";
        } elseif ($httpCode === 403) {
            $message = "Akses ditolak (HTTP 403 Forbidden). Kuota Google Gemini tidak aktif atau izin wilayah/proyek dibatasi.";
        } elseif ($httpCode === 429) {
            $message = "Batas kuota (Rate limit / Quota exceeded) Google Gemini terlampaui.";
        } else {
            $message = "Uji koneksi gagal (HTTP {$httpCode}): " . ($detail ?: "Respons tidak terduga dari server Gemini.");
        }

        return [
            'success' => false,
            'provider' => 'Google Gemini',
            'http_code' => $httpCode,
            'message' => $message,
            'raw_error' => $detail
        ];
    }

    /**
     * Ping uji coba ke OpenAI API
     */
    private function pingOpenAi(string $apiKey): array {
        $url = "https://api.openai.com/v1/models";

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => [
                'Authorization: Bearer ' . $apiKey,
                'Content-Type: application/json'
            ]
        ]);

        $response = curl_exec($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($error) {
            return [
                'success' => false,
                'provider' => 'OpenAI',
                'http_code' => $httpCode,
                'message' => "Gagal menghubungi server OpenAI: {$error}. Pastikan koneksi internet aktif."
            ];
        }

        $data = json_decode($response, true);

        if ($httpCode === 200 && isset($data['data'])) {
            return [
                'success' => true,
                'provider' => 'OpenAI',
                'http_code' => 200,
                'message' => "Koneksi Berhasil! API Key OpenAI valid dan terhubung dengan baik ke endpoint resmi OpenAI."
            ];
        }

        $detail = $data['error']['message'] ?? '';
        if ($httpCode === 401) {
            $message = "API Key OpenAI tidak valid atau telah kedaluwarsa. Periksa kembali API Key Anda dari platform.openai.com.";
        } elseif ($httpCode === 429) {
            $message = "Batas kuota atau rate limit OpenAI terlampaui (Insufficient Quota / Billing belum diaktifkan).";
        } else {
            $message = "Uji koneksi gagal (HTTP {$httpCode}): " . ($detail ?: "Respons tidak terduga dari server OpenAI.");
        }

        return [
            'success' => false,
            'provider' => 'OpenAI',
            'http_code' => $httpCode,
            'message' => $message,
            'raw_error' => $detail
        ];
    }

    /**
     * Test Kirim Pesan WhatsApp via Fonnte
     */
    public function testFonnte(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $target = trim($_POST['target_phone'] ?? '');
            if (empty($target)) {
                $_SESSION['flash_error'] = 'Nomor WhatsApp tujuan tes tidak boleh kosong.';
                $this->redirect('admin/settings');
            }

            $testMessage = "Halo! Ini adalah pesan uji coba integrasi Fonnte WhatsApp Gateway dari *Sistem Koperasi Kampus STMIK IKMI*.\n\nSistem API berjalan normal. ✅";
            $res = $this->fonnteService->sendMessage($target, $testMessage);

            if ($res['status']) {
                $modeText = ($res['mode'] ?? '') === 'simulation' ? ' (Mode Simulasi - Token Kosong)' : '';
                $_SESSION['flash_success'] = "Pesan tes WhatsApp berhasil diproses{$modeText} ke {$target}.";
            } else {
                $_SESSION['flash_error'] = "Gagal mengirim WhatsApp: " . ($res['message'] ?? 'Periksa token Fonnte.');
            }
        }

        $this->redirect('admin/settings');
    }

    /**
     * Manajemen Katalog Produk
     */
    public function products(): void {
        $products = $this->productModel->getAll();

        $this->view('admin/products', [
            'title' => 'Manajemen Katalog Produk - ' . INSTITUTION_NAME,
            'products' => $products
        ]);
    }

    /**
     * Tambah Produk Baru
     */
    public function addProduct(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = trim($_POST['name'] ?? '');
            $category = $_POST['category'] ?? 'ATK';
            $price = (float) ($_POST['price'] ?? 0);
            $stock = (int) ($_POST['stock'] ?? 0);
            $description = trim($_POST['description'] ?? '');
            $imageUrl = trim($_POST['image_url'] ?? '');

            if (!empty($name) && $price > 0) {
                $this->productModel->create([
                    'name' => $name,
                    'category' => $category,
                    'price' => $price,
                    'stock' => $stock,
                    'description' => $description,
                    'image_url' => $imageUrl ?: 'https://images.unsplash.com/photo-1544716278-ca5e3f4abd8c?w=500&q=80'
                ]);
                $_SESSION['flash_success'] = 'Produk baru berhasil ditambahkan ke katalog koperasi.';
            } else {
                $_SESSION['flash_error'] = 'Nama produk dan harga wajib diisi dengan benar.';
            }
        }

        $this->redirect('admin/products');
    }

    /**
     * Hapus Produk
     */
    public function deleteProduct(string $id): void {
        $this->productModel->delete((int) $id);
        $_SESSION['flash_success'] = 'Produk berhasil dihapus dari katalog.';
        $this->redirect('admin/products');
    }
}

