<?php

class Order {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Buat pesanan baru dari keranjang belanja
     */
    public function createOrder(int $userId, array $cartItems, string $paymentMethod = 'Kasir Koperasi'): ?string {
        if (empty($cartItems)) {
            return null;
        }

        $this->db->beginTransaction();

        try {
            // Hitung total belanja
            $totalAmount = 0;
            foreach ($cartItems as $item) {
                $totalAmount += ($item['price'] * $item['quantity']);
            }

            // Kode transaksi unik
            $orderCode = 'TRX-IKMI-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

            $stmt = $this->db->prepare("
                INSERT INTO orders (order_code, user_id, total_amount, status, payment_method)
                VALUES (:order_code, :user_id, :total_amount, :status, :payment_method)
            ");
            $stmt->execute([
                ':order_code' => $orderCode,
                ':user_id' => $userId,
                ':total_amount' => $totalAmount,
                ':status' => ORDER_LUNAS, // Alur Instan: Langsung tercatat LUNAS secara otomatis
                ':payment_method' => $paymentMethod
            ]);

            $orderId = (int) $this->db->lastInsertId();

            // Masukkan setiap item & kurangi stok produk
            $itemStmt = $this->db->prepare("
                INSERT INTO order_items (order_id, product_id, quantity, unit_price, subtotal)
                VALUES (:order_id, :product_id, :quantity, :unit_price, :subtotal)
            ");

            $stockStmt = $this->db->prepare("
                UPDATE products SET stock = MAX(0, stock - :quantity) WHERE id = :id
            ");

            foreach ($cartItems as $item) {
                $subtotal = $item['price'] * $item['quantity'];
                $itemStmt->execute([
                    ':order_id' => $orderId,
                    ':product_id' => $item['id'],
                    ':quantity' => $item['quantity'],
                    ':unit_price' => $item['price'],
                    ':subtotal' => $subtotal
                ]);

                $stockStmt->execute([
                    ':quantity' => $item['quantity'],
                    ':id' => $item['id']
                ]);
            }

            $this->db->commit();
            return $orderCode;
        } catch (Exception $e) {
            $this->db->rollBack();
            return null;
        }
    }

    public function getUserOrders(int $userId): array {
        $stmt = $this->db->prepare("
            SELECT o.*, 
                   (SELECT COUNT(*) FROM order_items oi WHERE oi.order_id = o.id) as total_items
            FROM orders o
            WHERE o.user_id = :user_id
            ORDER BY o.id DESC
        ");
        $stmt->execute([':user_id' => $userId]);
        return $stmt->fetchAll();
    }

    public function getAllOrders(): array {
        $stmt = $this->db->query("
            SELECT o.*, u.full_name, u.nickname, u.role, u.phone,
                   (SELECT COUNT(*) FROM order_items oi WHERE oi.order_id = o.id) as total_items
            FROM orders o
            JOIN users u ON o.user_id = u.id
            ORDER BY o.id DESC
        ");
        return $stmt->fetchAll();
    }

    public function getOrderDetails(string $orderCode): ?array {
        $stmt = $this->db->prepare("
            SELECT o.*, u.full_name, u.nickname, u.email, u.phone, u.role
            FROM orders o
            JOIN users u ON o.user_id = u.id
            WHERE o.order_code = :order_code
            LIMIT 1
        ");
        $stmt->execute([':order_code' => $orderCode]);
        $order = $stmt->fetch();

        if (!$order) {
            return null;
        }

        $itemStmt = $this->db->prepare("
            SELECT oi.*, p.name as product_name, p.category, p.image_url
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = :order_id
        ");
        $itemStmt->execute([':order_id' => $order['id']]);
        $order['items'] = $itemStmt->fetchAll();

        return $order;
    }

    public function updateStatus(int $orderId, string $status): bool {
        if (!in_array($status, [ORDER_PENDING, ORDER_LUNAS, ORDER_SELESAI])) {
            return false;
        }

        $stmt = $this->db->prepare("UPDATE orders SET status = :status WHERE id = :id");
        return $stmt->execute([
            ':status' => $status,
            ':id' => $orderId
        ]);
    }
}

