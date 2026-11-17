<?php
$user = $_SESSION['user'] ?? null;
$cartCount = !empty($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-ikmi-primary sticky-top shadow-sm py-2">
    <div class="container">
        <!-- Logo & Identitas Kampus -->
        <a class="navbar-brand d-flex align-items-center gap-2" href="<?= BASE_URL ?>">
            <div class="brand-badge bg-warning text-dark fw-bold rounded-3 px-2 py-1 fs-6">
                <i class="bi bi-mortarboard-fill me-1"></i>IKMI
            </div>
            <div>
                <span class="fw-bold tracking-tight d-block fs-6 lh-sm"><?= INSTITUTION_NAME ?></span>
                <small class="text-white-50 fs-xs d-block">E-Koperasi & AI Academic Engine</small>
            </div>
        </a>

        <!-- Mobile Toggle -->
        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent" aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <!-- Nav Items -->
        <div class="collapse navbar-collapse" id="navbarContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-3">
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?= BASE_URL ?>"><i class="bi bi-house-door me-1"></i> Beranda</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?= BASE_URL ?>/shop"><i class="bi bi-bag-check me-1"></i> Katalog Koperasi</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link text-white" href="<?= BASE_URL ?>/chatbot"><i class="bi bi-chat-dots-fill text-warning me-1"></i> AI Chatbot (RAG)</a>
                </li>
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-warning fw-semibold" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-cpu-fill me-1"></i> Modul AI Edukasi
                    </a>
                    <ul class="dropdown-menu border-0 shadow">
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/aiEdu"><i class="bi bi-lightning-charge-fill text-warning me-2"></i> AI Quiz Generator</a></li>
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/aiEdu/explainer"><i class="bi bi-chat-square-quote-fill text-primary me-2"></i> AI Academic Explainer</a></li>
                    </ul>
                </li>
            </ul>

            <!-- Right Nav: Cart, Auth, Roles -->
            <div class="d-flex align-items-center gap-2">
                <!-- Keranjang Belanja -->
                <a href="<?= BASE_URL ?>/shop/cart" class="btn btn-outline-light position-relative me-2 rounded-pill px-3 py-1 btn-sm">
                    <i class="bi bi-cart3 me-1"></i> Keranjang
                    <?php if ($cartCount > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-warning text-dark border border-white">
                            <?= $cartCount ?>
                        </span>
                    <?php endif; ?>
                </a>

                <?php if ($user): ?>
                    <!-- Profil User Logged In -->
                    <div class="dropdown">
                        <button class="btn btn-light dropdown-toggle rounded-pill d-flex align-items-center gap-2 px-3 py-1 shadow-sm btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <span class="badge bg-ikmi-accent text-dark fw-bold"><?= htmlspecialchars($user['role']) ?></span>
                            <span class="fw-semibold text-dark"><?= htmlspecialchars($user['nickname']) ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg mt-2">
                            <li class="px-3 py-2 border-bottom">
                                <small class="text-muted d-block">Masuk sebagai (<?= htmlspecialchars($user['role']) ?>)</small>
                                <span class="fw-bold d-block text-dark"><?= htmlspecialchars($user['full_name']) ?></span>
                                <small class="text-primary d-block font-monospace"><?= htmlspecialchars($user['email']) ?></small>
                            </li>
                            <?php if ($user['role'] === ROLE_ADMIN): ?>
                                <li><a class="dropdown-item text-danger fw-semibold" href="<?= BASE_URL ?>/admin"><i class="bi bi-speedometer2 me-2"></i> Dashboard Admin</a></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/orders"><i class="bi bi-card-checklist me-2"></i> Kelola Transaksi</a></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/products"><i class="bi bi-boxes me-2"></i> Kelola Produk</a></li>
                                <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/settings"><i class="bi bi-gear-fill me-2 text-warning"></i> Pengaturan API Key</a></li>
                                <li><hr class="dropdown-divider"></li>
                            <?php endif; ?>
                            <li><a class="dropdown-item" href="<?= BASE_URL ?>/shop/orders"><i class="bi bi-receipt me-2"></i> Pesanan Saya</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/auth/logout"><i class="bi bi-box-arrow-right me-2"></i> Keluar</a></li>
                        </ul>
                    </div>
                <?php else: ?>
                    <!-- Tombol Masuk & Daftar -->
                    <a href="<?= BASE_URL ?>/auth/login" class="btn btn-outline-light rounded-pill btn-sm px-3">Masuk</a>
                    <a href="<?= BASE_URL ?>/auth/register" class="btn btn-warning text-dark fw-bold rounded-pill btn-sm px-3 shadow-sm">Daftar</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</nav>

