<?php
$user = $_SESSION['user'] ?? null;
?>

<!-- Executive Dashboard Header -->
<div class="row align-items-center mb-4 g-3">
    <div class="col-md-7">
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-warning text-dark fw-bold px-3 py-1 rounded-pill fs-xs">
                <i class="bi bi-shield-check"></i> Dashboard Resmi
            </span>
            <small class="text-muted"><?= date('l, d F Y') ?></small>
        </div>
        <h2 class="fw-bold mb-0 text-dark">
            Selamat Datang<?= $user ? ', ' . htmlspecialchars($user['nickname']) : '' ?> 👋
        </h2>
        <p class="text-secondary small mb-0">Portal Terpadu E-Koperasi Kampus & AI Academic Engine STMIK IKMI</p>
    </div>
    <div class="col-md-5 text-md-end">
        <div class="d-flex flex-wrap gap-2 justify-content-md-end">
            <a href="<?= BASE_URL ?>/chatbot" class="btn btn-warning text-dark fw-bold rounded-pill btn-sm px-3 shadow-sm">
                <i class="bi bi-chat-dots-fill me-1"></i> Tanya AI Chatbot
            </a>
            <a href="<?= BASE_URL ?>/shop" class="btn btn-outline-primary rounded-pill btn-sm px-3">
                <i class="bi bi-bag-check me-1"></i> Belanja Kebutuhan
            </a>
        </div>
    </div>
</div>

<!-- 4 Clean Stat Metric Cards -->
<div class="row g-3 mb-4">
    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-2xs p-3 rounded-4 bg-white">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-secondary small fw-medium">Produk Koperasi</span>
                <div class="bg-primary-subtle text-primary p-2 rounded-3">
                    <i class="bi bi-box-seam fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold text-dark mb-0"><?= $stats['products'] ?></h3>
            <small class="text-muted fs-xs">ATK, Atribut & Buku Kuliah</small>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-2xs p-3 rounded-4 bg-white">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-secondary small fw-medium">Anggota Terdaftar</span>
                <div class="bg-success-subtle text-success p-2 rounded-3">
                    <i class="bi bi-people fs-5"></i>
                </div>
            </div>
            <h3 class="fw-bold text-dark mb-0"><?= $stats['members'] ?></h3>
            <small class="text-success fs-xs fw-semibold">Terverifikasi WhatsApp</small>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-2xs p-3 rounded-4 bg-white">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-secondary small fw-medium">Total Pesanan</span>
                <div class="bg-warning-subtle text-warning p-2 rounded-3">
                    <i class="bi bi-receipt fs-5 text-dark"></i>
                </div>
            </div>
            <h3 class="fw-bold text-dark mb-0"><?= $stats['orders'] ?></h3>
            <small class="text-muted fs-xs">Tercatat di Sistem</small>
        </div>
    </div>

    <div class="col-xl-3 col-sm-6">
        <div class="card border-0 shadow-2xs p-3 rounded-4 bg-white">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <span class="text-secondary small fw-medium">Gateway Service</span>
                <div class="bg-info-subtle text-info p-2 rounded-3">
                    <i class="bi bi-cpu fs-5"></i>
                </div>
            </div>
            <div class="d-flex align-items-center gap-1">
                <span class="badge bg-success text-white fs-xs px-2 py-1">Fonnte WA</span>
                <span class="badge bg-primary text-white fs-xs px-2 py-1">RAG Core</span>
            </div>
            <small class="text-muted fs-xs mt-1 d-block">Sistem Berjalan Aktif</small>
        </div>
    </div>
</div>

