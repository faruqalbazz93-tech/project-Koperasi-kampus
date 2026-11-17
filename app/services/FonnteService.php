<?php

class FonnteService {
    private string $token;
    private Setting $settingModel;

    public function __construct() {
        $this->settingModel = new Setting();
        $this->token = trim($this->settingModel->get('fonnte_token', '') ?? '');
    }

    /**
     * Kirim Pesan WhatsApp via Fonnte API
     */
    public function sendMessage(string $target, string $message): array {
        $target = $this->formatPhoneNumber($target);

        // Jika token belum disetel di Settings Admin, aktifkan Sandbox Simulation Mode
        if (empty($this->token)) {
            $this->logSimulation($target, $message);
            return [
                'status' => true,
                'mode' => 'simulation',
                'message' => 'Token Fonnte belum diatur. Pesan tercatat pada log simulasi kampus.',
                'target' => $target
            ];
        }

        $curl = curl_init();
        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://api.fonnte.com/send',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 20,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => [
                'target' => $target,
                'message' => $message,
                'countryCode' => '62'
            ],
            CURLOPT_HTTPHEADER => [
                'Authorization: ' . $this->token
            ],
        ]);

        $response = curl_exec($curl);
        $error = curl_error($curl);
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        if ($error) {
            $this->logSimulation($target, "[ERROR] " . $error . " | Message: " . $message);
            return [
                'status' => false,
                'message' => 'Gagal menghubungi Fonnte API: ' . $error
            ];
        }

        $result = json_decode($response, true);
        return [
            'status' => ($httpCode === 200 && isset($result['status']) && $result['status'] === true),
            'http_code' => $httpCode,
            'response' => $result,
            'raw' => $response
        ];
    }

    /**
     * Kirim Kode OTP Registrasi WhatsApp
     */
    public function sendOtp(string $phone, string $otp, string $nickname): array {
        $msg = "Halo *{$nickname}*,\n\n"
             . "Kode verifikasi WhatsApp akun Anda di *Koperasi Kampus STMIK IKMI* adalah:\n\n"
             . "🔐 *{$otp}*\n\n"
             . "Kode ini berlaku selama 10 menit. Jangan berikan kode ini kepada siapa pun.\n\n"
             . "—\n"
             . "🏛️ *Koperasi Kampus STMIK IKMI*";

        return $this->sendMessage($phone, $msg);
    }

    /**
     * Kirim Notifikasi Transaksi Sukses / Perubahan Status (Nota Digital WhatsApp)
     */
    public function sendOrderNotification(string $phone, string $orderCode, float $total, string $status, string $name): array {
        $formattedTotal = number_format($total, 0, ',', '.');
        
        if ($status === ORDER_LUNAS) {
            $msg = "Halo *{$name}*,\n\n"
                 . "🎉 *TRANSAKSI BERHASIL & LUNAS!*\n\n"
                 . "Pesanan Anda di *Koperasi Kampus STMIK IKMI* telah otomatis tercatat ke dalam sistem:\n\n"
                 . "📦 *Kode Transaksi*: `{$orderCode}`\n"
                 . "💰 *Total Pembayaran*: Rp {$formattedTotal}\n"
                 . "⚡ *Status*: *LUNAS (Otomatis)*\n\n"
                 . "Stok produk telah otomatis diperbarui. Anda dapat mengambil pesanan di counter Koperasi STMIK IKMI kapan saja.\n\n"
                 . "—\n🏛️ *Koperasi Kampus STMIK IKMI*";
            return $this->sendMessage($phone, $msg);
        }

        $msg = "Halo *{$name}*,\n\n"
             . "Pesanan Anda di *Koperasi Kampus STMIK IKMI* telah diperbarui!\n\n"
             . "📦 *Kode Transaksi*: `{$orderCode}`\n"
             . "💰 *Total*: Rp {$formattedTotal}\n"
             . "⚡ *Status*: *{$status}*\n\n";

        if ($status === ORDER_SELESAI) {
            $msg .= "Barang pesanan telah berhasil diserahterimakan. Terima kasih!\n\n";
        }

        $msg .= "—\n🏛️ *Koperasi Kampus STMIK IKMI*";

        return $this->sendMessage($phone, $msg);
    }

    /**
     * Format Nomor HP ke format Fonnte (08.. atau 62..)
     */
    private function formatPhoneNumber(string $phone): string {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($phone, '0')) {
            $phone = '62' . substr($phone, 1);
        } elseif (str_starts_with($phone, '8')) {
            $phone = '62' . $phone;
        }
        return $phone;
    }

    /**
     * Catat log simulasi saat token belum disetel atau untuk audit
     */
    private function logSimulation(string $target, string $message): void {
        $logFile = STORAGE_PATH . '/fonnte_simulation.log';
        $entry = "[" . date('Y-m-d H:i:s') . "] TARGET: {$target}\nMESSAGE:\n{$message}\n" . str_repeat("-", 50) . "\n";
        @file_put_contents($logFile, $entry, FILE_APPEND);
    }
}

