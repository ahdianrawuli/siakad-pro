<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <!-- Header -->
    <div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Laporan Keuangan</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Rekap transaksi pembayaran dan piutang santri.</p>
            <div class="mt-3 flex items-center gap-2">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold border border-blue-100">
                    <i class="fa-solid fa-receipt"></i> <?= $totalData ?> Transaksi
                </div>
                <button onclick="document.getElementById('infoModal').classList.remove('hidden')"
                    class="w-7 h-7 rounded-full bg-slate-100 text-slate-500 hover:bg-blue-100 hover:text-blue-600 flex items-center justify-center transition-colors border border-slate-200" title="Panduan">
                    <i class="fa-solid fa-circle-info text-sm"></i>
                </button>
            </div>
        </div>
        <div class="flex gap-2 flex-wrap">
            <?php $exportQs = http_build_query(['search'=>$search,'status'=>$statusFilter,'date_from'=>$dateFrom,'date_to'=>$dateTo,'class_id'=>$classId,'fee_type_id'=>$feeTypeId]); ?>
            <button onclick="window.print()"
                class="px-4 py-2.5 bg-white text-slate-700 border border-slate-300 rounded-xl text-sm font-semibold hover:bg-slate-50 transition flex items-center gap-2">
                <i class="fa-solid fa-print text-slate-400"></i> Print
            </button>
            <a href="/finance/reports/export?format=excel&<?= $exportQs ?>"
                class="px-4 py-2.5 bg-green-600 text-white rounded-xl text-sm font-semibold shadow-md shadow-green-500/20 hover:bg-green-700 transition flex items-center gap-2">
                <i class="fa-solid fa-file-excel"></i> Excel
            </a>
            <a href="/finance/reports/export?format=pdf&<?= $exportQs ?>" target="_blank"
                class="px-4 py-2.5 bg-red-600 text-white rounded-xl text-sm font-semibold shadow-md shadow-red-500/20 hover:bg-red-700 transition flex items-center gap-2">
                <i class="fa-solid fa-file-pdf"></i> PDF
            </a>
        </div>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <!-- Summary Cards (mengikuti filter) -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6">
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 border-l-4 border-green-500">
            <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider">Total Pemasukan (Lunas)</p>
            <h4 class="text-2xl font-extrabold text-green-700 mt-1 font-mono">Rp <?= number_format($total_income ?? 0, 0, ',', '.') ?></h4>
            <?php if (!empty($statusFilter) || !empty($dateFrom) || !empty($classId) || !empty($feeTypeId)): ?>
                <p class="text-[10px] text-slate-400 mt-1"><i class="fa-solid fa-filter mr-1"></i>Sesuai filter aktif</p>
            <?php endif; ?>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 border-l-4 border-red-500">
            <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider">Piutang / Belum Lunas</p>
            <h4 class="text-2xl font-extrabold text-red-700 mt-1 font-mono">Rp <?= number_format($total_unpaid ?? 0, 0, ',', '.') ?></h4>
            <?php if (!empty($statusFilter) || !empty($dateFrom) || !empty($classId) || !empty($feeTypeId)): ?>
                <p class="text-[10px] text-slate-400 mt-1"><i class="fa-solid fa-filter mr-1"></i>Sesuai filter aktif</p>
            <?php endif; ?>
        </div>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 border-l-4 border-blue-500">
            <p class="text-slate-500 text-xs font-semibold uppercase tracking-wider">Total Tagihan</p>
            <h4 class="text-2xl font-extrabold text-blue-700 mt-1 font-mono">Rp <?= number_format(($total_income ?? 0) + ($total_unpaid ?? 0), 0, ',', '.') ?></h4>
            <p class="text-[10px] text-slate-400 mt-1"><?= $totalData ?> transaksi</p>
        </div>
    </div>

    <div class="flex flex-col gap-6">

        <!-- Filter -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
            <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                <input type="hidden" name="limit" value="<?= $limit ?>">
                <!-- Baris 1 -->
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fa-solid fa-magnifying-glass text-xs"></i></span>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nama / NIS..."
                        class="w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
                </div>
                <select name="status" class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                    <option value="">Semua Status</option>
                    <option value="PAID"   <?= $statusFilter == 'PAID'   ? 'selected' : '' ?>>Lunas</option>
                    <option value="UNPAID" <?= $statusFilter == 'UNPAID' ? 'selected' : '' ?>>Belum Bayar</option>
                </select>
                <select name="class_id" class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none select2-class">
                    <option value="">Semua Kelas</option>
                    <?php foreach ($classes as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $classId == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="fee_type_id" class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none select2-feetype">
                    <option value="">Semua Jenis Tagihan</option>
                    <?php foreach ($feeTypes as $ft): ?>
                        <option value="<?= $ft['id'] ?>" <?= $feeTypeId == $ft['id'] ? 'selected' : '' ?>><?= htmlspecialchars($ft['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <!-- Baris 2 -->
                <input type="date" name="date_from" value="<?= $dateFrom ?>" placeholder="Dari tanggal"
                    class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                <input type="date" name="date_to" value="<?= $dateTo ?>" placeholder="Sampai tanggal"
                    class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                <div class="flex gap-2 md:col-span-2 justify-end">
                    <button type="submit" class="bg-slate-800 text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-slate-900 transition">Terapkan</button>
                    <?php if (!empty($search) || !empty($statusFilter) || !empty($dateFrom) || !empty($dateTo) || !empty($classId) || !empty($feeTypeId)): ?>
                        <a href="/finance/reports" class="flex items-center justify-center w-10 h-10 bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition" title="Reset">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Rekap per Kelas -->
        <?php if (!empty($byClass)): ?>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
                <h4 class="font-bold text-slate-700 text-sm flex items-center gap-2"><i class="fa-solid fa-layer-group text-slate-400"></i> Rekap per Kelas</h4>
                <span class="text-xs text-slate-400"><?= count($byClass) ?> kelas</span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Kelas</th>
                            <th class="px-5 py-3 text-right text-xs font-bold text-slate-500 uppercase">Lunas</th>
                            <th class="px-5 py-3 text-right text-xs font-bold text-slate-500 uppercase">Belum Bayar</th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase">Transaksi</th>
                            <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($byClass as $bc): ?>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-5 py-3 font-semibold text-slate-800"><?= htmlspecialchars($bc['class_name'] ?? 'Tanpa Kelas') ?></td>
                            <td class="px-5 py-3 text-right text-green-700 font-mono font-bold text-xs">Rp <?= number_format($bc['paid'], 0, ',', '.') ?></td>
                            <td class="px-5 py-3 text-right text-red-600 font-mono font-bold text-xs">Rp <?= number_format($bc['unpaid'], 0, ',', '.') ?></td>
                            <td class="px-5 py-3 text-center text-xs text-slate-500"><?= $bc['total'] ?></td>
                            <td class="px-5 py-3 text-center">
                                <?php
                                $classQs = http_build_query(['class_id' => array_column($classes, 'id', 'name')[$bc['class_name']] ?? '', 'status' => $statusFilter, 'date_from' => $dateFrom, 'date_to' => $dateTo, 'fee_type_id' => $feeTypeId]);
                                // Cari class_id dari nama
                                $cid = '';
                                foreach ($classes as $cl) { if ($cl['name'] === $bc['class_name']) { $cid = $cl['id']; break; } }
                                ?>
                                <a href="?class_id=<?= $cid ?>&status=<?= urlencode($statusFilter) ?>&date_from=<?= $dateFrom ?>&date_to=<?= $dateTo ?>&fee_type_id=<?= $feeTypeId ?>"
                                    class="text-xs text-blue-600 hover:underline font-semibold">Filter →</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <?php endif; ?>

        <!-- Tabel Transaksi -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col" id="printArea">
            <div class="overflow-x-auto">
                <table class="min-w-full whitespace-nowrap text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-4 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-4 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Siswa</th>
                            <th class="px-4 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Kelas</th>
                            <th class="px-4 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Keterangan</th>
                            <th class="px-4 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Metode</th>
                            <th class="px-4 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Jumlah</th>
                            <th class="px-4 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Status</th>
                            <th class="px-4 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <?php if (empty($transactions)): ?>
                            <tr><td colspan="8" class="px-5 py-16 text-center text-slate-400 text-sm">Belum ada data transaksi.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($transactions as $tx): ?>
                        <tr class="hover:bg-slate-50/80 transition-colors text-sm">
                            <td class="px-4 py-3 font-mono text-xs text-slate-500"><?= date('d/m/Y', strtotime($tx['created_at'])) ?></td>
                            <td class="px-4 py-3">
                                <div class="font-bold text-slate-800"><?= htmlspecialchars($tx['full_name']) ?></div>
                                <div class="text-[10px] text-slate-400 font-mono"><?= $tx['nis'] ?></div>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-600"><?= htmlspecialchars($tx['class_name'] ?? '-') ?></td>
                            <td class="px-4 py-3 text-xs text-slate-600"><?= htmlspecialchars($tx['fee_type_name'] ?? $tx['title'] ?? 'Tagihan') ?></td>
                            <td class="px-4 py-3 text-center">
                                <?php if (!empty($tx['payment_method'])): ?>
                                    <span class="px-2 py-0.5 text-[10px] font-bold rounded border <?= $tx['payment_method'] === 'CASH' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-purple-50 text-purple-700 border-purple-200' ?>">
                                        <?= $tx['payment_method'] ?>
                                    </span>
                                <?php else: ?>
                                    <span class="text-slate-300 text-xs">—</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-slate-700 text-xs">Rp <?= number_format($tx['amount'], 0, ',', '.') ?></td>
                            <td class="px-4 py-3 text-center">
                                <?php if ($tx['status'] == 'PAID'): ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-green-50 text-green-700 rounded-full text-[10px] font-bold border border-green-200">
                                        <i class="fa-solid fa-circle-check"></i> LUNAS
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-red-50 text-red-700 rounded-full text-[10px] font-bold border border-red-200">
                                        <i class="fa-solid fa-clock"></i> BELUM BAYAR
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="/finance/billing?nis=<?= urlencode($tx['nis']) ?>"
                                    class="w-8 h-8 rounded-lg bg-slate-50 text-slate-500 hover:bg-blue-600 hover:text-white inline-flex items-center justify-center transition shadow-sm border border-slate-200" title="Buka Tagihan Siswa">
                                    <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="p-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <span class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Show:</span>
                    <select onchange="window.location.href=updateQS(window.location.href,'limit',this.value)"
                        class="border border-slate-300 rounded-lg px-2 py-1 text-sm outline-none bg-white font-medium">
                        <option value="20"  <?= $limit == 20  ? 'selected' : '' ?>>20 entries</option>
                        <option value="50"  <?= $limit == 50  ? 'selected' : '' ?>>50 entries</option>
                        <option value="100" <?= $limit == 100 ? 'selected' : '' ?>>100 entries</option>
                    </select>
                </div>
                <?php if ($totalPages > 1): ?>
                <div class="flex items-center gap-1.5">
                    <?php $qs = "&limit=$limit&search=" . urlencode($search) . "&status=$statusFilter&date_from=$dateFrom&date_to=$dateTo&class_id=$classId&fee_type_id=$feeTypeId"; ?>
                    <?php if ($currentPage > 1): ?>
                        <a href="?page=<?= $currentPage - 1 . $qs ?>" class="w-8 h-8 flex items-center justify-center bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-50 transition shadow-sm"><i class="fa-solid fa-chevron-left"></i></a>
                    <?php endif; ?>
                    <span class="text-xs font-bold text-slate-600 px-2">Hal <?= $currentPage ?> / <?= $totalPages ?></span>
                    <?php if ($currentPage < $totalPages): ?>
                        <a href="?page=<?= $currentPage + 1 . $qs ?>" class="w-8 h-8 flex items-center justify-center bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-50 transition shadow-sm"><i class="fa-solid fa-chevron-right"></i></a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<!-- Modal Info -->
<div id="infoModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-blue-50 flex justify-between items-center">
            <h3 class="font-bold text-blue-800 flex items-center gap-2"><i class="fa-solid fa-circle-info text-blue-500"></i> Panduan Laporan Keuangan</h3>
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-blue-100 text-blue-500 hover:bg-blue-200 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="p-6 space-y-5 text-sm text-slate-600 max-h-[70vh] overflow-y-auto">
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">1</span> Filter Data</h4>
                <ul class="list-disc list-inside space-y-1.5 text-slate-500">
                    <li><strong class="text-slate-700">Nama/NIS</strong> — Cari transaksi siswa tertentu.</li>
                    <li><strong class="text-slate-700">Status</strong> — Filter Lunas atau Belum Bayar.</li>
                    <li><strong class="text-slate-700">Kelas</strong> — Tampilkan transaksi per kelas tertentu.</li>
                    <li><strong class="text-slate-700">Jenis Tagihan</strong> — Filter per jenis (SPP, Asrama, dll).</li>
                    <li><strong class="text-slate-700">Rentang Tanggal</strong> — Filter berdasarkan periode.</li>
                    <li>Semua kartu ringkasan di atas <strong class="text-slate-700">mengikuti filter aktif</strong>.</li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">2</span> Rekap per Kelas</h4>
                <p class="text-slate-500">Tabel rekap menampilkan total lunas dan piutang per kelas. Klik <strong class="text-slate-700">Filter →</strong> untuk langsung menyaring tabel transaksi ke kelas tersebut.</p>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">3</span> Kolom Tabel</h4>
                <ul class="list-disc list-inside space-y-1.5 text-slate-500">
                    <li><strong class="text-slate-700">Kelas</strong> — Kelas siswa saat ini.</li>
                    <li><strong class="text-slate-700">Metode</strong> — CASH (tunai) atau TRANSFER. Kosong jika belum bayar.</li>
                    <li><strong class="text-slate-700">Aksi <i class="fa-solid fa-arrow-up-right-from-square text-xs"></i></strong> — Buka halaman tagihan siswa tersebut langsung.</li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">4</span> Export</h4>
                <ul class="list-disc list-inside space-y-1.5 text-slate-500">
                    <li><strong class="text-slate-700">Excel</strong> — Unduh data sesuai filter aktif dalam format .xls (termasuk kolom kelas & metode bayar).</li>
                    <li><strong class="text-slate-700">PDF</strong> — Buka halaman cetak sesuai filter aktif.</li>
                    <li><strong class="text-slate-700">Print</strong> — Cetak langsung dari browser (hanya tabel).</li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">5</span> Relasi ke Menu Lain</h4>
                <div class="space-y-2">
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-cash-register text-blue-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Kasir / Tagihan</div><div class="text-[11px] text-slate-400">Data berasal dari pembayaran di <strong>Keuangan → Kasir</strong>.</div></div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-list-check text-green-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Jenis Tagihan</div><div class="text-[11px] text-slate-400">Kelola jenis tagihan di <strong>Keuangan → Jenis Tagihan</strong>.</div></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end">
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="px-5 py-2 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700 transition">Mengerti</button>
        </div>
    </div>
</div>

<style>
@media print {
    body > *:not(main) { display: none !important; }
    main { padding: 0 !important; background: white !important; }
    .mb-6, #infoModal { display: none !important; }
    #printArea { box-shadow: none !important; border: none !important; }
}
</style>

<script>
$(document).ready(function() {
    $('.select2-class').select2({ placeholder: 'Semua Kelas', allowClear: true, width: '100%' });
    $('.select2-feetype').select2({ placeholder: 'Semua Jenis Tagihan', allowClear: true, width: '100%' });
});
function updateQS(uri, key, value) {
    var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
    var sep = uri.indexOf('?') !== -1 ? "&" : "?";
    return uri.match(re) ? uri.replace(re, '$1' + key + "=" + value + '$2') : uri + sep + key + "=" + value;
}
window.onclick = function(e) { if (e.target == document.getElementById('infoModal')) document.getElementById('infoModal').classList.add('hidden'); }
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
