<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-5 col-md-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-ikmi-primary text-white p-4 text-center">
                    <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold mb-2">Portal Akses</span>
                    <h4 class="fw-bold mb-1">Masuk ke Sistem</h4>
                    <p class="small text-white-50 mb-0"><?= INSTITUTION_NAME ?></p>
                </div>
                <div class="card-body p-4 p-lg-5 bg-white">
                    <form action="<?= BASE_URL ?>/auth/login" method="POST">
                        <div class="mb-3">
                            <label class="form-label fw-semibold small text-secondary">
                                <i class="bi bi-envelope me-1"></i> Alamat Email Kampus
                            </label>
                            <input type="email" name="email" id="login_email" class="form-control" placeholder="nama@ikmi.ac.id" required>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold small text-secondary">
                                <i class="bi bi-key me-1"></i> Kata Sandi
                            </label>
                            <input type="password" name="password" id="login_password" class="form-control" placeholder="Kata sandi akun" required>
                        </div>

                        <div class="d-grid mb-3">
                            <button type="submit" class="btn btn-warning text-dark fw-bold py-2 rounded-pill shadow-sm">
                                <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Sekarang
                            </button>
                        </div>

                        <div class="text-center">
                            <small class="text-muted">Belum memiliki akun? <a href="<?= BASE_URL ?>/auth/register" class="text-primary fw-bold text-decoration-none">Daftar Anggota Baru</a></small>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Demo Quick Login Accounts for STMIK IKMI Evaluator -->
            <div class="card border-0 shadow-sm bg-light p-3 rounded-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <small class="fw-bold text-secondary text-uppercase"><i class="bi bi-key-fill text-warning me-1"></i> Akun Login Cepat (Tiap Role):</small>
                    <span class="badge bg-primary-subtle text-primary">Demo Ready</span>
                </div>
                <div class="d-flex flex-column gap-2">
                    <!-- 1. Administrator -->
                    <button type="button" class="btn btn-outline-dark btn-sm text-start d-flex justify-content-between align-items-center rounded-3 bg-white"
                            onclick="document.getElementById('login_email').value='admin@ikmi.ac.id'; document.getElementById('login_password').value='admin123';">
                        <div>
                            <span class="badge bg-dark text-warning me-1">Administrator</span>
                            <small class="fw-semibold">admin@ikmi.ac.id</small>
                        </div>
                        <small class="text-muted">Pass: admin123</small>
                    </button>

                    <!-- 2. Dosen -->
                    <button type="button" class="btn btn-outline-success btn-sm text-start d-flex justify-content-between align-items-center rounded-3 bg-white"
                            onclick="document.getElementById('login_email').value='budi.dosen@ikmi.ac.id'; document.getElementById('login_password').value='dosen123';">
                        <div>
                            <span class="badge bg-success text-white me-1">Dosen</span>
                            <small class="fw-semibold">budi.dosen@ikmi.ac.id</small>
                        </div>
                        <small class="text-muted">Pass: dosen123</small>
                    </button>

                    <!-- 3. Mahasiswa -->
                    <button type="button" class="btn btn-outline-primary btn-sm text-start d-flex justify-content-between align-items-center rounded-3 bg-white"
                            onclick="document.getElementById('login_email').value='rian.mahasiswa@ikmi.ac.id'; document.getElementById('login_password').value='mhs123';">
                        <div>
                            <span class="badge bg-primary text-white me-1">Mahasiswa</span>
                            <small class="fw-semibold">rian.mahasiswa@ikmi.ac.id</small>
                        </div>
                        <small class="text-muted">Pass: mhs123</small>
                    </button>

                    <!-- 4. Staff -->
                    <button type="button" class="btn btn-outline-info btn-sm text-start d-flex justify-content-between align-items-center rounded-3 bg-white"
                            onclick="document.getElementById('login_email').value='siti.staff@ikmi.ac.id'; document.getElementById('login_password').value='staff123';">
                        <div>
                            <span class="badge bg-info text-dark me-1">Staff</span>
                            <small class="fw-semibold">siti.staff@ikmi.ac.id</small>
                        </div>
                        <small class="text-muted">Pass: staff123</small>
                    </button>

                    <!-- 5. Tamu -->
                    <button type="button" class="btn btn-outline-secondary btn-sm text-start d-flex justify-content-between align-items-center rounded-3 bg-white"
                            onclick="document.getElementById('login_email').value='guest@ikmi.ac.id'; document.getElementById('login_password').value='tamu123';">
                        <div>
                            <span class="badge bg-secondary text-white me-1">Tamu</span>
                            <small class="fw-semibold">guest@ikmi.ac.id</small>
                        </div>
                        <small class="text-muted">Pass: tamu123</small>
                    </button>
                </div>
            </div>

        </div>
    </div>
</div>

