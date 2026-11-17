<?php

class Controller {
    /**
     * Render view dengan layout utama
     */
    protected function view(string $viewPath, array $data = []): void {
        extract($data);

        $viewFile = APP_PATH . '/views/' . $viewPath . '.php';
        if (!file_exists($viewFile)) {
            die("View file [{$viewPath}] tidak ditemukan.");
        }

        // Simpan view content ke buffer
        ob_start();
        require $viewFile;
        $content = ob_get_clean();

        // Render template utama
        require APP_PATH . '/views/layouts/header.php';
        echo $content;
        require APP_PATH . '/views/layouts/footer.php';
    }

    /**
     * Response JSON untuk REST/AJAX
     */
    protected function json(array $data, int $statusCode = 200): void {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data);
        exit;
    }

    /**
     * Redirect helper
     */
    protected function redirect(string $path): void {
        $url = (strpos($path, 'http') === 0) ? $path : BASE_URL . '/' . ltrim($path, '/');
        header("Location: {$url}");
        exit;
    }

    /**
     * Autentikasi helpers
     */
    protected function getUser(): ?array {
        return $_SESSION['user'] ?? null;
    }

    protected function isLoggedIn(): bool {
        return isset($_SESSION['user']) && !empty($_SESSION['user']['id']);
    }

    protected function requireAuth(): void {
        if (!$this->isLoggedIn()) {
            $_SESSION['flash_error'] = 'Silakan login terlebih dahulu untuk mengakses fitur ini.';
            $this->redirect('auth/login');
        }
    }

    protected function requireRole(array|string $roles): void {
        $this->requireAuth();
        $roles = (array) $roles;
        $user = $this->getUser();

        if (!in_array($user['role'], $roles)) {
            $_SESSION['flash_error'] = 'Akses ditolak: Anda tidak memiliki wewenang untuk halaman ini.';
            $this->redirect('');
        }
    }
}

