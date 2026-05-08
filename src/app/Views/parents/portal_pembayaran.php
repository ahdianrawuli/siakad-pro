<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/parent_sidebar.php'; ?>
<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">
<?php
$pageTitle    = 'Pembayaran';
$pageSubtitle = $student ? htmlspecialchars($student['full_name']) : 'Pilih santri terlebih dahulu';
$pageBadgeIcon = 'fa-file-invoice-dollar';
$infoItems    = [
    'Halaman ini menampilkan tagihan dan riwayat pembayaran santri.',
    'Status "Lunas" berarti pembayaran telah dikonfirmasi oleh bendahara.',
    'Hubungi bendahara pesantren jika ada tagihan yang tidak sesuai.',
];
require __DIR__ . '/../layouts/portal_header_card.php';
?>

    <?php $baseUrl = '/portal/orangtua/pembayaran'; require __DIR__ . '/_child_selector.php'; ?>

    <?php if (!$student): ?>
    <div class="bg-white rounded-2xl p-10 text-center text-slate-400 border border-slate-200">Akun belum terhubung ke data siswa.</div>
    <?php else: ?>

    <!-- Tagihan -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-6">
        <div class="px-5 py-3 border-b border-slate-200 font-semibold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-file-invoice text-orange-500"></i> Tagihan
        </div>
        <?php if (empty($bills)): ?>
        <p class="text-center text-slate-400 py-8">Tidak ada tagihan.</p>
        <?php else: ?>
        <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Jenis</th>
                    <th class="px-5 py-3 text-right text-xs font-bold text-slate-500 uppercase">Jumlah</th>
                    <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Tanggal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($bills as $b): ?>
                <tr class="hover:bg-slate-50">
                    <td class="px-5 py-3 text-slate-800"><?= htmlspecialchars($b['fee_name'] ?? $b['title'] ?? '-') ?></td>
                    <td class="px-5 py-3 text-right text-slate-700 font-medium">Rp <?= number_format($b['amount'], 0, ',', '.') ?></td>
                    <td class="px-5 py-3 text-center">
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold <?= ($b['status'] ?? '') === 'PAID' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                            <?= ($b['status'] ?? '') === 'PAID' ? 'Lunas' : 'Belum Bayar' ?>
                        </span>
                    </td>
                    <td class="px-5 py-3 text-slate-500 text-xs"><?= date('d M Y', strtotime($b['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>

    <!-- Riwayat Transaksi -->
    <?php if (!empty($transactions)): ?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-200 font-semibold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-clock-rotate-left text-green-600"></i> Riwayat Pembayaran
        </div>
        <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Keterangan</th>
                    <th class="px-5 py-3 text-right text-xs font-bold text-slate-500 uppercase">Jumlah</th>
                    <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Tanggal</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($transactions as $t): ?>
                <tr class="hover:bg-slate-50">
                    <td class="px-5 py-3 text-slate-800"><?= htmlspecialchars($t['fee_name'] ?? $t['description'] ?? '-') ?></td>
                    <td class="px-5 py-3 text-right text-green-700 font-medium">Rp <?= number_format($t['amount_paid'], 0, ',', '.') ?></td>
                    <td class="px-5 py-3 text-slate-500 text-xs"><?= date('d M Y', strtotime($t['payment_date'] ?? $t['created_at'])) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
    </div>
    <?php endif; ?>

    <?php endif; ?>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
