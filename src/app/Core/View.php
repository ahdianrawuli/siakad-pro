<?php
namespace App\Core;

class View {
    public static function render($view, $data = []) {
        // 1. Ekstrak data array menjadi variabel (contoh: ['title' => 'Home'] menjadi $title)
        extract($data);

        // 2. Tentukan lokasi file view
        // __DIR__ mengarah ke app/Core, jadi kita naik satu level ke app/Views
        $viewFile = __DIR__ . '/../Views/' . $view . '.php';

        // 3. Cek apakah file ada
        if (file_exists($viewFile)) {
            require $viewFile;
        } else {
            // Jika tidak ada, matikan proses dan beri pesan error jelas
            die("Critical Error: View file not found at: $viewFile");
        }
    }
}
