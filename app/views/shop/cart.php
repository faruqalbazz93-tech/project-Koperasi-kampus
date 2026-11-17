<div class="container py-5">
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                <div class="card-header bg-white py-3 px-4 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-cart3 text-primary me-2"></i>Keranjang Belanja</h5>
                    <span class="badge bg-secondary"><?= count($cartItems) ?> Macam Item</span>
                </div>
                <div class="card-body p-4">
                    <?php if (empty($cartItems)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-cart-x text-muted display-4 d-block mb-3"></i>
                            <h5 class="fw-bold text-secondary">Keranjang belanja Anda masih kosong.</h5>
                            <p class="text-muted small">Pilih perlengkapan kuliah resmi dari katalog koperasi.</p>
                            <a href="<?= BASE_URL ?>/shop" class="btn btn-primary rounded-pill px-4 mt-2">
                                <i class="bi bi-bag-plus me-1"></i> Mulai Belanja
                            </a>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Produk</th>
                                        <th class="text-center">Harga</th>
                                        <th class="text-center" style="width: 140px;">Jumlah</th>
                                        <th class="text-end">Subtotal</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($cartItems as $item): ?>
                                        <tr>
                                            <td>
                                                <div class="d-flex align-items-center gap-3">
                                                    <img src="<?= htmlspecialchars($item['image_url']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" 
                                                         class="rounded-3" style="width: 50px; height: 50px; object-fit: cover;">
                                                    <div>
                                                        <h6 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($item['name']) ?></h6>
                                                        <small class="badge bg-light text-secondary border"><?= htmlspecialchars($item['category']) ?></small>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="text-center">Rp <?= number_format($item['price'], 0, ',', '.') ?></td>
                                            <td class="text-center">
                                                <div class="input-group input-group-sm justify-content-center">
                                                    <form action="<?= BASE_URL ?>/shop/updateCart" method="POST" class="d-inline">
                                                        <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                                        <input type="hidden" name="action" value="decrease">
                                                        <button class="btn btn-outline-secondary" type="submit">-</button>
                                                    </form>
                                                    <span class="input-group-text bg-white px-3 fw-bold"><?= $item['quantity'] ?></span>
                                                    <form action="<?= BASE_URL ?>/shop/updateCart" method="POST" class="d-inline">
                                                        <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                                        <input type="hidden" name="action" value="increase">
                                                        <button class="btn btn-outline-secondary" type="submit">+</button>
                                                    </form>
                                                </div>
                                            </td>
                                            <td class="text-end fw-bold text-ikmi-primary">
                                                Rp <?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>
                                            </td>
                                            <td class="text-end">
                                                <form action="<?= BASE_URL ?>/shop/updateCart" method="POST" class="d-inline">
                                                    <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                                    <input type="hidden" name="action" value="remove">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger border-0" title="Hapus">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <a href="<?= BASE_URL ?>/shop" class="text-decoration-none text-muted small"><i class="bi bi-arrow-left me-1"></i> Lanjut Belanja Produk Lain</a>
        </div>

        <!-- Ringkasan Pesanan -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4 p-4 bg-white sticky-top" style="top: 80px;">
                <h5 class="fw-bold mb-3 text-dark">Ringkasan Pesanan</h5>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Total Pembelian</span>
                    <span class="fw-bold">Rp <?= number_format($totalPrice, 0, ',', '.') ?></span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span class="text-muted">Institusi</span>
                    <span class="badge bg-light text-primary border">STMIK IKMI</span>
                </div>
                <hr class="my-3">
                <div class="d-flex justify-content-between mb-4">
                    <span class="fw-bold text-dark fs-5">Total Akhir</span>
                    <span class="fw-bold text-ikmi-primary fs-5">Rp <?= number_format($totalPrice, 0, ',', '.') ?></span>
                </div>

                <?php if (!empty($cartItems)): ?>
                    <a href="<?= BASE_URL ?>/shop/checkout" class="btn btn-warning text-dark fw-bold w-100 py-2 rounded-pill shadow-sm">
                        <i class="bi bi-credit-card me-1"></i> Lanjut ke Checkout
                    </a>
                <?php else: ?>
                    <button class="btn btn-secondary w-100 py-2 rounded-pill" disabled>Keranjang Kosong</button>
                <?php endif; ?>

                <div class="mt-4 p-3 bg-light rounded-3 border">
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <i class="bi bi-shield-check text-success fs-5"></i>
                        <span class="fw-semibold small">Jaminan Transaksi Resmi</span>
                    </div>
                    <p class="small text-muted mb-0">Pesanan tercatat langsung di sistem Koperasi Kampus STMIK IKMI.</p>
                </div>
            </div>
        </div>
    </div>
</div>

