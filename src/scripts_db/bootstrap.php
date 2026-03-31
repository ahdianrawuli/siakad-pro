<?php
// Pastikan hanya bisa dijalankan lewat CLI (Terminal)
if (php_sapi_name() !== 'cli') {
    die("Akses ditolak. Script ini hanya untuk CLI.");
}

// Autoloader (Sama seperti di public/index.php tapi disesuaikan path-nya)
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    // Path naik satu level ke folder 'app'
    $base_dir = __DIR__ . '/../app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) return;

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require $file;
    }
});

// Load Env/Config jika perlu (opsional, karena Database.php sudah handle env via docker)
echo "[CLI] Bootstrap loaded.\n";

