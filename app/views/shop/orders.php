<div class="container py-5">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <span class="badge bg-warning text-dark fw-bold px-3 py-1 rounded-pill mb-2">Riwayat Belanja</span>
            <h2 class="fw-bold mb-0">Pesanan Koperasi Saya</h2>
        </div>
        <a href="<?= BASE_URL ?>/shop" class="btn btn-outline-primary rounded-pill btn-sm px-3">
            <i class="bi bi-cart-plus me-1"></i> Belanja Lagi
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="card-body p-0">
            <?php if (empty($orders)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-receipt text-muted display-4 d-block mb-3"></i>
                    <h5 class="fw-bold text-secondary">Belum ada transaksi pesanan.</h5>
                    <p class="text-muted small">Anda belum melakukan pemesanan atribut atau alat tulis.</p>
                    <a href="<?= BASE_URL ?>/shop" class="btn btn-primary rounded-pill px-4 mt-2">Buka Katalog Koperasi</a>
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Kode Transaksi</th>
                                <th>Tanggal</th>
                                <th>Metode Bayar</th>
                                <th>Total Belanja</th>
                                <th>Status Pesanan</th>
                                <th class="text-end pe-4">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $o): ?>
                                <tr>
                                    <td class="ps-4 fw-bold font-monospace text-ikmi-primary">
                                        <?= htmlspecialchars($o['order_code']) ?>
                                    </td>
                                    <td class="text-secondary small">
                                        <?= date('d M Y, H:i', strtotime($o['created_at'])) ?>
                                    </td>
                                    <td>
                                        <small class="badge bg-light text-dark border"><?= htmlspecialchars($o['payment_method']) ?></small>
                                    </td>
                                    <td class="fw-bold text-dark">
                                        Rp <?= number_format($o['total_amount'], 0, ',', '.') ?>
                                    </td>
                                    <td>
                                        <?php if ($o['status'] === ORDER_PENDING): ?>
                                            <span class="badge badge-status-pending px-3 py-2 rounded-pill">
                                                <i class="bi bi-clock me-1"></i> Pending
                                            </span>
                                        <?php elseif ($o['status'] === ORDER_LUNAS): ?>
                                            <span class="badge badge-status-lunas px-3 py-2 rounded-pill">
                                                <i class="bi bi-credit-card-fill me-1"></i> Lunas
                                            </span>
                                        <?php else: ?>
                                            <span class="badge badge-status-selesai px-3 py-2 rounded-pill">
                                                <i class="bi bi-check2-all me-1"></i> Selesai
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <a href="<?= BASE_URL ?>/shop/orderDetail/<?= htmlspecialchars($o['order_code']) ?>" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                            <i class="bi bi-eye"></i> Detail
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

