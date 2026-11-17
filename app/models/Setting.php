<?php

class Setting {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    public function get(string $key, ?string $default = null): ?string {
        $stmt = $this->db->prepare("SELECT `value` FROM settings WHERE `key` = :key LIMIT 1");
        $stmt->execute([':key' => $key]);
        $row = $stmt->fetch();
        return $row ? $row['value'] : $default;
    }

    public function set(string $key, ?string $value): bool {
        if (Database::getDriver() === 'sqlite') {
            $stmt = $this->db->prepare("
                INSERT INTO settings (`key`, `value`) 
                VALUES (:key, :val)
                ON CONFLICT(`key`) DO UPDATE SET `value` = :val_up, updated_at = CURRENT_TIMESTAMP
            ");
        } else {
            $stmt = $this->db->prepare("
                INSERT INTO settings (`key`, `value`) 
                VALUES (:key, :val)
                ON DUPLICATE KEY UPDATE `value` = :val_up, updated_at = CURRENT_TIMESTAMP
            ");
        }

        return $stmt->execute([
            ':key' => $key,
            ':val' => $value ?? '',
            ':val_up' => $value ?? ''
        ]);
    }

    public function getAll(): array {
        $stmt = $this->db->query("SELECT `key`, `value` FROM settings");
        $results = [];
        while ($row = $stmt->fetch()) {
            $results[$row['key']] = $row['value'];
        }
        return $results;
    }
}
