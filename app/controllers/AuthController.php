<?php

class AuthController extends Controller {
    private User $userModel;
    private OtpService $otpService;
    private FonnteService $fonnteService;

    public function __construct() {
        $this->userModel = new User();
        $this->otpService = new OtpService();
        $this->fonnteService = new FonnteService();
    }

    /**
     * Halaman Registrasi
     */
    public function register(): void {
        if ($this->isLoggedIn()) {
            $this->redirect('');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nickname = trim($_POST['nickname'] ?? '');
            $fullName = trim($_POST['full_name'] ?? '');
            $email = trim($_POST['email'] ?? '');
            $phone = trim($_POST['phone'] ?? '');
            $password = $_POST['password'] ?? '';
            $role = $_POST['role'] ?? ROLE_MAHASISWA;

            // Validasi data wajib
            $errors = [];
            if (empty($nickname)) $errors[] = 'Nama Panggilan wajib diisi.';
            if (empty($fullName)) $errors[] = 'Nama Lengkap wajib diisi.';
            if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Format email tidak valid.';
            if (empty($phone)) $errors[] = 'Nomor WhatsApp wajib diisi.';
            if (strlen($password) < 6) $errors[] = 'Password minimal 6 karakter.';

            // Validasi duplikasi
            if ($this->userModel->existsEmail($email)) {
                $errors[] = 'Email ini telah terdaftar dalam sistem.';
            }
            if ($this->userModel->existsPhone($phone)) {
                $errors[] = 'Nomor WhatsApp ini telah terdaftar dalam sistem.';
            }

            if (!empty($errors)) {
                $_SESSION['flash_error'] = implode('<br>', $errors);
                $this->view('auth/register', [
                    'title' => 'Pendaftaran Anggota - ' . INSTITUTION_NAME,
                    'old' => $_POST
                ]);
                return;
            }

            // Simpan pendaftaran dengan identitas institusi otomatis STMIK IKMI
            $userId = $this->userModel->register([
                'nickname' => $nickname,
                'full_name' => $fullName,
                'email' => $email,
                'phone' => $phone,
                'password' => $password,
                'role' => $role
            ]);

            // Picu proses verifikasi WhatsApp OTP (Tanpa Verifikasi Email via Fonnte API)
            $waOtp = $this->otpService->generateWaOtp($userId);
            
            // Kirim WhatsApp OTP via Fonnte
            $this->fonnteService->sendOtp($phone, $waOtp, $nickname);

            $_SESSION['pending_user_id'] = $userId;
            $_SESSION['flash_success'] = "Pendaftaran berhasil! Kode OTP verifikasi telah dikirimkan ke WhatsApp {$phone}.";
            
            $this->redirect('auth/verify/' . $userId);
            return;
        }

        $this->view('auth/register', [
            'title' => 'Pendaftaran Anggota - ' . INSTITUTION_NAME
        ]);
    }

    /**
     * Halaman Verifikasi WhatsApp OTP (Fonnte API Gateway)
     */
    public function verify(?string $userId = null): void {
        $userId = (int) ($userId ?? $_SESSION['pending_user_id'] ?? 0);
        if (!$userId) {
            $this->redirect('auth/login');
        }

        $user = $this->userModel->findById($userId);
        if (!$user) {
            $this->redirect('auth/register');
        }

        // Jika sudah aktif, langsung alihkan ke login
        if ($user['is_active'] == 1 && $user['is_phone_verified'] == 1) {
            $_SESSION['flash_success'] = 'Akun Anda telah terverifikasi WhatsApp dan aktif sepenuhnya.';
            $this->redirect('auth/login');
        }

        // Ambil kode aktif terkini untuk panduan simulasi visual kampus
        $activeCodes = $this->otpService->getActiveVerificationCodes($userId);

        $this->view('auth/verify', [
            'title' => 'Verifikasi WhatsApp - ' . INSTITUTION_NAME,
            'user' => $user,
            'activeCodes' => $activeCodes
        ]);
    }

    /**
     * Submit Verifikasi OTP WhatsApp
     */
    public function verifyWa(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $userId = (int) ($_POST['user_id'] ?? 0);
            $otp = trim($_POST['wa_otp'] ?? '');

            if ($this->otpService->verifyWaOtp($userId, $otp)) {
                $_SESSION['flash_success'] = 'Nomor WhatsApp berhasil diverifikasi! Akun Anda aktif.';
                $this->redirect('auth/login');
            } else {
                $_SESSION['flash_error'] = 'Kode OTP WhatsApp salah atau sudah kedaluwarsa.';
                $this->redirect('auth/verify/' . $userId);
            }
        }
    }

    /**
     * Kirim Ulang OTP WhatsApp via Fonnte
     */
    public function resend(string $userId): void {
        $userId = (int) $userId;
        $user = $this->userModel->findById($userId);

        if ($user) {
            $newOtp = $this->otpService->generateWaOtp($userId);
            $this->fonnteService->sendOtp($user['phone'], $newOtp, $user['nickname']);
            $_SESSION['flash_success'] = "Kode OTP baru telah dikirimkan ke WhatsApp {$user['phone']}.";
        }

        $this->redirect('auth/verify/' . $userId);
    }

    /**
     * Login Pengguna
     */
    public function login(): void {
        if ($this->isLoggedIn()) {
            $this->redirect('');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = trim($_POST['email'] ?? '');
            $password = $_POST['password'] ?? '';

            $user = $this->userModel->findByEmail($email);

            if ($user && password_verify($password, $user['password_hash'])) {
                // Cek status verifikasi WhatsApp
                if ($user['is_active'] == 0 || !$user['is_phone_verified']) {
                    $_SESSION['pending_user_id'] = $user['id'];
                    $_SESSION['flash_error'] = 'Akun belum aktif! Selesaikan verifikasi WhatsApp OTP terlebih dahulu.';
                    $this->redirect('auth/verify/' . $user['id']);
                    return;
                }

                // Set session login
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'nickname' => $user['nickname'],
                    'full_name' => $user['full_name'],
                    'email' => $user['email'],
                    'phone' => $user['phone'],
                    'role' => $user['role'],
                    'institution' => $user['institution']
                ];

                $_SESSION['flash_success'] = "Selamat datang kembali, {$user['nickname']} ({$user['role']})!";
                
                if ($user['role'] === ROLE_ADMIN) {
                    $this->redirect('admin');
                } else {
                    $this->redirect('');
                }
                return;
            }

            $_SESSION['flash_error'] = 'Kombinasi email atau password salah.';
        }

        $this->view('auth/login', [
            'title' => 'Masuk Akun - ' . INSTITUTION_NAME
        ]);
    }

    /**
     * Logout
     */
    public function logout(): void {
        unset($_SESSION['user']);
        session_destroy();
        session_start();
        $_SESSION['flash_success'] = 'Anda telah berhasil keluar dari sistem.';
        $this->redirect('auth/login');
    }
}