<!-- Main Dashboard Grid -->
<div class="row g-4 mb-4">
    
    <!-- Sisi Kiri: Katalog Produk Unggulan & Quick AI Chatbot -->
    <div class="col-lg-8">
        
        <!-- Banner Interaktif AI Chatbot Database -->
        <div class="card border-0 shadow-sm rounded-4 p-4 mb-4 bg-gradient text-white position-relative overflow-hidden" 
             style="background: linear-gradient(135deg, #0a193b 0%, #0d3284 100%);">
            <div class="row align-items-center position-relative z-1">
                <div class="col-md-8">
                    <span class="badge bg-warning text-dark fw-bold px-3 py-1 rounded-pill mb-2 fs-xs">
                        <i class="bi bi-robot"></i> Text-to-SQL & RAG Engine
                    </span>
                    <h4 class="fw-bold mb-2">AI Chatbot Pembaca Database Kampus</h4>
                    <p class="text-white-50 small mb-3">
                        Tanyakan stok perlengkapan kuliah, rekapitulasi pesanan harian, atau bahas materi kuis secara real-time langsung dari basis data koperasi.
                    </p>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="<?= BASE_URL ?>/chatbot" class="btn btn-warning text-dark fw-bold rounded-pill btn-sm px-3 shadow-sm">
                            <i class="bi bi-chat-text-fill me-1"></i> Buka Chatbot Sekarang
                        </a>
                        <a href="<?= BASE_URL ?>/aiEdu/explainer" class="btn btn-outline-light rounded-pill btn-sm px-3">
                            <i class="bi bi-mortarboard me-1"></i> Bedah Konsep Soal
                        </a>
                    </div>
                </div>
                <div class="col-md-4 text-center d-none d-md-block">
                    <i class="bi bi-cpu-fill text-warning opacity-75" style="font-size: 6.5rem;"></i>
                </div>
            </div>
        </div>

        <!-- Katalog Produk Koperasi Pilihan -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <div>
                    <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-bag-check-fill text-primary me-2"></i>Katalog Kebutuhan Kampus</h5>
                    <small class="text-muted">Perlengkapan resmi mahasiswa & civitas STMIK IKMI</small>
                </div>
                <a href="<?= BASE_URL ?>/shop" class="btn btn-outline-primary rounded-pill btn-sm px-3">
                    Lihat Semua Produk <i class="bi bi-arrow-right"></i>
                </a>
            </div>

            <div class="row g-3">
                <?php foreach ($featuredProducts as $product): ?>
                    <div class="col-md-6">
                        <div class="card card-product border h-100 rounded-3 overflow-hidden shadow-2xs">
                            <div class="row g-0 h-100 align-items-center">
                                <div class="col-4" style="height: 120px;">
                                    <img src="<?= htmlspecialchars($product['image_url']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" 
                                         class="w-100 h-100 object-fit-cover">
                                </div>
                                <div class="col-8 p-3 d-flex flex-column justify-content-between">
                                    <div>
                                        <span class="badge bg-light text-primary border fs-2xs mb-1"><?= htmlspecialchars($product['category']) ?></span>
                                        <h6 class="fw-bold text-dark mb-1 text-truncate" title="<?= htmlspecialchars($product['name']) ?>">
                                            <?= htmlspecialchars($product['name']) ?>
                                        </h6>
                                        <span class="fw-bold text-ikmi-primary fs-sm">Rp <?= number_format($product['price'], 0, ',', '.') ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                                        <small class="text-muted fs-xs">Stok: <strong><?= $product['stock'] ?></strong></small>
                                        <form action="<?= BASE_URL ?>/shop/addToCart" method="POST" class="ajax-add-cart">
                                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                            <input type="hidden" name="quantity" value="1">
                                            <button type="submit" class="btn btn-sm btn-primary rounded-pill px-2 py-0 fs-xs">
                                                <i class="bi bi-cart-plus"></i> Beli
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>

    <!-- Sisi Kanan: Status Sivitas, Quick Generator, & Notifikasi Gateway -->
    <div class="col-lg-4">
        
        <!-- Kartu Identitas & Sivitas Kampus -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
            <h6 class="fw-bold text-dark mb-2"><i class="bi bi-mortarboard-fill text-primary me-2"></i>Sivitas Akademika IKMI</h6>
            <p class="text-secondary small mb-3">
                Pendaftaran anggota terbuka untuk Mahasiswa, Dosen, dan Staff tanpa perlu verifikasi email.
            </p>
            <div class="d-flex flex-wrap gap-2 mb-3">
                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1">Mahasiswa</span>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1">Dosen</span>
                <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1">Staff</span>
                <span class="badge bg-secondary-subtle text-secondary border border-secondary-subtle px-2 py-1">Tamu</span>
            </div>
            <div class="p-3 bg-light rounded-3 border">
                <small class="text-muted d-block fs-xs">Institusi Terdaftar:</small>
                <span class="fw-bold text-ikmi-primary small"><?= INSTITUTION_NAME ?></span>
            </div>
        </div>

        <!-- Kartu Quick AI Quiz Generator -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-white mb-4">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h6 class="fw-bold text-dark mb-0"><i class="bi bi-lightning-fill text-warning me-1"></i>AI Quiz Generator</h6>
                <span class="badge bg-warning-subtle text-dark fs-2xs">Cerdas</span>
            </div>
            <p class="text-secondary small mb-3">
                Simulasikan ujian Pilihan Ganda & Essay otomatis untuk menguji pemahaman materi kuliah.
            </p>
            <a href="<?= BASE_URL ?>/aiEdu" class="btn btn-outline-warning text-dark fw-bold rounded-pill btn-sm w-100">
                Mulai Kuis Akademik
            </a>
        </div>

        <!-- Kartu Notifikasi WhatsApp Fonnte -->
        <div class="card border-0 shadow-sm rounded-4 p-4 bg-light border">
            <div class="d-flex align-items-center gap-2 mb-2">
                <i class="bi bi-whatsapp text-success fs-5"></i>
                <h6 class="fw-bold text-dark mb-0">Notifikasi WA Otomatis</h6>
            </div>
            <p class="text-secondary small mb-0">
                Setiap pemesanan atau perubahan status pesanan otomatis memicu pesan WhatsApp langsung ke nomor ponsel pemesan via Fonnte Gateway.
            </p>
        </div>

    </div>

</div>
