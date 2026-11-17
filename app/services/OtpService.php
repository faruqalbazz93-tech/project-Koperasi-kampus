<?php

class OtpService {
    private PDO $db;

    public function __construct() {
        $this->db = Database::getConnection();
    }

    /**
     * Buat OTP WhatsApp (6 digit angka)
     */
    public function generateWaOtp(int $userId): string {
        $otp = (string) random_int(100000, 999999);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        // Nonaktifkan OTP lama yang belum terpakai
        $stmt = $this->db->prepare("UPDATE verifications SET is_used = 1 WHERE user_id = :user_id AND type = 'wa_otp'");
        $stmt->execute([':user_id' => $userId]);

        $stmt = $this->db->prepare("
            INSERT INTO verifications (user_id, type, code, expires_at, is_used)
            VALUES (:user_id, 'wa_otp', :code, :expires_at, 0)
        ");
        $stmt->execute([
            ':user_id' => $userId,
            ':code' => $otp,
            ':expires_at' => $expiresAt
        ]);

        return $otp;
    }

    /**
     * Buat Token Verifikasi Email (Hash string acak)
     */
    public function generateEmailToken(int $userId): string {
        $token = bin2hex(random_bytes(16)); // 32 chars hex
        $expiresAt = date('Y-m-d H:i:s', strtotime('+24 hours'));

        // Nonaktifkan token lama
        $stmt = $this->db->prepare("UPDATE verifications SET is_used = 1 WHERE user_id = :user_id AND type = 'email_token'");
        $stmt->execute([':user_id' => $userId]);

        $stmt = $this->db->prepare("
            INSERT INTO verifications (user_id, type, code, expires_at, is_used)
            VALUES (:user_id, 'email_token', :code, :expires_at, 0)
        ");
        $stmt->execute([
            ':user_id' => $userId,
            ':code' => $token,
            ':expires_at' => $expiresAt
        ]);

        return $token;
    }

    /**
     * Verifikasi WhatsApp OTP
     */
    public function verifyWaOtp(int $userId, string $code): bool {
        $stmt = $this->db->prepare("
            SELECT * FROM verifications
            WHERE user_id = :user_id AND type = 'wa_otp' AND code = :code AND is_used = 0 AND expires_at >= :now
            ORDER BY id DESC LIMIT 1
        ");
        $stmt->execute([
            ':user_id' => $userId,
            ':code' => trim($code),
            ':now' => date('Y-m-d H:i:s')
        ]);
        $record = $stmt->fetch();

        if ($record) {
            // Tandai sudah dipakai
            $update = $this->db->prepare("UPDATE verifications SET is_used = 1 WHERE id = :id");
            $update->execute([':id' => $record['id']]);

            // Update user table
            $updateUser = $this->db->prepare("UPDATE users SET is_phone_verified = 1 WHERE id = :user_id");
            $updateUser->execute([':user_id' => $userId]);

            $this->checkAndActivateUser($userId);
            return true;
        }

        return false;
    }

    /**
     * Verifikasi Email Token
     */
    public function verifyEmailToken(int $userId, string $token): bool {
        $stmt = $this->db->prepare("
            SELECT * FROM verifications
            WHERE user_id = :user_id AND type = 'email_token' AND code = :code AND is_used = 0 AND expires_at >= :now
            ORDER BY id DESC LIMIT 1
        ");
        $stmt->execute([
            ':user_id' => $userId,
            ':code' => trim($token),
            ':now' => date('Y-m-d H:i:s')
        ]);
        $record = $stmt->fetch();

        if ($record) {
            $update = $this->db->prepare("UPDATE verifications SET is_used = 1 WHERE id = :id");
            $update->execute([':id' => $record['id']]);

            $updateUser = $this->db->prepare("UPDATE users SET is_email_verified = 1 WHERE id = :user_id");
            $updateUser->execute([':user_id' => $userId]);

            $this->checkAndActivateUser($userId);
            return true;
        }

        return false;
    }

    /**
     * Cek jika verifikasi WhatsApp OTP sudah valid, maka akun aktif sepenuhnya
     */
    private function checkAndActivateUser(int $userId): void {
        $stmt = $this->db->prepare("SELECT is_phone_verified FROM users WHERE id = :id");
        $stmt->execute([':id' => $userId]);
        $user = $stmt->fetch();

        if ($user && $user['is_phone_verified'] == 1) {
            // Tanpa verifikasi email: akun langsung aktif penuh setelah verifikasi WhatsApp
            $activate = $this->db->prepare("UPDATE users SET is_email_verified = 1, is_active = 1 WHERE id = :id");
            $activate->execute([':id' => $userId]);
        }
    }

    /**
     * Dapatkan kode verifikasi aktif terakhir (untuk simulasi/display kampus)
     */
    public function getActiveVerificationCodes(int $userId): array {
        $stmt = $this->db->prepare("
            SELECT type, code, expires_at FROM verifications
            WHERE user_id = :user_id AND is_used = 0 AND expires_at >= :now
            ORDER BY id DESC
        ");
        $stmt->execute([
            ':user_id' => $userId,
            ':now' => date('Y-m-d H:i:s')
        ]);
        $rows = $stmt->fetchAll();

        $result = ['wa_otp' => null, 'email_token' => null];
        foreach ($rows as $row) {
            if ($row['type'] === 'wa_otp' && !$result['wa_otp']) {
                $result['wa_otp'] = $row['code'];
            }
            if ($row['type'] === 'email_token' && !$result['email_token']) {
                $result['email_token'] = $row['code'];
            }
        }
        return $result;
    }
}

