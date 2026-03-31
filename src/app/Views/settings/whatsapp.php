<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <h3 class="text-3xl font-medium text-gray-700 mb-6">WhatsApp Gateway</h3>
    <?php \App\Core\Session::flash(); ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded shadow">
            <h4 class="font-bold mb-4 border-b pb-2">Konfigurasi API</h4>
            <div class="bg-yellow-50 p-3 rounded text-xs text-yellow-800 mb-4">
                Sistem ini mendukung vendor seperti <b>Fonnte, Wablas, atau WooWA</b>. 
                Sesuaikan URL Endpoint dengan dokumentasi vendor Anda.
            </div>
            <form action="/settings/whatsapp/update" method="POST">
                <?= \App\Core\Csrf::input() ?>
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-600">API URL Endpoint</label>
                    <input type="text" name="wa_api_url" value="<?= $config['wa_api_url'] ?? 'https://api.fonnte.com/send' ?>" class="w-full p-2 border rounded" placeholder="https://api.vendor.com/send">
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-600">API Token / Key</label>
                    <input type="text" name="wa_api_token" value="<?= $config['wa_api_token'] ?? '' ?>" class="w-full p-2 border rounded" placeholder="Paste Token Disini">
                </div>
                <button type="submit" class="bg-green-600 text-white px-6 py-2 rounded font-bold hover:bg-green-700 w-full">Simpan Konfigurasi</button>
            </form>
        </div>

        <div class="bg-white p-6 rounded shadow h-fit">
            <h4 class="font-bold mb-4 border-b pb-2">Tes Koneksi</h4>
            <form action="/settings/whatsapp/test" method="POST">
                <?= \App\Core\Csrf::input() ?>
                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-600">Nomor HP Tujuan</label>
                    <input type="text" name="test_number" class="w-full p-2 border rounded" placeholder="0812xxxx (Gunakan kode negara jika perlu)">
                </div>
                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded font-bold hover:bg-blue-700 w-full">
                    <i class="fa-brands fa-whatsapp mr-2"></i> Kirim Pesan Tes
                </button>
            </form>
        </div>
    </div>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
