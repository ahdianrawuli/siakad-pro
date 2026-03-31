<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <div class="flex justify-between items-center mb-6">
        <h3 class="text-3xl font-medium text-gray-700">Pusat Bantuan</h3>
        <button onclick="document.getElementById('modalNewTicket').classList.remove('hidden')" class="bg-blue-600 text-white px-4 py-2 rounded font-bold shadow hover:bg-blue-700">
            <i class="fa-solid fa-plus mr-2"></i> Buat Tiket
        </button>
    </div>
    <?php \App\Core\Session::flash(); ?>

    <div id="modalNewTicket" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <h3 class="text-lg font-bold mb-4">Tiket Baru</h3>
            <form action="/support/create" method="POST">
                <?= \App\Core\Csrf::input() ?>
                <input type="text" name="subject" placeholder="Judul Masalah" class="w-full p-2 border rounded mb-3" required>
                <select name="category" class="w-full p-2 border rounded mb-3">
                    <option value="ASRAMA">Fasilitas Asrama</option>
                    <option value="AKADEMIK">Masalah Akademik</option>
                    <option value="KEUANGAN">Pembayaran/Tagihan</option>
                    <option value="LAINNYA">Lainnya</option>
                </select>
                <textarea name="message" rows="4" placeholder="Jelaskan masalah Anda..." class="w-full p-2 border rounded mb-3" required></textarea>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('modalNewTicket').classList.add('hidden')" class="px-4 py-2 bg-gray-200 rounded">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Kirim</button>
                </div>
            </form>
        </div>
    </div>

    <div class="space-y-4">
        <?php foreach($tickets as $t): ?>
        <a href="/support/detail?id=<?= $t['id'] ?>" class="block bg-white p-4 rounded shadow hover:bg-blue-50 transition border-l-4 <?= $t['status']=='OPEN'?'border-green-500':($t['status']=='ANSWERED'?'border-yellow-500':'border-gray-500') ?>">
            <div class="flex justify-between">
                <div>
                    <h4 class="font-bold text-lg text-gray-800"><?= $t['subject'] ?></h4>
                    <p class="text-sm text-gray-500">Pelapor: <?= $t['reporter'] ?> | Kategori: <?= $t['category'] ?></p>
                </div>
                <div class="text-right">
                    <span class="text-xs font-bold px-2 py-1 rounded <?= $t['status']=='OPEN'?'bg-green-100 text-green-800':($t['status']=='ANSWERED'?'bg-yellow-100 text-yellow-800':'bg-gray-200 text-gray-600') ?>">
                        <?= $t['status'] ?>
                    </span>
                    <p class="text-xs text-gray-400 mt-1"><?= substr($t['created_at'], 0, 10) ?></p>
                </div>
            </div>
        </a>
        <?php endforeach; ?>
    </div>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
