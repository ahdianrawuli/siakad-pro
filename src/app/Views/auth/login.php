<?php $title = 'Masuk'; require __DIR__ . '/../layouts/public_header.php'; ?>

<div class="min-h-screen flex">
    <!-- Kiri: Gambar + Quote -->
    <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden">
        <img src="https://images.unsplash.com/photo-1564981797816-1043664bf78d?w=1200&q=80&auto=format&fit=crop"
             alt="Pesantren" class="absolute inset-0 w-full h-full object-cover">
        <div class="absolute inset-0 bg-gradient-to-br from-green-900/85 to-green-700/70"></div>
        <div class="relative z-10 flex flex-col justify-between p-12 w-full">
            <a href="/" class="flex items-center gap-3">
                <div class="w-10 h-10 bg-white/20 rounded-full flex items-center justify-center">
                    <i class="fa-solid fa-mosque text-white text-lg"></i>
                </div>
                <span class="text-white font-extrabold text-sm tracking-wide">THAWALIB PARABEK</span>
            </a>
            <div>
                <blockquote class="text-white text-2xl font-bold leading-snug mb-4">
                    "Terpuji dalam tradisi,<br>Terdepan dalam prestasi"
                </blockquote>
                <p class="text-green-200 text-sm">Pondok Pesantren Sumatera Thawalib Parabek · Bukittinggi Agam</p>
            </div>
        </div>
    </div>

    <!-- Kanan: Form -->
    <div class="w-full lg:w-1/2 flex items-center justify-center bg-gray-50 px-6 py-12">
        <div class="w-full max-w-md space-y-6">

            <div>
                <h2 class="text-3xl font-extrabold text-gray-900">Selamat datang 👋</h2>
                <p class="mt-1 text-gray-500 text-sm">Masuk ke akun SIAKAD Anda</p>
            </div>

            <?php \App\Core\Session::flash(); ?>

            <div class="bg-white p-8 rounded-2xl shadow-sm border border-gray-200 space-y-5">
                <form action="/login" method="POST" class="space-y-5">
                    <?php if(class_exists('\App\Core\Csrf')) echo \App\Core\Csrf::input(); ?>

                    <div>
                        <label class="block text-xs font-bold text-gray-500 uppercase mb-1.5">Email / Username</label>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-400">
                                <i class="fa-solid fa-user text-sm"></i>
                            </span>
                            <input type="text" name="email" required autofocus
                                class="block w-full pl-10 pr-4 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition"
                                placeholder="Email atau username...">
                        </div>
                    </div>

                    <div>
                        <div class="flex justify-between items-center mb-1.5">
                            <label class="block text-xs font-bold text-gray-500 uppercase">Password</label>
                            <a href="/forgot-password" class="text-xs text-green-600 hover:underline">Lupa password?</a>
                        </div>
                        <div class="relative">
                            <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center text-gray-400">
                                <i class="fa-solid fa-lock text-sm"></i>
                            </span>
                            <input type="password" name="password" id="password" required
                                class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-xl text-sm focus:ring-2 focus:ring-green-500 focus:border-green-500 outline-none transition"
                                placeholder="••••••••">
                            <button type="button" onclick="togglePwd()" class="absolute inset-y-0 right-0 pr-3.5 flex items-center text-gray-400 hover:text-gray-600">
                                <i class="fa-solid fa-eye text-sm" id="eye-icon"></i>
                            </button>
                        </div>
                    </div>

                    <button type="submit"
                        class="w-full py-3.5 bg-green-600 hover:bg-green-700 text-white font-bold rounded-xl transition shadow-lg shadow-green-200 flex items-center justify-center gap-2 text-sm">
                        MASUK SEKARANG <i class="fa-solid fa-right-to-bracket"></i>
                    </button>
                </form>

                <div class="border-t border-gray-100 pt-4 text-center text-sm text-gray-500">
                    Belum punya akun?
                    <a href="/register" class="text-green-600 font-bold hover:underline">Daftar di sini</a>
                </div>
            </div>

            <!-- Info akun demo -->
            <details class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
                <summary class="px-5 py-3.5 text-xs font-bold text-gray-600 cursor-pointer select-none flex items-center gap-2">
                    <i class="fa-solid fa-circle-info text-green-500"></i> Akun Demo & Panduan Login
                </summary>
                <div class="px-5 pb-4 pt-1 text-xs text-gray-500 space-y-1.5 border-t border-gray-100">
                    <p>👤 Admin &nbsp;&nbsp;: <code class="bg-gray-100 px-1 rounded">admin</code> / <code class="bg-gray-100 px-1 rounded">password</code></p>
                    <p>🎓 Siswa &nbsp;&nbsp;: <code class="bg-gray-100 px-1 rounded">25260001</code> / <code class="bg-gray-100 px-1 rounded">27092013</code></p>
                    <p>📖 Guru &nbsp;&nbsp;&nbsp;: <code class="bg-gray-100 px-1 rounded">ahmad.fauzi</code> / <code class="bg-gray-100 px-1 rounded">123456</code></p>
                    <p>👨‍👩‍👦 Ortu &nbsp;&nbsp;: <code class="bg-gray-100 px-1 rounded">083891834125</code> / <code class="bg-gray-100 px-1 rounded">25260001</code></p>
                    <p class="text-gray-400 pt-1">Login orang tua: Username = No HP Ayah, Password = NIS siswa</p>
                </div>
            </details>

        </div>
    </div>
</div>

<script>
function togglePwd() {
    const inp = document.getElementById('password');
    const ico = document.getElementById('eye-icon');
    if (inp.type === 'password') { inp.type = 'text'; ico.classList.replace('fa-eye','fa-eye-slash'); }
    else { inp.type = 'password'; ico.classList.replace('fa-eye-slash','fa-eye'); }
}
</script>

</body>
</html>
