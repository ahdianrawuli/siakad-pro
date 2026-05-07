<?php $title = 'Verifikasi OTP'; require __DIR__ . '/../layouts/public_header.php'; ?>

<div class="min-h-screen bg-gray-50 flex items-center justify-center py-10 px-4">
    <div class="max-w-md w-full space-y-6">

        <div class="text-center">
            <div class="w-16 h-16 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4 text-2xl">
                <i class="fa-solid fa-mobile-screen-button"></i>
            </div>
            <h2 class="text-3xl font-extrabold text-gray-900">Masukkan Kode OTP</h2>
            <p class="mt-2 text-sm text-gray-600">
                Kode 6 digit telah dikirim ke WhatsApp
                <span class="font-bold text-gray-800"><?= htmlspecialchars($phoneMasked) ?></span>
            </p>
            <p class="text-xs text-gray-400 mt-1">Berlaku selama 5 menit</p>
        </div>

        <?php \App\Core\Session::flash(); ?>

        <div class="bg-white p-8 rounded-xl shadow-sm border border-gray-200 space-y-5">
            <form action="/forgot-password/verify" method="POST" class="space-y-5">
                <?= \App\Core\Csrf::input() ?>

                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1 text-center">Kode OTP</label>
                    <input type="text" name="otp" required autofocus maxlength="6" inputmode="numeric" pattern="[0-9]{6}"
                        class="block w-full text-center text-3xl font-mono font-bold tracking-[0.5em] py-4 border-2 border-gray-300 rounded-xl focus:ring-green-500 focus:border-green-500 outline-none"
                        placeholder="000000">
                </div>

                <button type="submit"
                    class="w-full py-3 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg transition shadow-lg shadow-green-200 flex items-center justify-center gap-2">
                    <i class="fa-solid fa-check"></i> Verifikasi OTP
                </button>
            </form>

            <div class="text-center text-sm text-gray-500">
                Tidak menerima kode?
                <a href="/forgot-password" class="text-green-600 font-bold hover:underline">Kirim ulang</a>
            </div>
        </div>

    </div>
</div>

</body>
</html>
