<?php use App\Models\AppConfig; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SIAKAD</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-green-50 h-screen flex items-center justify-center relative">

    <a href="/" class="absolute top-6 left-6 text-green-700 hover:text-green-900 font-bold flex items-center gap-2 transition-colors">
        <i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda
    </a>

    <div class="w-full max-w-md bg-white rounded-xl shadow-2xl overflow-hidden m-4">
        <div class="bg-green-600 p-8 text-center">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full bg-white text-green-600 mb-4 shadow-lg">
                <i class="fa-solid fa-graduation-cap text-3xl"></i>
            </div>
            <h2 class="text-2xl font-bold text-white">SIAKAD LOGIN</h2>
            <p class="text-green-100 text-sm mt-1">Sistem Informasi Akademik</p>
        </div>

        <div class="p-8">
            <?php \App\Core\Session::flash(); ?>

            <form action="/login" method="POST">
                <?php if(class_exists('\App\Core\Csrf')) echo \App\Core\Csrf::input(); ?>

                <div class="mb-5">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Email / Username</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <input type="text" name="email" class="w-full pl-10 pr-3 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition" placeholder="Masukkan email..." required>
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 text-sm font-bold mb-2">Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" name="password" class="w-full pl-10 pr-3 py-3 rounded-lg border border-gray-300 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent transition" placeholder="********" required>
                    </div>
                </div>

                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 rounded-lg shadow-lg transform hover:-translate-y-1 transition-all duration-200">
                    MASUK SEKARANG <i class="fa-solid fa-right-to-bracket ml-2"></i>
                </button>
            </form>

            <div class="mt-6 text-center text-sm text-gray-500">
                <p>Lupa password? Hubungi Administrator.</p>
                <p class="mt-2">&copy; <?= date('Y') ?> Siakad Parabek</p>
            </div>
        </div>
    </div>

</body>
</html>
