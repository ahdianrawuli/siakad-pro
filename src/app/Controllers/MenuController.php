<?php
namespace App\Controllers;
use App\Models\Menu;

class MenuController {
    public function index() {
        $menus = Menu::getAll();
        
        // Return JSON dulu untuk testing fase ini
        header('Content-Type: application/json');
        echo json_encode([
            'status' => 'success', 
            'message' => 'Real Data from Database',
            'data' => $menus
        ]);
    }

    public function store() {
        // Logika simpan menu baru
        $data = [
            'parent_id' => $_POST['parent_id'] ?? null,
            'title' => $_POST['title'],
            'url' => $_POST['url'],
            'icon' => $_POST['icon'] ?? 'circle',
            'order_num' => $_POST['order_num'] ?? 0
        ];
        Menu::create($data);
        echo json_encode(['status' => 'success', 'message' => 'Menu created']);
    }
}
