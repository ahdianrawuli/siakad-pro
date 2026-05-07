<?php $title = 'Lupa Password'; require __DIR__ . '/../layouts/public_header.php'; ?>

<div class="min-h-screen bg-gray-50 flex items-center justify-center py-10 px-4">
    <div class="max-w-md w-full space-y-6">

        <div class="text-center">
            <h2 class="text-3xl font-extrabold text-gray-900">Lupa Password</h2>
            <p class="mt-2 text-sm text-gray-600">Masukkan email atau username akun Anda. Kami akan mengirim kode OTP ke WhatsApp terdaftar.</p>
        </div>

        <?php \App\Core\Session::flash(); ?>

        <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-200 space-y-5">
            <form action="/forgot-password/send" method="POST" class="space-y-5">
                <?= \App\Core\Csrf::input() ?>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Email / Username</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fa-solid fa-user"></i>
                        </span>
                        <input type="text" name="email" required autofocus
                            class="block w-full pl-10 px-3 py-3 border border-gray-300 rounded-lg text-sm focus:ring-green-500 focus:border-green-500"
                            placeholder="Masukkan email atau username...">
                    </div>
                </div>

                <button type="submit"
                    class="w-full py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg transition shadow-lg shadow-green-200 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-paper-plane"></i> Kirim Kode OTP
                </button>
            </form>

            <div class="text-center text-sm text-gray-500">
                Ingat password? <a href="/login" class="text-green-600 font-bold hover:underline">Masuk di sini</a>
            </div>
        </div>

    </div>
</div>

</body>
</html>
