<?php
$user = $_SESSION['user'] ?? null;
$cartCount = !empty($_SESSION['cart']) ? array_sum(array_column($_SESSION['cart'], 'quantity')) : 0;

// Helper untuk mengecek active route
$currentUrl = $_SERVER['REQUEST_URI'] ?? '';
if (!function_exists('isRouteActive')) {
    function isRouteActive($path, $currentUrl) {
        if ($path === '' || $path === '/') {
            return (strpos($currentUrl, '/shop') === false && 
                    strpos($currentUrl, '/chatbot') === false && 
                    strpos($currentUrl, '/aiEdu') === false && 
                    strpos($currentUrl, '/admin') === false && 
                    strpos($currentUrl, '/auth') === false);
        }
        return strpos($currentUrl, $path) !== false;
    }
}
?>

<!-- SIDEBAR NAVIGASI UTAMA (Responsive: Fixed Desktop & Offcanvas Drawer on Mobile via offcanvas-lg) -->
<aside class="offcanvas-lg offcanvas-start sidebar-wrapper bg-ikmi-sidebar text-white d-flex flex-column flex-shrink-0" tabindex="-1" id="sidebarOffcanvas" aria-labelledby="sidebarOffcanvasLabel">
    
    <!-- Brand Logo & Kampus Title -->
    <div class="sidebar-brand p-3 border-bottom border-dark-subtle d-flex align-items-center justify-content-between">
        <a href="<?= BASE_URL ?>" class="d-flex align-items-center gap-2 text-decoration-none text-white">
            <div class="brand-icon bg-warning text-dark fw-bold rounded-3 d-flex align-items-center justify-content-center shadow-sm" style="width: 38px; height: 38px;">
                <i class="bi bi-mortarboard-fill fs-5"></i>
            </div>
            <div>
                <span class="fw-bold tracking-tight d-block fs-6 lh-sm text-white">STMIK IKMI</span>
                <small class="text-white-50 fs-xs d-block">Koperasi Kampus & AI</small>
            </div>
        </a>
        <button class="btn btn-sm btn-link text-white-50 d-lg-none p-0 text-decoration-none" type="button" data-bs-dismiss="offcanvas" data-bs-target="#sidebarOffcanvas" aria-label="Close">
            <i class="bi bi-x-lg fs-5"></i>
        </button>
    </div>

    <!-- Navigation Menu Items -->
    <div class="sidebar-content overflow-y-auto flex-grow-1 p-3">
        
        <!-- Grup: Navigasi Utama -->
        <small class="text-uppercase text-white-50 fw-bold fs-xs tracking-wider px-2 d-block mb-2">Navigasi Utama</small>
        <ul class="nav nav-pills flex-column gap-1 mb-4">
            <li class="nav-item">
                <a href="<?= BASE_URL ?>" class="nav-link sidebar-link <?= isRouteActive('/', $currentUrl) ? 'active' : '' ?>">
                    <i class="bi bi-grid-1x2-fill me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= BASE_URL ?>/shop" class="nav-link sidebar-link <?= isRouteActive('shop', $currentUrl) && !isRouteActive('cart', $currentUrl) && !isRouteActive('orders', $currentUrl) ? 'active' : '' ?>">
                    <i class="bi bi-bag-check-fill me-2"></i> Katalog Koperasi
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= BASE_URL ?>/shop/cart" class="nav-link sidebar-link d-flex justify-content-between align-items-center <?= isRouteActive('cart', $currentUrl) ? 'active' : '' ?>">
                    <div>
                        <i class="bi bi-cart3 me-2"></i> Keranjang Belanja
                    </div>
                    <?php if ($cartCount > 0): ?>
                        <span class="badge bg-warning text-dark rounded-pill fw-bold"><?= $cartCount ?></span>
                    <?php endif; ?>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= BASE_URL ?>/shop/orders" class="nav-link sidebar-link <?= isRouteActive('orders', $currentUrl) ? 'active' : '' ?>">
                    <i class="bi bi-receipt-cutoff me-2"></i> Riwayat Transaksi
                </a>
            </li>
        </ul>

        <!-- Grup: AI & Edukasi Cerdas -->
        <small class="text-uppercase text-warning fw-bold fs-xs tracking-wider px-2 d-block mb-2">
            <i class="bi bi-stars me-1"></i> Core Engine AI
        </small>
        <ul class="nav nav-pills flex-column gap-1 mb-4">
            <li class="nav-item">
                <a href="<?= BASE_URL ?>/chatbot" class="nav-link sidebar-link d-flex justify-content-between align-items-center <?= isRouteActive('chatbot', $currentUrl) ? 'active' : '' ?>">
                    <div>
                        <i class="bi bi-chat-dots-fill text-warning me-2"></i> AI Database Chatbot
                    </div>
                    <span class="badge bg-success-subtle text-success fs-2xs px-2 py-0 rounded-pill">RAG Live</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= BASE_URL ?>/aiEdu" class="nav-link sidebar-link <?= isRouteActive('aiEdu', $currentUrl) && !isRouteActive('explainer', $currentUrl) ? 'active' : '' ?>">
                    <i class="bi bi-lightning-charge-fill text-warning me-2"></i> AI Quiz Generator
                </a>
            </li>
            <li class="nav-item">
                <a href="<?= BASE_URL ?>/aiEdu/explainer" class="nav-link sidebar-link <?= isRouteActive('explainer', $currentUrl) ? 'active' : '' ?>">
                    <i class="bi bi-mortarboard-fill text-info me-2"></i> AI Academic Explainer
                </a>
            </li>
        </ul>

        <!-- Grup: Panel Administrator (Role Protected) -->
        <?php if ($user && $user['role'] === ROLE_ADMIN): ?>
            <small class="text-uppercase text-danger fw-bold fs-xs tracking-wider px-2 d-block mb-2">
                <i class="bi bi-shield-lock-fill me-1"></i> Panel Administrator
            </small>
            <ul class="nav nav-pills flex-column gap-1 mb-3">
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/admin" class="nav-link sidebar-link <?= isRouteActive('/admin', $currentUrl) && !isRouteActive('admin/orders', $currentUrl) && !isRouteActive('admin/products', $currentUrl) && !isRouteActive('admin/settings', $currentUrl) ? 'active' : '' ?>">
                        <i class="bi bi-speedometer2 me-2"></i> Ringkasan Eksekutif
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/admin/orders" class="nav-link sidebar-link <?= isRouteActive('admin/orders', $currentUrl) ? 'active' : '' ?>">
                        <i class="bi bi-card-checklist me-2"></i> Kelola Transaksi
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/admin/products" class="nav-link sidebar-link <?= isRouteActive('admin/products', $currentUrl) ? 'active' : '' ?>">
                        <i class="bi bi-boxes me-2"></i> Kelola Produk
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= BASE_URL ?>/admin/settings" class="nav-link sidebar-link d-flex justify-content-between align-items-center <?= isRouteActive('admin/settings', $currentUrl) ? 'active' : '' ?>">
                        <div>
                            <i class="bi bi-gear-fill me-2 text-warning"></i> Pengaturan API Key
                        </div>
                        <span class="badge bg-warning text-dark fs-2xs px-1">Fonnte/AI</span>
                    </a>
                </li>
            </ul>
        <?php endif; ?>

    </div>

    <!-- User Profile & Authentication at Sidebar Bottom -->
    <div class="sidebar-footer p-3 border-top border-dark-subtle bg-black bg-opacity-20">
        <?php if ($user): ?>
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center gap-2 overflow-hidden">
                    <div class="avatar-circle bg-warning text-dark fw-bold rounded-circle d-flex align-items-center justify-content-center flex-shrink-0" style="width: 36px; height: 36px; font-size: 0.85rem;">
                        <?= strtoupper(substr($user['nickname'], 0, 2)) ?>
                    </div>
                    <div class="overflow-hidden">
                        <span class="fw-semibold text-white fs-sm d-block text-truncate"><?= htmlspecialchars($user['nickname']) ?></span>
                        <span class="badge bg-light text-dark fs-2xs px-1 py-0"><?= htmlspecialchars($user['role']) ?></span>
                    </div>
                </div>
                <a href="<?= BASE_URL ?>/auth/logout" class="btn btn-sm btn-outline-danger border-0 p-1 rounded-circle" title="Keluar">
                    <i class="bi bi-box-arrow-right fs-5"></i>
                </a>
            </div>
        <?php else: ?>
            <div class="d-grid gap-2">
                <a href="<?= BASE_URL ?>/auth/login" class="btn btn-outline-light btn-sm rounded-pill">Masuk</a>
                <a href="<?= BASE_URL ?>/auth/register" class="btn btn-warning text-dark fw-bold btn-sm rounded-pill">Daftar Akun</a>
            </div>
        <?php endif; ?>
    </div>

</aside>

