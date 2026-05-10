<?php
namespace App\Controllers;

use App\Core\Session;

class ScopeController {
    
    public function change() {
        // Ambil scope dari form
        $scope = $_POST['scope'] ?? 'GLOBAL';
        
        // Daftar scope yang valid (bisa disesuaikan dengan database Anda)
        $allowedScopes = ['GLOBAL', 'MTS', 'MA', 'PDF']; 
        
        if (in_array($scope, $allowedScopes)) {
            // Simpan scope lama untuk animasi transisi
            $prev = Session::get('active_scope', 'GLOBAL');
            Session::set('prev_scope', $prev);
            Session::set('active_scope', $scope);

            $label = ($scope == 'GLOBAL') ? 'Semua Jenjang' : "Jenjang $scope";
            Session::setFlash('success', "Mode tampilan diubah ke: $label");
        }
        
        // Redirect kembali ke halaman asal (Refresh)
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        exit;
    }
}
