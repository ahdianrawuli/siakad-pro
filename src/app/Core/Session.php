<?php
namespace App\Core;

class Session {
    public static function init() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    public static function set($key, $val) {
        $_SESSION[$key] = $val;
    }

    public static function get($key) {
        return isset($_SESSION[$key]) ? $_SESSION[$key] : null;
    }

    public static function remove($key) {
        if (isset($_SESSION[$key])) {
            unset($_SESSION[$key]);
        }
    }

    public static function destroy() {
        session_destroy();
    }

    // Set pesan notifikasi
    public static function setFlash($type, $message) {
        $_SESSION['flash'] = [
            'type' => $type, // success, error, warning
            'message' => $message
        ];
    }

    // Tampilkan langsung (Format Tailwind - Dipakai di Login)
    public static function flash() {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            $bg = $flash['type'] == 'success' ? 'bg-green-100 border-green-400 text-green-700' : 'bg-red-100 border-red-400 text-red-700';
            
            echo '<div class="'.$bg.' border px-4 py-3 rounded relative mb-4" role="alert">';
            echo '<span class="block sm:inline">'.$flash['message'].'</span>';
            echo '</div>';
            
            unset($_SESSION['flash']);
        }
    }

    // [BARU] Ambil pesan flash untuk custom view (Format Bootstrap)
    public static function getFlash($expectedType = null) {
        if (isset($_SESSION['flash'])) {
            $flash = $_SESSION['flash'];
            
            // Jika tipe diminta spesifik (misal hanya 'success')
            if ($expectedType && $flash['type'] !== $expectedType) {
                return null;
            }

            // Hapus session agar tidak muncul terus
            unset($_SESSION['flash']);
            return $flash['message'];
        }
        return null;
    }
}
