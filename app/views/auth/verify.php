<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-ikmi-primary text-white p-4 text-center">
                    <span class="badge bg-success px-3 py-1 rounded-pill fw-bold mb-2">
                        <i class="bi bi-whatsapp me-1"></i> Fonnte Gateway
                    </span>
                    <h4 class="fw-bold mb-1">Verifikasi Nomor WhatsApp</h4>
                    <p class="small text-white-50 mb-0"><?= htmlspecialchars($user['institution']) ?></p>
                </div>
                <div class="card-body p-4 p-lg-5 bg-white">
                    
                    <!-- Detail Pengguna -->
                    <div class="p-3 bg-light rounded-3 mb-4 border">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <h6 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($user['full_name']) ?></h6>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle"><?= htmlspecialchars($user['role']) ?></span>
                        </div>
                        <div class="small text-muted mb-1"><i class="bi bi-envelope me-1"></i><?= htmlspecialchars($user['email']) ?></div>
                        <div class="small fw-semibold text-dark"><i class="bi bi-whatsapp text-success me-1"></i><?= htmlspecialchars($user['phone']) ?></div>
                    </div>

                    <!-- Form Verifikasi WhatsApp OTP -->
                    <div class="card border border-success-subtle bg-light p-4 rounded-4 mb-4">
                        <div class="text-center mb-3">
                            <i class="bi bi-shield-check text-success fs-1 mb-2 d-inline-block"></i>
                            <h5 class="fw-bold text-dark mb-1">Masukkan 6 Digit Kode OTP</h5>
                            <small class="text-muted">Kode verifikasi telah dikirimkan ke WhatsApp Anda melalui Fonnte API Gateway.</small>
                        </div>

                        <form action="<?= BASE_URL ?>/auth/verifyWa" method="POST">
                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                            <div class="mb-3">
                                <input type="text" name="wa_otp" id="input_wa_otp" 
                                       class="form-control text-center fs-3 fw-bold letter-spacing-2 border-2 border-success py-2" 
                                       placeholder="______" maxlength="6" required pattern="\d{6}" autofocus>
                                <div class="form-text text-center small mt-2">Kode OTP aktif selama 10 menit.</div>
                            </div>
                            <button type="submit" class="btn btn-success w-100 rounded-pill py-2 fw-bold shadow-sm">
                                <i class="bi bi-check-circle-fill me-1"></i> Verifikasi Akun Sekarang
                            </button>
                        </form>
                    </div>

                    <div class="text-center">
                        <a href="<?= BASE_URL ?>/auth/resend/<?= $user['id'] ?>" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                            <i class="bi bi-arrow-clockwise me-1"></i> Kirim Ulang OTP WhatsApp
                        </a>
                    </div>
                </div>
            </div>

            <!-- Panel Simulator Kampus (Memudahkan Evaluasi & Pengujian) -->
            <div class="card border-0 shadow-sm bg-dark text-white rounded-4 p-4">
                <div class="d-flex align-items-center justify-content-between mb-3 border-bottom border-secondary pb-2">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge bg-warning text-dark"><i class="bi bi-terminal"></i> Fonnte Sandbox</span>
                        <h6 class="fw-bold mb-0">Simulation Log (Sandbox Mode)</h6>
                    </div>
                    <small class="text-white-50">STMIK IKMI Core</small>
                </div>
                <p class="small text-white-50 mb-3">
                    Bila token Fonnte belum dimasukkan di menu Pengaturan Admin, kode OTP yang digenerate sistem dapat disalin langsung dari kotak di bawah:
                </p>
                <div class="bg-black bg-opacity-50 p-3 rounded-3 border border-secondary d-flex align-items-center justify-content-between">
                    <div>
                        <small class="text-white-50 d-block mb-1"><i class="bi bi-whatsapp text-success me-1"></i> WhatsApp OTP Aktif:</small>
                        <code class="fs-4 text-warning fw-bold"><?= $activeCodes['wa_otp'] ?? 'SUDAH TERPAKAI / TIDAK ADA' ?></code>
                    </div>
                    <?php if (!empty($activeCodes['wa_otp']) && !$user['is_phone_verified']): ?>
                        <button type="button" class="btn btn-outline-light btn-sm rounded-pill" 
                                onclick="document.getElementById('input_wa_otp').value = '<?= $activeCodes['wa_otp'] ?>';">
                            <i class="bi bi-box-arrow-in-down-left"></i> Tempel Cepat
                        </button>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>
