<?php

class Product {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function getAll(?string $category = null): array {
        if ($category && in_array($category, ['ATK', 'Atribut', 'Buku', 'Lainnya'])) {
            $stmt = $this->db->prepare("SELECT * FROM products WHERE category = :category ORDER BY id DESC");
            $stmt->execute([':category' => $category]);
            return $stmt->fetchAll();
        }

        $stmt = $this->db->query("SELECT * FROM products ORDER BY id DESC");
        return $stmt->fetchAll();
    }

    public function getById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM products WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public function create(array $data): int {
        $stmt = $this->db->prepare("
            INSERT INTO products (category, name, description, price, stock, image_url)
            VALUES (:category, :name, :description, :price, :stock, :image_url)
        ");
        $stmt->execute([
            ':category' => $data['category'],
            ':name' => $data['name'],
            ':description' => $data['description'] ?? '',
            ':price' => $data['price'],
            ':stock' => $data['stock'] ?? 0,
            ':image_url' => $data['image_url'] ?? ''
        ]);
        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, array $data): bool {
        $stmt = $this->db->prepare("
            UPDATE products 
            SET category = :category, name = :name, description = :description, price = :price, stock = :stock, image_url = :image_url
            WHERE id = :id
        ");
        return $stmt->execute([
            ':id' => $id,
            ':category' => $data['category'],
            ':name' => $data['name'],
            ':description' => $data['description'] ?? '',
            ':price' => $data['price'],
            ':stock' => $data['stock'] ?? 0,
            ':image_url' => $data['image_url'] ?? ''
        ]);
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare("DELETE FROM products WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}

