<?php

class App {
    protected string $controller = 'HomeController';
    protected string $action = 'index';
    protected array $params = [];

    public function __construct() {
        $url = $this->parseUrl();

        // 1. Deteksi Controller
        if (!empty($url[0])) {
            $controllerName = ucfirst($url[0]) . 'Controller';
            if (file_exists(APP_PATH . '/controllers/' . $controllerName . '.php')) {
                $this->controller = $controllerName;
                unset($url[0]);
            }
        }

        require_once APP_PATH . '/controllers/' . $this->controller . '.php';
        $controllerInstance = new $this->controller();

        // 2. Deteksi Action (Method)
        if (!empty($url[1])) {
            if (method_exists($controllerInstance, $url[1])) {
                $this->action = $url[1];
                unset($url[1]);
            }
        }

        // 3. Simpan sisa parameter
        $this->params = $url ? array_values($url) : [];

        // 4. Jalankan controller action
        call_user_func_array([$controllerInstance, $this->action], $this->params);
    }

    private function parseUrl(): array {
        if (isset($_GET['url']) && !empty(trim($_GET['url']))) {
            return explode('/', filter_var(rtrim($_GET['url'], '/'), FILTER_SANITIZE_URL));
        }

        if (!empty($_SERVER['PATH_INFO'])) {
            return explode('/', filter_var(trim($_SERVER['PATH_INFO'], '/'), FILTER_SANITIZE_URL));
        }

        // Parsing manual dari REQUEST_URI bila mod_rewrite / query string kosong
        $requestUri = parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH);
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? '';
        $baseDir = dirname($scriptName);

        $path = $requestUri;
        if ($baseDir !== '/' && strpos($path, $baseDir) === 0) {
            $path = substr($path, strlen($baseDir));
        }

        $path = trim($path, '/');
        // Hilangkan 'index.php' jika ada
        if (strpos($path, 'index.php') === 0) {
            $path = trim(substr($path, strlen('index.php')), '/');
        }

        if (!empty($path)) {
            return explode('/', filter_var($path, FILTER_SANITIZE_URL));
        }

        return [];
    }
}

