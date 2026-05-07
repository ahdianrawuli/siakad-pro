<?php $title = 'Reset Password'; require __DIR__ . '/../layouts/public_header.php'; ?>

<div class="min-h-screen bg-gray-50 flex items-center justify-center py-10 px-4">
    <div class="max-w-md w-full space-y-6">

        <div class="text-center">
            <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                <i class="fa-solid fa-lock"></i>
            </div>
            <h2 class="text-3xl font-extrabold text-gray-900">Buat Password Baru</h2>
            <p class="mt-2 text-sm text-gray-600">Masukkan password baru untuk akun Anda</p>
        </div>

        <?php \App\Core\Session::flash(); ?>

        <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-200 space-y-5">
            <form action="/forgot-password/reset" method="POST" class="space-y-5">
                <?= \App\Core\Csrf::input() ?>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Password Baru</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" name="password" required minlength="6"
                            class="block w-full pl-10 px-3 py-3 border border-gray-300 rounded-lg text-sm focus:ring-green-500 focus:border-green-500"
                            placeholder="Minimal 6 karakter">
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Ulangi Password</label>
                    <div class="relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-gray-400">
                            <i class="fa-solid fa-lock"></i>
                        </span>
                        <input type="password" name="password_confirm" required minlength="6"
                            class="block w-full pl-10 px-3 py-3 border border-gray-300 rounded-lg text-sm focus:ring-green-500 focus:border-green-500"
                            placeholder="Ulangi password baru">
                    </div>
                </div>

                <button type="submit"
                    class="w-full py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg transition shadow-lg shadow-green-200 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Simpan Password Baru
                </button>
            </form>
        </div>

    </div>
</div>

</body>
</html>
