<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <span class="badge bg-dark text-warning fw-bold px-3 py-1 rounded-pill mb-2"><i class="bi bi-shield-lock-fill"></i> Portal Administrator</span>
            <h2 class="fw-bold mb-0">Dashboard Manajemen Koperasi</h2>
            <small class="text-muted"><?= INSTITUTION_NAME ?></small>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= BASE_URL ?>/admin/orders" class="btn btn-outline-primary rounded-pill btn-sm px-3">
                <i class="bi bi-receipt me-1"></i> Kelola Transaksi
            </a>
            <a href="<?= BASE_URL ?>/admin/products" class="btn btn-primary rounded-pill btn-sm px-3">
                <i class="bi bi-boxes me-1"></i> Kelola Produk
            </a>
        </div>
    </div>

    <!-- Stat Cards -->
    <div class="row g-4 mb-5">
        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm p-4 bg-white rounded-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small">Total Anggota</span>
                    <i class="bi bi-people text-primary fs-4"></i>
                </div>
                <h3 class="fw-bold text-dark mb-0"><?= $stats['users'] ?></h3>
                <small class="text-muted">Semua peran pengguna</small>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm p-4 bg-white rounded-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small">Total Transaksi</span>
                    <i class="bi bi-cart-check text-success fs-4"></i>
                </div>
                <h3 class="fw-bold text-dark mb-0"><?= $stats['orders'] ?></h3>
                <small class="text-muted">Seluruh pesanan masuk</small>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm p-4 bg-white rounded-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small">Pesanan Pending</span>
                    <i class="bi bi-hourglass-split text-warning fs-4"></i>
                </div>
                <h3 class="fw-bold text-dark mb-0"><?= $stats['pending'] ?></h3>
                <small class="text-warning fw-semibold">Menunggu pembayaran</small>
            </div>
        </div>

        <div class="col-md-3 col-sm-6">
            <div class="card border-0 shadow-sm p-4 bg-white rounded-4">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="text-muted small">Pendapatan Lunas</span>
                    <i class="bi bi-cash-stack text-success fs-4"></i>
                </div>
                <h4 class="fw-bold text-success mb-0">Rp <?= number_format($stats['revenue'], 0, ',', '.') ?></h4>
                <small class="text-muted">Status Lunas & Selesai</small>
            </div>
        </div>
    </div>

    <!-- Recent Orders Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-5">
        <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-clock-history text-primary me-2"></i>Transaksi Terbaru</h6>
            <a href="<?= BASE_URL ?>/admin/orders" class="small text-primary text-decoration-none">Lihat Semua</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Kode Transaksi</th>
                            <th>Pemesan</th>
                            <th>Peran</th>
                            <th>Total</th>
                            <th>Status Transaksi</th>
                            <th class="text-end pe-4">Ubah Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentOrders)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">Belum ada data pesanan.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentOrders as $ro): ?>
                                <tr>
                                    <td class="ps-4 fw-bold font-monospace text-ikmi-primary">
                                        <?= htmlspecialchars($ro['order_code']) ?>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark"><?= htmlspecialchars($ro['full_name']) ?></div>
                                        <small class="text-muted"><?= htmlspecialchars($ro['phone']) ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border"><?= htmlspecialchars($ro['role']) ?></span>
                                    </td>
                                    <td class="fw-bold text-dark">
                                        Rp <?= number_format($ro['total_amount'], 0, ',', '.') ?>
                                    </td>
                                    <td>
                                        <?php if ($ro['status'] === ORDER_PENDING): ?>
                                            <span class="badge badge-status-pending px-3 py-1 rounded-pill">Pending</span>
                                        <?php elseif ($ro['status'] === ORDER_LUNAS): ?>
                                            <span class="badge badge-status-lunas px-3 py-1 rounded-pill">Lunas</span>
                                        <?php else: ?>
                                            <span class="badge badge-status-selesai px-3 py-1 rounded-pill">Selesai</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <form action="<?= BASE_URL ?>/admin/updateOrderStatus" method="POST" class="d-inline-flex gap-1 justify-content-end">
                                            <input type="hidden" name="order_id" value="<?= $ro['id'] ?>">
                                            <select name="status" class="form-select form-select-sm" style="width: 110px;" onchange="this.form.submit()">
                                                <option value="<?= ORDER_PENDING ?>" <?= ($ro['status'] === ORDER_PENDING) ? 'selected' : '' ?>>Pending</option>
                                                <option value="<?= ORDER_LUNAS ?>" <?= ($ro['status'] === ORDER_LUNAS) ? 'selected' : '' ?>>Lunas</option>
                                                <option value="<?= ORDER_SELESAI ?>" <?= ($ro['status'] === ORDER_SELESAI) ? 'selected' : '' ?>>Selesai</option>
                                            </select>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Data Sivitas Pengguna & Verifikasi Ganda -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="card-header bg-white py-3 px-4 border-bottom">
            <h6 class="fw-bold mb-0 text-dark"><i class="bi bi-people-fill text-success me-2"></i>Daftar Pengguna & Status Verifikasi Ganda</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Nama Lengkap & Panggilan</th>
                            <th>Email</th>
                            <th>No. WhatsApp</th>
                            <th>Peran</th>
                            <th>Institusi</th>
                            <th class="text-center">Email Verified</th>
                            <th class="text-center">WA OTP Verified</th>
                            <th class="text-end pe-4">Status Akun</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $u): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark"><?= htmlspecialchars($u['full_name']) ?></div>
                                    <small class="text-muted">(<?= htmlspecialchars($u['nickname']) ?>)</small>
                                </td>
                                <td class="text-secondary small font-monospace"><?= htmlspecialchars($u['email']) ?></td>
                                <td class="text-secondary small"><?= htmlspecialchars($u['phone']) ?></td>
                                <td>
                                    <span class="badge bg-light text-primary border"><?= htmlspecialchars($u['role']) ?></span>
                                </td>
                                <td>
                                    <small class="text-muted"><?= htmlspecialchars($u['institution']) ?></small>
                                </td>
                                <td class="text-center">
                                    <?= $u['is_email_verified'] ? '<i class="bi bi-check-circle-fill text-success fs-5"></i>' : '<i class="bi bi-x-circle-fill text-danger fs-5"></i>' ?>
                                </td>
                                <td class="text-center">
                                    <?= $u['is_phone_verified'] ? '<i class="bi bi-check-circle-fill text-success fs-5"></i>' : '<i class="bi bi-x-circle-fill text-danger fs-5"></i>' ?>
                                </td>
                                <td class="text-end pe-4">
                                    <span class="badge <?= $u['is_active'] ? 'bg-success' : 'bg-secondary' ?> px-2 py-1">
                                        <?= $u['is_active'] ? 'Aktif' : 'Non-aktif' ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

