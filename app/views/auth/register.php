<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7 col-md-9">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
                <div class="card-header bg-ikmi-primary text-white p-4 text-center">
                    <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold mb-2">Formulir Pendaftaran</span>
                    <h4 class="fw-bold mb-1">Registrasi Anggota Baru</h4>
                    <p class="small text-white-50 mb-0"><?= INSTITUTION_NAME ?></p>
                </div>
                <div class="card-body p-4 p-lg-5 bg-white">
                    <form action="<?= BASE_URL ?>/auth/register" method="POST">
                        
                        <!-- Institusi Otomatis (Readonly) -->
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-secondary">
                                <i class="bi bi-building me-1"></i> Institusi / Koperasi Kampus (Otomatis)
                            </label>
                            <input type="text" class="form-control bg-light fw-bold text-ikmi-primary border-primary-subtle" 
                                   value="<?= INSTITUTION_NAME ?>" readonly name="institution">
                            <div class="form-text text-muted" style="font-size: 0.8rem;">
                                Identitas institusi di-set otomatis ke area kampus STMIK IKMI.
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <!-- Nama Panggilan -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary">
                                    <i class="bi bi-person me-1"></i> Nama Panggilan <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="nickname" class="form-control" placeholder="Contoh: Rian" required
                                       value="<?= htmlspecialchars($old['nickname'] ?? '') ?>">
                            </div>

                            <!-- Nama Lengkap -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary">
                                    <i class="bi bi-person-badge me-1"></i> Nama Lengkap <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="full_name" class="form-control" placeholder="Contoh: Rian Saputra, S.Kom." required
                                       value="<?= htmlspecialchars($old['full_name'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <!-- Email -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary">
                                    <i class="bi bi-envelope me-1"></i> Alamat Email <span class="text-danger">*</span>
                                </label>
                                <input type="email" name="email" class="form-control" placeholder="nama@ikmi.ac.id" required
                                       value="<?= htmlspecialchars($old['email'] ?? '') ?>">
                            </div>

                            <!-- Nomor WhatsApp -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary">
                                    <i class="bi bi-whatsapp me-1 text-success"></i> No. WhatsApp (Aktif) <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="phone" class="form-control" placeholder="08xxxxxxxxxx" required
                                       value="<?= htmlspecialchars($old['phone'] ?? '') ?>">
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <!-- Pilihan Peran Pengguna -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary">
                                    <i class="bi bi-shield-lock me-1"></i> Peran Pengguna <span class="text-danger">*</span>
                                </label>
                                <select name="role" class="form-select" required>
                                    <option value="<?= ROLE_MAHASISWA ?>" selected>Mahasiswa</option>
                                    <option value="<?= ROLE_DOSEN ?>">Dosen</option>
                                    <option value="<?= ROLE_STAFF ?>">Staff</option>
                                    <option value="<?= ROLE_TAMU ?>">Tamu</option>
                                </select>
                            </div>

                            <!-- Password -->
                            <div class="col-md-6">
                                <label class="form-label fw-semibold small text-secondary">
                                    <i class="bi bi-key me-1"></i> Kata Sandi <span class="text-danger">*</span>
                                </label>
                                <input type="password" name="password" class="form-control" placeholder="Minimal 6 karakter" required minlength="6">
                            </div>
                        </div>

                        <!-- Catatan Verifikasi WhatsApp via Fonnte -->
                        <div class="alert alert-info border-0 d-flex align-items-center mb-4 rounded-3" role="alert">
                            <i class="bi bi-whatsapp fs-4 me-3 text-success"></i>
                            <div class="small">
                                <strong>Verifikasi Cepat via WhatsApp:</strong> Sistem tidak menggunakan verifikasi email. Kode OTP 6 digit akan langsung dikirimkan ke nomor WhatsApp Anda menggunakan <strong>Fonnte API Gateway</strong>.
                            </div>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-warning text-dark fw-bold py-2 rounded-pill shadow-sm">
                                <i class="bi bi-whatsapp me-1"></i> Daftar & Dapatkan OTP WhatsApp
                            </button>
                        </div>

                        <div class="text-center">
                            <small class="text-muted">Sudah memiliki akun terverifikasi? <a href="<?= BASE_URL ?>/auth/login" class="text-primary fw-bold text-decoration-none">Masuk di sini</a></small>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

