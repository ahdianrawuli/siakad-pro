<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">
    <div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-center gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <div class="flex items-center gap-2 text-sm text-slate-400 mb-1">
                <a href="/finance/inventory" class="hover:text-blue-600 transition">Inventaris Aset</a>
                <i class="fa-solid fa-chevron-right text-xs"></i>
                <span class="text-slate-600 font-semibold">Riwayat Mutasi Kondisi</span>
            </div>
            <h3 class="text-2xl font-extrabold text-slate-800">Riwayat Mutasi Kondisi</h3>
            <?php if ($item): ?>
                <p class="text-slate-500 text-sm mt-1">Barang: <strong><?= htmlspecialchars($item['name']) ?></strong> <span class="font-mono text-blue-600">(<?= $item['code'] ?>)</span></p>
            <?php else: ?>
                <p class="text-slate-500 text-sm mt-1">Semua perubahan kondisi aset.</p>
            <?php endif; ?>
        </div>
        <a href="/finance/inventory" class="px-4 py-2.5 bg-slate-100 text-slate-700 rounded-xl text-sm font-semibold hover:bg-slate-200 transition flex items-center gap-2 w-fit">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full whitespace-nowrap text-left">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Barang</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Kondisi Lama</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Kondisi Baru</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Catatan</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Diubah Oleh</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php if (empty($rows)): ?>
                        <tr><td colspan="6" class="px-5 py-16 text-center text-slate-400 text-sm">Belum ada riwayat mutasi.</td></tr>
                    <?php endif; ?>
                    <?php
                    $condColors = [
                        'BAIK'        => 'bg-green-50 text-green-700 border-green-200',
                        'RUSAK_RINGAN'=> 'bg-yellow-50 text-yellow-700 border-yellow-200',
                        'RUSAK_BERAT' => 'bg-red-50 text-red-700 border-red-200',
                        'HILANG'      => 'bg-slate-100 text-slate-500 border-slate-200',
                    ];
                    ?>
                    <?php foreach ($rows as $r): ?>
                    <tr class="hover:bg-slate-50/80 transition-colors text-sm">
                        <td class="px-5 py-4">
                            <div class="font-bold text-slate-800"><?= htmlspecialchars($r['item_name']) ?></div>
                            <span class="text-[10px] font-mono text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-100"><?= $r['item_code'] ?></span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg border <?= $condColors[$r['old_condition']] ?? '' ?>">
                                <?= str_replace('_', ' ', $r['old_condition']) ?>
                            </span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg border <?= $condColors[$r['new_condition']] ?? '' ?>">
                                <?= str_replace('_', ' ', $r['new_condition']) ?>
                            </span>
                        </td>
                        <td class="px-5 py-4 text-xs text-slate-500"><?= htmlspecialchars($r['notes'] ?? '-') ?></td>
                        <td class="px-5 py-4 text-xs font-semibold text-slate-700"><?= htmlspecialchars($r['changed_by_name']) ?></td>
                        <td class="px-5 py-4 text-xs text-slate-500"><?= date('d M Y H:i', strtotime($r['changed_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if ($totalPages > 1): ?>
        <div class="p-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
            <span class="text-xs text-slate-500">Halaman <?= $currentPage ?> / <?= $totalPages ?></span>
            <div class="flex gap-1.5">
                <?php $qs = $itemId ? "&item_id=$itemId" : ""; ?>
                <?php if ($currentPage > 1): ?>
                    <a href="?page=<?= $currentPage - 1 . $qs ?>" class="w-8 h-8 flex items-center justify-center bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors shadow-sm"><i class="fa-solid fa-chevron-left"></i></a>
                <?php endif; ?>
                <?php if ($currentPage < $totalPages): ?>
                    <a href="?page=<?= $currentPage + 1 . $qs ?>" class="w-8 h-8 flex items-center justify-center bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors shadow-sm"><i class="fa-solid fa-chevron-right"></i></a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
