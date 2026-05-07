<?php $title = 'Masuk'; require __DIR__ . '/../layouts/public_header.php'; ?>

<div class="min-h-screen bg-gray-50 flex items-center justify-center py-10 px-4">
    <div class="max-w-md w-full space-y-6">

        <div class="text-center">
            <h2 class="text-3xl font-extrabold text-gray-900">Masuk ke SIAKAD</h2>
            <p class="mt-2 text-sm text-gray-600">Gunakan akun yang telah terdaftar</p>
        </div>

        <?php \App\Core\Session::flash(); ?>

        <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-200 space-y-5">
            <form action="/login" method="POST" class="space-y-5">
                <?php if(class_exists('\App\Core\Csrf')) echo \App\Core\Csrf::input(); ?>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Email / Username</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <input type="text" name="email" required
                            class="block w-full pl-10 px-3 py-3 border border-gray-300 rounded-lg text-sm focus:ring-green-500 focus:border-green-500"
                            placeholder="Masukkan email atau username...">
                    </div>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label class="block text-xs font-bold text-gray-500 uppercase">Password</label>
                        <a href="/forgot-password" class="text-xs text-green-600 hover:underline">Lupa password?</a>
                    </div>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" name="password" required
                            class="block w-full pl-10 px-3 py-3 border border-gray-300 rounded-lg text-sm focus:ring-green-500 focus:border-green-500"
                            placeholder="••••••••">
                    </div>
                </div>

                <button type="submit"
                    class="w-full py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg transition shadow-lg shadow-green-200 flex items-center justify-center gap-2">
                    MASUK SEKARANG <i class="fa-solid fa-right-to-bracket"></i>
                </button>
            </form>

            <div class="border-t border-gray-100 pt-4 text-center">
                <p class="text-sm text-gray-500">Belum punya akun?
                    <a href="/register" class="text-green-600 font-bold hover:underline">Daftar di sini</a>
                </p>
            </div>
        </div>

        <div class="bg-white p-4 rounded-xl border border-gray-200 text-xs text-gray-500 space-y-1">
            <p class="font-bold text-gray-600 mb-1">Akun Demo:</p>
            <p>👤 Admin &nbsp;&nbsp;: <code class="bg-gray-100 px-1 rounded">admin</code> / <code class="bg-gray-100 px-1 rounded">password</code></p>
            <p>🎓 Siswa &nbsp;&nbsp;: <code class="bg-gray-100 px-1 rounded">25260001</code> / <code class="bg-gray-100 px-1 rounded">27092013</code> <span class="text-gray-400 text-xs">(tgl lahir: 27 Sep 2013)</span></p>
            <p>📖 Guru &nbsp;&nbsp;&nbsp;: <code class="bg-gray-100 px-1 rounded">ahmad.fauzi</code> / <code class="bg-gray-100 px-1 rounded">123456</code></p>
        </div>

    </div>
</div>

</body>
</html>
