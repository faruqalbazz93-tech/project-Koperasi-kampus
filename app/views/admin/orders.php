<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <span class="badge bg-warning text-dark fw-bold px-3 py-1 rounded-pill mb-2">Manajemen Transaksi</span>
            <h2 class="fw-bold mb-0">Daftar Seluruh Pesanan Koperasi</h2>
            <small class="text-muted"><?= INSTITUTION_NAME ?></small>
        </div>
        <a href="<?= BASE_URL ?>/admin" class="btn btn-outline-secondary rounded-pill btn-sm px-3">
            <i class="bi bi-arrow-left me-1"></i> Dashboard Admin
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Kode Transaksi</th>
                            <th>Tanggal</th>
                            <th>Pemesan</th>
                            <th>Peran</th>
                            <th>Metode Bayar</th>
                            <th>Total Belanja</th>
                            <th>Status Saat Ini</th>
                            <th class="text-end pe-4">Ubah Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">Belum ada transaksi di koperasi.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($orders as $o): ?>
                                <tr>
                                    <td class="ps-4 fw-bold font-monospace text-ikmi-primary">
                                        <a href="<?= BASE_URL ?>/shop/orderDetail/<?= htmlspecialchars($o['order_code']) ?>" class="text-decoration-none">
                                            <?= htmlspecialchars($o['order_code']) ?>
                                        </a>
                                    </td>
                                    <td class="text-secondary small">
                                        <?= date('d M Y, H:i', strtotime($o['created_at'])) ?>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark"><?= htmlspecialchars($o['full_name']) ?></div>
                                        <small class="text-muted"><?= htmlspecialchars($o['phone']) ?></small>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border"><?= htmlspecialchars($o['role']) ?></span>
                                    </td>
                                    <td>
                                        <small class="badge bg-light text-secondary border"><?= htmlspecialchars($o['payment_method']) ?></small>
                                    </td>
                                    <td class="fw-bold text-dark">
                                        Rp <?= number_format($o['total_amount'], 0, ',', '.') ?>
                                    </td>
                                    <td>
                                        <?php if ($o['status'] === ORDER_PENDING): ?>
                                            <span class="badge badge-status-pending px-3 py-1 rounded-pill">Pending</span>
                                        <?php elseif ($o['status'] === ORDER_LUNAS): ?>
                                            <span class="badge badge-status-lunas px-3 py-1 rounded-pill">Lunas</span>
                                        <?php else: ?>
                                            <span class="badge badge-status-selesai px-3 py-1 rounded-pill">Selesai</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <form action="<?= BASE_URL ?>/admin/updateOrderStatus" method="POST" class="d-inline-flex gap-1 justify-content-end">
                                            <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                                            <select name="status" class="form-select form-select-sm" style="width: 120px;" onchange="this.form.submit()">
                                                <option value="<?= ORDER_PENDING ?>" <?= ($o['status'] === ORDER_PENDING) ? 'selected' : '' ?>>Pending</option>
                                                <option value="<?= ORDER_LUNAS ?>" <?= ($o['status'] === ORDER_LUNAS) ? 'selected' : '' ?>>Lunas</option>
                                                <option value="<?= ORDER_SELESAI ?>" <?= ($o['status'] === ORDER_SELESAI) ? 'selected' : '' ?>>Selesai</option>
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
</div>

