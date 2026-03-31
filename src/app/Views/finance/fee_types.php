<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-6">
    <div class="flex justify-between items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-800">Master Jenis Tagihan</h1>
        <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="bg-blue-600 text-white px-4 py-2 rounded shadow hover:bg-blue-700">
            <i class="fa-solid fa-plus mr-2"></i> Tambah Baru
        </button>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-100 text-gray-600 uppercase">
                <tr>
                    <th class="px-6 py-3">Nama Tagihan</th>
                    <th class="px-6 py-3">Nominal Default</th>
                    <th class="px-6 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if (!empty($types)): ?>
                    <?php foreach ($types as $t): ?>
                    <tr>
                        <td class="px-6 py-4 font-medium"><?= $t['name'] ?></td>
                        <td class="px-6 py-4">Rp <?= number_format($t['amount'], 0, ',', '.') ?></td>
                        <td class="px-6 py-4 text-center">
                            <a href="/finance/fee-types/delete?id=<?= $t['id'] ?>" onclick="return confirm('Hapus data ini?')" class="text-red-500 hover:text-red-700">
                                <i class="fa-solid fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="3" class="px-6 py-8 text-center text-gray-400">Belum ada data.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <div id="addModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg w-full max-w-md p-6">
            <h3 class="font-bold text-lg mb-4">Tambah Jenis Tagihan</h3>
            <form action="/finance/fee-types/store" method="POST">
                <div class="mb-4">
                    <label class="block text-sm font-bold mb-2">Nama Tagihan</label>
                    <input type="text" name="name" class="w-full border rounded px-3 py-2" placeholder="Contoh: SPP Bulanan" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-bold mb-2">Nominal (Rp)</label>
                    <input type="number" name="amount" class="w-full border rounded px-3 py-2" placeholder="0">
                </div>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="px-4 py-2 text-gray-600">Batal</button>
                    <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Simpan</button>
                </div>
            </form>
        </div>
    </div>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
