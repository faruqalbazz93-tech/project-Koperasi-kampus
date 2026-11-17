<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <span class="badge bg-warning text-dark fw-bold px-3 py-1 rounded-pill mb-2">Manajemen Inventaris</span>
            <h2 class="fw-bold mb-0">Katalog Produk Koperasi</h2>
            <small class="text-muted"><?= INSTITUTION_NAME ?></small>
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-warning text-dark fw-bold rounded-pill btn-sm px-3 shadow-sm" data-bs-toggle="modal" data-bs-target="#addProductModal">
                <i class="bi bi-plus-circle me-1"></i> Tambah Produk Baru
            </button>
            <a href="<?= BASE_URL ?>/admin" class="btn btn-outline-secondary rounded-pill btn-sm px-3">
                <i class="bi bi-arrow-left me-1"></i> Dashboard
            </a>
        </div>
    </div>

    <!-- Product Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">Produk</th>
                            <th>Kategori</th>
                            <th>Harga Satuan</th>
                            <th class="text-center">Sisa Stok</th>
                            <th>Deskripsi</th>
                            <th class="text-end pe-4">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($products as $p): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center gap-3">
                                        <img src="<?= htmlspecialchars($p['image_url']) ?>" alt="" class="rounded-3" style="width: 48px; height: 48px; object-fit: cover;">
                                        <div>
                                            <h6 class="fw-bold mb-0 text-dark"><?= htmlspecialchars($p['name']) ?></h6>
                                            <small class="text-muted">ID: #<?= $p['id'] ?></small>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle">
                                        <?= htmlspecialchars($p['category']) ?>
                                    </span>
                                </td>
                                <td class="fw-bold text-ikmi-primary">
                                    Rp <?= number_format($p['price'], 0, ',', '.') ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge <?= ($p['stock'] > 10) ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' ?> px-2 py-1">
                                        <?= $p['stock'] ?> unit
                                    </span>
                                </td>
                                <td>
                                    <small class="text-secondary" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; max-width: 280px;">
                                        <?= htmlspecialchars($p['description']) ?>
                                    </small>
                                </td>
                                <td class="text-end pe-4">
                                    <a href="<?= BASE_URL ?>/admin/deleteProduct/<?= $p['id'] ?>" class="btn btn-sm btn-outline-danger border-0" 
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus produk ini?');" title="Hapus Produk">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal Tambah Produk Baru -->
<div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header bg-ikmi-primary text-white">
                <h5 class="modal-title fw-bold" id="addProductModalLabel"><i class="bi bi-plus-circle me-2 text-warning"></i>Tambah Produk Koperasi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= BASE_URL ?>/admin/addProduct" method="POST">
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Nama Produk <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Contoh: Flashdisk 32GB IKMI Edition" required>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-secondary">Kategori <span class="text-danger">*</span></label>
                            <select name="category" class="form-select" required>
                                <option value="ATK">ATK</option>
                                <option value="Atribut">Atribut</option>
                                <option value="Buku">Buku</option>
                                <option value="Lainnya">Lainnya</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold text-secondary">Stok Awal <span class="text-danger">*</span></label>
                            <input type="number" name="stock" class="form-control" value="50" min="0" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">Harga Satuan (Rp) <span class="text-danger">*</span></label>
                        <input type="number" name="price" class="form-control" placeholder="Contoh: 35000" min="500" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold text-secondary">URL Gambar Produk</label>
                        <input type="url" name="image_url" class="form-control" placeholder="https://images.unsplash.com/...">
                        <div class="form-text small">Kosongkan bila menggunakan gambar default.</div>
                    </div>

                    <div class="mb-2">
                        <label class="form-label small fw-semibold text-secondary">Deskripsi Produk</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Jelaskan spesifikasi perlengkapan kuliah ini..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light p-3">
                    <button type="button" class="btn btn-secondary rounded-pill px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning text-dark fw-bold rounded-pill px-4">Simpan Produk</button>
                </div>
            </form>
        </div>
    </div>
</div>

