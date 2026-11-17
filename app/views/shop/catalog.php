<div class="container py-5">
    <div class="row align-items-center mb-4">
        <div class="col-md-7">
            <span class="badge bg-warning text-dark fw-bold px-3 py-1 rounded-pill mb-2">E-Commerce Koperasi</span>
            <h2 class="fw-bold mb-1">Katalog Produk Kampus</h2>
            <p class="text-muted mb-0">Penyedia perlengkapan kuliah resmi <?= INSTITUTION_NAME ?></p>
        </div>
        <div class="col-md-5 mt-3 mt-md-0">
            <!-- Filter Kategori Tabs -->
            <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                <a href="<?= BASE_URL ?>/shop" class="btn btn-sm rounded-pill <?= empty($currentCategory) ? 'btn-primary' : 'btn-outline-secondary' ?>">
                    Semua
                </a>
                <a href="<?= BASE_URL ?>/shop?category=ATK" class="btn btn-sm rounded-pill <?= ($currentCategory === 'ATK') ? 'btn-primary' : 'btn-outline-secondary' ?>">
                    <i class="bi bi-pencil me-1"></i> ATK
                </a>
                <a href="<?= BASE_URL ?>/shop?category=Atribut" class="btn btn-sm rounded-pill <?= ($currentCategory === 'Atribut') ? 'btn-primary' : 'btn-outline-secondary' ?>">
                    <i class="bi bi-person-badge me-1"></i> Atribut
                </a>
                <a href="<?= BASE_URL ?>/shop?category=Buku" class="btn btn-sm rounded-pill <?= ($currentCategory === 'Buku') ? 'btn-primary' : 'btn-outline-secondary' ?>">
                    <i class="bi bi-book me-1"></i> Buku
                </a>
            </div>
        </div>
    </div>

    <!-- Product Grid -->
    <div class="row g-4">
        <?php if (empty($products)): ?>
            <div class="col-12 text-center py-5">
                <i class="bi bi-box2 text-muted fs-1 d-block mb-3"></i>
                <h5 class="text-secondary">Belum ada produk di kategori ini.</h5>
                <a href="<?= BASE_URL ?>/shop" class="btn btn-primary rounded-pill btn-sm mt-2">Lihat Semua Kategori</a>
            </div>
        <?php else: ?>
            <?php foreach ($products as $p): ?>
                <div class="col-lg-3 col-md-4 col-sm-6">
                    <div class="card card-product border-0 shadow-sm h-100 overflow-hidden">
                        <div class="product-img-wrapper">
                            <img src="<?= htmlspecialchars($p['image_url']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" loading="lazy">
                            <span class="position-absolute top-0 start-0 m-2 badge bg-primary text-white">
                                <?= htmlspecialchars($p['category']) ?>
                            </span>
                            <span class="position-absolute top-0 end-0 m-2 badge bg-dark bg-opacity-75 text-white">
                                Stok: <?= $p['stock'] ?>
                            </span>
                        </div>
                        <div class="card-body p-3 d-flex flex-column">
                            <h6 class="card-title fw-bold text-dark mb-1" style="min-height: 40px;" title="<?= htmlspecialchars($p['name']) ?>">
                                <?= htmlspecialchars($p['name']) ?>
                            </h6>
                            <p class="card-text text-muted small flex-grow-1 mb-3" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">
                                <?= htmlspecialchars($p['description']) ?>
                            </p>
                            <div class="d-flex align-items-center justify-content-between pt-2 border-top">
                                <div>
                                    <small class="text-muted d-block" style="font-size: 0.75rem;">Harga</small>
                                    <span class="fw-bold text-ikmi-primary fs-6">Rp <?= number_format($p['price'], 0, ',', '.') ?></span>
                                </div>
                                <form action="<?= BASE_URL ?>/shop/addToCart" method="POST" class="ajax-add-cart">
                                    <input type="hidden" name="product_id" value="<?= $p['id'] ?>">
                                    <input type="hidden" name="quantity" value="1">
                                    <button type="submit" class="btn btn-sm btn-warning text-dark fw-bold rounded-pill px-3 shadow-sm" <?= ($p['stock'] <= 0) ? 'disabled' : '' ?>>
                                        <i class="bi bi-cart-plus me-1"></i> Beli
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

