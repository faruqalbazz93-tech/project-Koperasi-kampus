<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white mb-4">
                <div class="card-header bg-ikmi-primary text-white p-4 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold mb-1">Faktur Transaksi</span>
                        <h4 class="fw-bold mb-0">Invoice #<?= htmlspecialchars($order['order_code']) ?></h4>
                        <small class="text-white-50"><?= INSTITUTION_NAME ?></small>
                    </div>
                    <div class="text-end">
                        <?php if ($order['status'] === ORDER_PENDING): ?>
                            <span class="badge badge-status-pending px-3 py-2 rounded-pill fs-6">
                                <i class="bi bi-clock me-1"></i> PENDING
                            </span>
                        <?php elseif ($order['status'] === ORDER_LUNAS): ?>
                            <span class="badge badge-status-lunas px-3 py-2 rounded-pill fs-6">
                                <i class="bi bi-credit-card-fill me-1"></i> LUNAS
                            </span>
                        <?php else: ?>
                            <span class="badge badge-status-selesai px-3 py-2 rounded-pill fs-6">
                                <i class="bi bi-check2-all me-1"></i> SELESAI
                            </span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card-body p-4 p-lg-5">
                    <!-- Informasi Penerima & Tanggal -->
                    <div class="row g-4 mb-4 pb-3 border-bottom">
                        <div class="col-sm-6">
                            <small class="text-muted d-block text-uppercase fw-bold fs-xs">Diterbitkan Untuk:</small>
                            <h6 class="fw-bold text-dark mb-1"><?= htmlspecialchars($order['full_name']) ?> (<?= htmlspecialchars($order['nickname']) ?>)</h6>
                            <div class="small text-secondary mb-1">Peran: <?= htmlspecialchars($order['role']) ?></div>
                            <div class="small text-secondary"><i class="bi bi-whatsapp text-success me-1"></i><?= htmlspecialchars($order['phone']) ?></div>
                        </div>
                        <div class="col-sm-6 text-sm-end">
                            <small class="text-muted d-block text-uppercase fw-bold fs-xs">Waktu Transaksi:</small>
                            <div class="fw-bold text-dark mb-1"><?= date('d F Y, H:i', strtotime($order['created_at'])) ?> WIB</div>
                            <small class="text-muted d-block">Metode Pembayaran:</small>
                            <span class="badge bg-light text-dark border"><?= htmlspecialchars($order['payment_method']) ?></span>
                        </div>
                    </div>

                    <!-- Rincian Produk -->
                    <h6 class="fw-bold text-dark mb-3"><i class="bi bi-box-seam text-primary me-2"></i>Rincian Barang Koperasi</h6>
                    <div class="table-responsive mb-4">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Item</th>
                                    <th class="text-center">Kategori</th>
                                    <th class="text-center">Harga Satuan</th>
                                    <th class="text-center">Qty</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($order['items'] as $item): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="" class="rounded" style="width: 40px; height: 40px; object-fit: cover;">
                                                <span class="fw-semibold text-dark"><?= htmlspecialchars($item['product_name']) ?></span>
                                            </div>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-light text-secondary border"><?= htmlspecialchars($item['category']) ?></span>
                                        </td>
                                        <td class="text-center">Rp <?= number_format($item['unit_price'], 0, ',', '.') ?></td>
                                        <td class="text-center fw-bold"><?= $item['quantity'] ?></td>
                                        <td class="text-end fw-bold text-ikmi-primary">Rp <?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <th colspan="4" class="text-end fs-6">Total Pembayaran:</th>
                                    <th class="text-end fs-5 text-ikmi-primary fw-bold">Rp <?= number_format($order['total_amount'], 0, ',', '.') ?></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Status Transaksi Otomatis -->
                    <?php if ($order['status'] === ORDER_LUNAS || $order['status'] === ORDER_SELESAI): ?>
                        <div class="alert alert-success border-0 rounded-3 p-3 d-flex align-items-center mb-0 shadow-sm">
                            <i class="bi bi-check-circle-fill fs-2 text-success me-3"></i>
                            <div>
                                <h6 class="fw-bold mb-1 text-success"><i class="bi bi-patch-check-fill me-1"></i> Transaksi Berhasil!</h6>
                                <p class="small mb-0 text-dark">
                                    Pesanan Anda telah tercatat dan stok telah diperbarui secara otomatis. Nota digital telah dikirimkan ke WhatsApp terdaftar. Anda dapat langsung mengambil barang di counter Koperasi Kampus STMIK IKMI.
                                </p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info border-0 rounded-3 p-3 d-flex align-items-center mb-0">
                            <i class="bi bi-info-circle-fill fs-3 text-info me-3"></i>
                            <div class="small">
                                <strong>Status Transaksi:</strong> Pesanan sedang diproses dalam sistem Koperasi Kampus STMIK IKMI.
                            </div>
                        </div>
                    <?php endif; ?>

                </div>
                <div class="card-footer bg-light p-3 d-flex flex-wrap justify-content-between align-items-center gap-2">
                    <div class="d-flex gap-2">
                        <a href="<?= BASE_URL ?>/shop/orders" class="btn btn-outline-secondary btn-sm rounded-pill px-3">
                            <i class="bi bi-receipt me-1"></i> Riwayat Pembelian
                        </a>
                        <a href="<?= BASE_URL ?>/shop" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                            <i class="bi bi-bag-plus me-1"></i> Belanja Lagi
                        </a>
                    </div>
                    <button type="button" class="btn btn-dark btn-sm rounded-pill px-3 shadow-sm" onclick="window.print();">
                        <i class="bi bi-printer-fill me-1"></i> Cetak Struk Mandiri
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

