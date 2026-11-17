<?php

class User {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Daftarkan pengguna baru dengan identitas terikat ke STMIK IKMI
     */
    public function register(array $data): int {
        // Enforce aturan institusi secara otomatis
        $institution = INSTITUTION_NAME;

        $stmt = $this->db->prepare("
            INSERT INTO users (nickname, full_name, email, phone, password_hash, institution, role, is_email_verified, is_phone_verified, is_active)
            VALUES (:nickname, :full_name, :email, :phone, :password_hash, :institution, :role, 0, 0, 0)
        ");

        $stmt->execute([
            ':nickname' => trim($data['nickname']),
            ':full_name' => trim($data['full_name']),
            ':email' => strtolower(trim($data['email'])),
            ':phone' => trim($data['phone']),
            ':password_hash' => password_hash($data['password'], PASSWORD_BCRYPT),
            ':institution' => $institution,
            ':role' => $data['role'] ?? ROLE_MAHASISWA
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function findByEmail(string $email): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => strtolower(trim($email))]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public function existsEmail(string $email): bool {
        return (bool) $this->findByEmail($email);
    }

    public function existsPhone(string $phone): bool {
        $stmt = $this->db->prepare("SELECT COUNT(*) as count FROM users WHERE phone = :phone");
        $stmt->execute([':phone' => trim($phone)]);
        return ((int) $stmt->fetch()['count']) > 0;
    }

    public function getAllUsers(): array {
        $stmt = $this->db->query("SELECT id, nickname, full_name, email, phone, institution, role, is_email_verified, is_phone_verified, is_active, created_at FROM users ORDER BY id DESC");
        return $stmt->fetchAll();
    }
}

