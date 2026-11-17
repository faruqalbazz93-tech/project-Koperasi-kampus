<?php
$user = $_SESSION['user'] ?? null;
$cartCount = !empty($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;
?>

<!-- TOPBAR DASHBOARD (Clean, Minimalist Header) -->
<header class="topbar-navbar bg-white border-bottom sticky-top py-2 px-3 px-lg-4 d-flex align-items-center justify-content-between">
    
    <!-- Sisi Kiri: Tombol Toggle Mobile & Info Kampus -->
    <div class="d-flex align-items-center gap-3">
        <!-- Mobile Sidebar Toggle Button -->
        <button class="btn btn-outline-secondary d-lg-none btn-sm rounded-3 px-2 py-1" type="button" data-bs-toggle="offcanvas" data-bs-target="#sidebarOffcanvas" aria-controls="sidebarOffcanvas">
            <i class="bi bi-list fs-5"></i>
        </button>

        <div class="d-none d-sm-flex align-items-center gap-2">
            <span class="badge bg-ikmi-primary text-white rounded-pill px-3 py-1 fw-bold fs-xs">
                <i class="bi bi-building me-1"></i> <?= INSTITUTION_NAME ?>
            </span>
            <span class="text-muted small d-none d-md-inline">&bull; Clean & Modern Dashboard</span>
        </div>
    </div>

    <!-- Sisi Kanan: Action Badges, Cart, & Profile Dropdown -->
    <div class="d-flex align-items-center gap-3">
        
        <!-- Indikator Gateway Fonnte -->
        <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill px-2 py-1 small d-none d-md-flex align-items-center gap-1">
            <i class="bi bi-whatsapp"></i> Fonnte WA Active
        </span>

        <!-- Tombol Keranjang Cepat -->
        <a href="<?= BASE_URL ?>/shop/cart" class="btn btn-light position-relative rounded-pill px-3 py-1 btn-sm border shadow-2xs">
            <i class="bi bi-cart3 text-primary me-1"></i>
            <span class="d-none d-sm-inline">Keranjang</span>
            <?php if ($cartCount > 0): ?>
                <span class="badge bg-warning text-dark rounded-pill ms-1 fw-bold">
                    <?= $cartCount ?>
                </span>
            <?php endif; ?>
        </a>

        <?php if ($user): ?>
            <!-- Dropdown Profil -->
            <div class="dropdown">
                <button class="btn btn-light dropdown-toggle rounded-pill d-flex align-items-center gap-2 px-3 py-1 shadow-2xs border btn-sm" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    <span class="badge bg-ikmi-accent text-dark fw-bold"><?= htmlspecialchars($user['role']) ?></span>
                    <span class="fw-semibold text-dark d-none d-sm-inline"><?= htmlspecialchars($user['nickname']) ?></span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow-lg mt-2 rounded-3">
                    <li class="px-3 py-2 border-bottom">
                        <small class="text-muted d-block">Masuk sebagai (<?= htmlspecialchars($user['role']) ?>)</small>
                        <span class="fw-bold d-block text-dark"><?= htmlspecialchars($user['full_name']) ?></span>
                        <small class="text-primary d-block font-monospace"><?= htmlspecialchars($user['email']) ?></small>
                    </li>
                    <?php if ($user['role'] === ROLE_ADMIN): ?>
                        <li><a class="dropdown-item text-danger fw-semibold" href="<?= BASE_URL ?>/admin"><i class="bi bi-speedometer2 me-2"></i> Dashboard Admin</a></li>
                        <li><a class="dropdown-item" href="<?= BASE_URL ?>/admin/settings"><i class="bi bi-gear-fill me-2 text-warning"></i> Pengaturan API Key</a></li>
                        <li><hr class="dropdown-divider"></li>
                    <?php endif; ?>
                    <li><a class="dropdown-item" href="<?= BASE_URL ?>/shop/orders"><i class="bi bi-receipt me-2"></i> Pesanan Saya</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="<?= BASE_URL ?>/auth/logout"><i class="bi bi-box-arrow-right me-2"></i> Keluar</a></li>
                </ul>
            </div>
        <?php else: ?>
            <a href="<?= BASE_URL ?>/auth/login" class="btn btn-outline-primary rounded-pill btn-sm px-3">Masuk</a>
            <a href="<?= BASE_URL ?>/auth/register" class="btn btn-warning text-dark fw-bold rounded-pill btn-sm px-3 shadow-sm">Daftar</a>
        <?php endif; ?>

    </div>
</header>

