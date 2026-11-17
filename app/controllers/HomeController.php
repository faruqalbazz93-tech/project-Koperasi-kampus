<?php

class HomeController extends Controller {
    public function index(): void {
        $productModel = new Product();
        $featuredProducts = array_slice($productModel->getAll(), 0, 4);

        $db = Database::getConnection();
        $totalProducts = (int) $db->query("SELECT COUNT(*) as c FROM products")->fetch()['c'];
        $totalMembers = (int) $db->query("SELECT COUNT(*) as c FROM users WHERE is_active = 1")->fetch()['c'];
        $totalOrders = (int) $db->query("SELECT COUNT(*) as c FROM orders")->fetch()['c'];

        $this->view('home/index', [
            'title' => 'Beranda - ' . INSTITUTION_NAME,
            'featuredProducts' => $featuredProducts,
            'stats' => [
                'products' => $totalProducts,
                'members' => $totalMembers,
                'orders' => $totalOrders
            ]
        ]);
    }
}

