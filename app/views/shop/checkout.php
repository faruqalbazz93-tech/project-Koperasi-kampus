<div class="container py-5">
    <div class="row g-4 justify-content-center">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-ikmi-primary text-white p-4">
                    <span class="badge bg-warning text-dark px-3 py-1 rounded-pill fw-bold mb-2">Formulir Checkout</span>
                    <h4 class="fw-bold mb-0">Konfirmasi & Pembayaran Pesanan</h4>
                </div>
                <div class="card-body p-4 p-lg-5 bg-white">
                    <form action="<?= BASE_URL ?>/shop/placeOrder" method="POST">
                        
                        <!-- Informasi Pemesan (Civitas Akademika) -->
                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-person-check-fill text-primary me-2"></i>Data Sivitas Akademika</h6>
                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Nama Lengkap</label>
                                <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($user['full_name']) ?>" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Peran / Status Kampus</label>
                                <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($user['role']) ?> (STMIK IKMI)" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted">No. WhatsApp</label>
                                <input type="text" class="form-control bg-light" value="<?= htmlspecialchars($user['phone']) ?>" readonly>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label small text-muted">Unit Koperasi</label>
                                <input type="text" class="form-control bg-light fw-bold text-ikmi-primary" value="<?= INSTITUTION_NAME ?>" readonly>
                            </div>
                        </div>

                        <!-- Pilihan Metode Pembayaran -->
                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-wallet2 text-success me-2"></i>Metode Pembayaran (Selesai Otomatis)</h6>
                        <div class="mb-4">
                            <div class="form-check p-3 border rounded-3 mb-2 bg-light">
                                <input class="form-check-input ms-0 me-3" type="radio" name="payment_method" id="pay_cash" value="QRIS / Kasir Koperasi IKMI" checked>
                                <label class="form-check-label d-block" for="pay_cash">
                                    <strong class="text-dark">QRIS / Saldo Koperasi Kampus</strong>
                                    <small class="d-block text-muted">Pembayaran instan langsung tercatat Lunas di sistem Koperasi Kampus STMIK IKMI.</small>
                                </label>
                            </div>
                            <div class="form-check p-3 border rounded-3 bg-light">
                                <input class="form-check-input ms-0 me-3" type="radio" name="payment_method" id="pay_bank" value="Transfer Rekening Koperasi">
                                <label class="form-check-label d-block" for="pay_bank">
                                    <strong class="text-dark">Transfer Rekening Koperasi STMIK IKMI</strong>
                                    <small class="d-block text-muted">Otomatis terkonfirmasi lunas dan nota transaksi langsung dikirim ke WhatsApp.</small>
                                </label>
                            </div>
                        </div>

                        <!-- Ringkasan Item -->
                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-basket3 text-warning me-2"></i>Daftar Barang Dipesan</h6>
                        <ul class="list-group list-group-flush mb-4 border rounded-3">
                            <?php foreach ($cartItems as $item): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center py-2">
                                    <div>
                                        <span class="fw-semibold text-dark"><?= htmlspecialchars($item['name']) ?></span>
                                        <small class="text-muted d-block"><?= $item['quantity'] ?>x @ Rp <?= number_format($item['price'], 0, ',', '.') ?></small>
                                    </div>
                                    <span class="fw-bold text-dark">Rp <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?></span>
                                </li>
                            <?php endforeach; ?>
                            <li class="list-group-item d-flex justify-content-between align-items-center py-3 bg-light">
                                <strong class="fs-5 text-dark">Total Pembayaran:</strong>
                                <strong class="fs-5 text-ikmi-primary">Rp <?= number_format($totalPrice, 0, ',', '.') ?></strong>
                            </li>
                        </ul>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success text-white fw-bold py-3 rounded-pill shadow">
                                <i class="bi bi-check-circle-fill me-2"></i> Konfirmasi Checkout & Bayar (Instan Lunas)
                            </button>
                            <div class="text-center text-muted small mt-1">
                                <i class="bi bi-shield-check text-success me-1"></i> Transaksi langsung tercatat Lunas, stok terpotong otomatis, dan nota dikirim ke WhatsApp Anda.
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

