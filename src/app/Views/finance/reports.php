<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-6">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">Laporan Keuangan</h1>

    <?php \App\Core\Session::flash(); ?>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow border-l-4 border-green-500">
            <p class="text-gray-500 text-sm">Total Pemasukan (Paid)</p>
            <h2 class="text-2xl font-bold text-green-700">
                Rp <?= number_format($total_income ?? 0, 0, ',', '.') ?>
            </h2>
        </div>

        <div class="bg-white p-6 rounded-lg shadow border-l-4 border-red-500">
            <p class="text-gray-500 text-sm">Piutang / Belum Lunas (Unpaid)</p>
            <h2 class="text-2xl font-bold text-red-700">
                Rp <?= number_format($total_unpaid ?? 0, 0, ',', '.') ?>
            </h2>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b bg-gray-50 flex justify-between items-center">
            <h3 class="font-bold text-gray-700">10 Transaksi Terakhir</h3>
            <span class="text-xs text-gray-400">Diurutkan dari yang terbaru</span>
        </div>
        
        <table class="w-full text-sm text-left">
            <thead class="bg-gray-100 text-gray-600 uppercase">
                <tr>
                    <th class="px-6 py-3">Tanggal</th>
                    <th class="px-6 py-3">Siswa</th>
                    <th class="px-6 py-3">Keterangan</th>
                    <th class="px-6 py-3 text-right">Jumlah</th>
                    <th class="px-6 py-3 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                <?php if (!empty($recent_transactions)): ?>
                    <?php foreach ($recent_transactions as $tx): ?>
                    <tr>
                        <td class="px-6 py-4 text-gray-500">
                            <?= date('d/m/Y', strtotime($tx['created_at'])) ?>
                        </td>
                        <td class="px-6 py-4 font-bold">
                            <?= $tx['full_name'] ?> 
                            <br>
                            <span class="text-xs font-normal text-gray-400"><?= $tx['nis'] ?></span>
                        </td>
                        <td class="px-6 py-4">
                            <?= $tx['title'] ?? $tx['name'] ?? 'Tagihan' ?>
                        </td>
                        <td class="px-6 py-4 text-right">
                            Rp <?= number_format($tx['amount'], 0, ',', '.') ?>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">LUNAS</span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                            Belum ada transaksi lunas.
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
