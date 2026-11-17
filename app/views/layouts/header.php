<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? APP_NAME) ?></title>
    <!-- Bootstrap 5.3 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Custom Theme CSS -->
    <link href="<?= BASE_URL ?>/assets/css/style.css" rel="stylesheet">
</head>
<body>

<div class="dashboard-layout-container">
    
    <!-- RESPONSIVE SIDEBAR (Single instance in DOM: Desktop Fixed & Mobile Offcanvas Drawer via offcanvas-lg) -->
    <?php require APP_PATH . '/views/layouts/sidebar.php'; ?>

    <!-- MAIN CONTENT WRAPPER -->
    <div class="main-content-wrapper flex-grow-1 d-flex flex-column min-vh-100">
        
        <!-- Topbar Navigation -->
        <?php require APP_PATH . '/views/layouts/topbar.php'; ?>

        <!-- Main Body Area -->
        <main class="page-content-area flex-grow-1">
            
            <!-- Flash Message Alerts -->
            <div class="container-fluid px-0 mb-3">
                <?php if (!empty($_SESSION['flash_success'])): ?>
                    <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 d-flex align-items-center rounded-3 mb-3" role="alert">
                        <i class="bi bi-check-circle-fill fs-5 me-2 text-success"></i>
                        <div><?= $_SESSION['flash_success'] ?></div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['flash_success']); ?>
                <?php endif; ?>

                <?php if (!empty($_SESSION['flash_error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show shadow-sm border-0 d-flex align-items-center rounded-3 mb-3" role="alert">
                        <i class="bi bi-exclamation-triangle-fill fs-5 me-2 text-danger"></i>
                        <div><?= $_SESSION['flash_error'] ?></div>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                    <?php unset($_SESSION['flash_error']); ?>
                <?php endif; ?>
            </div>
