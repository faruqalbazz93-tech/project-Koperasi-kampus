<?php

class ShopController extends Controller {
    private Product $productModel;
    private Order $orderModel;

    public function __construct() {
        $this->productModel = new Product();
        $this->orderModel = new Order();
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    /**
     * Katalog Koperasi Kampus
     */
    public function index(): void {
        $category = $_GET['category'] ?? null;
        $products = $this->productModel->getAll($category);

        $this->view('shop/catalog', [
            'title' => 'Katalog Koperasi Kampus - ' . INSTITUTION_NAME,
            'products' => $products,
            'currentCategory' => $category
        ]);
    }

    /**
     * Tambah item ke keranjang belanja
     */
    public function addToCart(): void {
        $productId = (int) ($_POST['product_id'] ?? 0);
        $qty = max(1, (int) ($_POST['quantity'] ?? 1));

        $product = $this->productModel->getById($productId);
        if (!$product) {
            if ($this->isAjax()) {
                $this->json(['success' => false, 'message' => 'Produk tidak ditemukan.'], 404);
            }
            $_SESSION['flash_error'] = 'Produk tidak ditemukan.';
            $this->redirect('shop');
        }

        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] += $qty;
        } else {
            $_SESSION['cart'][$productId] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'category' => $product['category'],
                'price' => (float) $product['price'],
                'image_url' => $product['image_url'],
                'quantity' => $qty
            ];
        }

        if ($this->isAjax()) {
            $totalCount = array_sum(array_column($_SESSION['cart'], 'quantity'));
            $this->json([
                'success' => true,
                'message' => "{$product['name']} berhasil ditambahkan ke keranjang.",
                'total_cart_items' => $totalCount
            ]);
        }

        $_SESSION['flash_success'] = "{$product['name']} ditambahkan ke keranjang belanja.";
        $this->redirect('shop/cart');
    }

    /**
     * Tampilan Keranjang Belanja
     */
    public function cart(): void {
        $cartItems = $_SESSION['cart'] ?? [];
        $totalPrice = 0;

        foreach ($cartItems as $item) {
            $totalPrice += ($item['price'] * $item['quantity']);
        }

        $this->view('shop/cart', [
            'title' => 'Keranjang Belanja - ' . INSTITUTION_NAME,
            'cartItems' => $cartItems,
            'totalPrice' => $totalPrice
        ]);
    }

    /**
     * Update Quantity Keranjang
     */
    public function updateCart(): void {
        $productId = (int) ($_POST['product_id'] ?? 0);
        $action = $_POST['action'] ?? '';

        if (isset($_SESSION['cart'][$productId])) {
            if ($action === 'increase') {
                $_SESSION['cart'][$productId]['quantity'] += 1;
            } elseif ($action === 'decrease') {
                $_SESSION['cart'][$productId]['quantity'] -= 1;
                if ($_SESSION['cart'][$productId]['quantity'] <= 0) {
                    unset($_SESSION['cart'][$productId]);
                }
            } elseif ($action === 'remove') {
                unset($_SESSION['cart'][$productId]);
            }
        }

        $this->redirect('shop/cart');
    }

    /**
     * Halaman Checkout
     */
    public function checkout(): void {
        $this->requireAuth();

        $cartItems = $_SESSION['cart'] ?? [];
        if (empty($cartItems)) {
            $_SESSION['flash_error'] = 'Keranjang belanja Anda masih kosong.';
            $this->redirect('shop');
        }

        $totalPrice = 0;
        foreach ($cartItems as $item) {
            $totalPrice += ($item['price'] * $item['quantity']);
        }

        $this->view('shop/checkout', [
            'title' => 'Checkout Pesanan - ' . INSTITUTION_NAME,
            'cartItems' => $cartItems,
            'totalPrice' => $totalPrice,
            'user' => $this->getUser()
        ]);
    }

    /**
     * Proses Pemesanan (Place Order)
     */
    public function placeOrder(): void {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $cartItems = $_SESSION['cart'] ?? [];
            if (empty($cartItems)) {
                $this->redirect('shop');
            }

            $user = $this->getUser();
            $paymentMethod = $_POST['payment_method'] ?? 'Kasir Koperasi IKMI';

            $orderCode = $this->orderModel->createOrder($user['id'], $cartItems, $paymentMethod);

            if ($orderCode) {
                // Hitung total untuk pesan WhatsApp
                $totalPrice = 0;
                foreach ($cartItems as $item) {
                    $totalPrice += ($item['price'] * $item['quantity']);
                }

                // Kirim Notifikasi WhatsApp via Fonnte (Status LUNAS Otomatis)
                if (!empty($user['phone'])) {
                    $fonnte = new FonnteService();
                    $fonnte->sendOrderNotification($user['phone'], $orderCode, $totalPrice, ORDER_LUNAS, $user['full_name']);
                }

                // Bersihkan keranjang
                $_SESSION['cart'] = [];
                $_SESSION['flash_success'] = "Transaksi Berhasil! Pesanan {$orderCode} telah tercatat secara otomatis (Status: LUNAS) dan stok produk telah diperbarui.";
                $this->redirect('shop/orderDetail/' . $orderCode);
            } else {
                $_SESSION['flash_error'] = 'Gagal memproses pesanan. Silakan coba kembali.';
                $this->redirect('shop/checkout');
            }
        }
    }

    /**
     * Riwayat Pesanan Pengguna
     */
    public function orders(): void {
        $this->requireAuth();
        $user = $this->getUser();
        $orders = $this->orderModel->getUserOrders($user['id']);

        $this->view('shop/orders', [
            'title' => 'Riwayat Pesanan - ' . INSTITUTION_NAME,
            'orders' => $orders
        ]);
    }

    /**
     * Detail Invoice Pesanan
     */
    public function orderDetail(string $orderCode = ''): void {
        $this->requireAuth();
        $order = $this->orderModel->getOrderDetails($orderCode);

        if (!$order) {
            $_SESSION['flash_error'] = 'Pesanan tidak ditemukan.';
            $this->redirect('shop/orders');
        }

        $user = $this->getUser();
        // Mahasiswa/Dosen hanya bisa melihat pesanannya sendiri, Admin bisa melihat semua
        if ($user['role'] !== ROLE_ADMIN && $order['user_id'] != $user['id']) {
            $_SESSION['flash_error'] = 'Anda tidak memiliki hak akses untuk pesanan ini.';
            $this->redirect('shop/orders');
        }

        $this->view('shop/order_detail', [
            'title' => 'Detail Pesanan #' . $orderCode,
            'order' => $order
        ]);
    }

    private function isAjax(): bool {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') 
            || (isset($_SERVER['HTTP_ACCEPT']) && strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== false);
    }
}

