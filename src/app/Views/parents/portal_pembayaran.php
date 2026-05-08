<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/parent_sidebar.php'; ?>
<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">
    <div class="mb-5">
        <h1 class="text-xl font-bold text-gray-800"><i class="fa-solid fa-file-invoice-dollar text-orange-500 mr-2"></i>Pembayaran</h1>
    </div>

    <?php $baseUrl = '/portal/orangtua/pembayaran'; require __DIR__ . '/_child_selector.php'; ?>

    <?php if (!$student): ?>
    <div class="bg-white rounded-xl p-10 text-center text-gray-400">Akun belum terhubung ke data siswa.</div>
    <?php else: ?>

    <!-- Tagihan -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="px-5 py-3 border-b border-gray-100 font-semibold text-gray-700">Tagihan</div>
        <?php if (empty($bills)): ?>
        <p class="text-center text-gray-400 py-8">Tidak ada tagihan.</p>
        <?php else: ?>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                <tr>
                    <th class="px-4 py-2 text-left">Jenis</th>
                    <th class="px-4 py-2 text-right">Jumlah</th>
                    <th class="px-4 py-2 text-center">Status</th>
                    <th class="px-4 py-2 text-left">Tanggal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php foreach ($bills as $b): ?>
                <tr>
                    <td class="px-4 py-2 text-gray-800"><?= htmlspecialchars($b['fee_name'] ?? $b['title'] ?? '-') ?></td>
                    <td class="px-4 py-2 text-right text-gray-700">Rp <?= number_format($b['amount'], 0, ',', '.') ?></td>
                    <td class="px-4 py-2 text-center">
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold <?= ($b['status'] ?? '') === 'PAID' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                            <?= ($b['status'] ?? '') === 'PAID' ? 'Lunas' : 'Belum Bayar' ?>
                        </span>
                    </td>
                    <td class="px-4 py-2 text-gray-500 text-xs"><?= date('d M Y', strtotime($b['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>

    <!-- Riwayat Transaksi -->
    <?php if (!empty($transactions)): ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 font-semibold text-gray-700">Riwayat Pembayaran</div>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                <tr>
                    <th class="px-4 py-2 text-left">Keterangan</th>
                    <th class="px-4 py-2 text-right">Jumlah</th>
                    <th class="px-4 py-2 text-left">Tanggal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php foreach ($transactions as $t): ?>
                <tr>
                    <td class="px-4 py-2 text-gray-800"><?= htmlspecialchars($t['fee_name'] ?? $t['description'] ?? '-') ?></td>
                    <td class="px-4 py-2 text-right text-green-700 font-medium">Rp <?= number_format($t['amount_paid'], 0, ',', '.') ?></td>
                    <td class="px-4 py-2 text-gray-500 text-xs"><?= date('d M Y', strtotime($t['payment_date'] ?? $t['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>

    <?php endif; ?>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
