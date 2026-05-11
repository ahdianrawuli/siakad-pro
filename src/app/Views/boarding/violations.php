<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Rekap Pelanggaran Asrama</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Rekapitulasi pelanggaran santri yang tinggal di asrama.</p>
            <div class="mt-3 flex items-center gap-2">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold border border-blue-100">
                    <i class="fa-solid fa-users"></i> <?= count($students) ?> Santri
                </div>
                <?php if (($scope ?? 'GLOBAL') !== 'GLOBAL'): ?>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-amber-50 text-amber-700 rounded-lg text-xs font-bold border border-amber-200">
                    <i class="fa-solid fa-filter"></i> Unit: <?= $scope ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="/asrama/violations/print?dorm_id=<?= urlencode($dormId) ?>&from=<?= $from ?>&to=<?= $to ?>" target="_blank"
                class="px-4 py-2.5 bg-slate-600 text-white rounded-xl text-sm font-semibold hover:bg-slate-700 transition flex items-center gap-2 w-fit">
                <i class="fa-solid fa-print"></i> Cetak
            </a>
        </div>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <!-- Filter -->
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 mb-6">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <select name="dorm_id" class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                <option value="">Semua Kamar</option>
                <?php foreach ($dorms as $d): ?>
                    <option value="<?= $d['id'] ?>" <?= $dormId == $d['id'] ? 'selected' : '' ?>><?= htmlspecialchars($d['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <div class="flex items-center gap-2">
                <input type="date" name="from" value="<?= $from ?>" class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                <span class="text-slate-400 text-sm">s/d</span>
                <input type="date" name="to" value="<?= $to ?>" class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
            </div>
            <button type="submit" class="bg-slate-800 text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-slate-900 transition-colors">Terapkan</button>
        </form>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Santri</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Kamar</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Jml Pelanggaran</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Total Poin</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($students)): ?>
                        <tr><td colspan="5" class="px-5 py-16 text-center text-slate-400">Tidak ada data.</td></tr>
                    <?php endif; ?>
                    <?php foreach ($students as $s): ?>
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="px-5 py-4">
                            <div class="font-bold text-slate-800"><?= htmlspecialchars($s['full_name']) ?></div>
                            <div class="text-xs text-slate-400 font-mono"><?= $s['nis'] ?></div>
                        </td>
                        <td class="px-5 py-4 text-slate-600 text-xs"><?= htmlspecialchars($s['dorm_name'] ?? '-') ?></td>
                        <td class="px-5 py-4 text-center font-bold <?= $s['total_violations'] > 0 ? 'text-red-600' : 'text-slate-400' ?>">
                            <?= $s['total_violations'] ?: '-' ?>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <?php if ($s['total_points'] > 0): ?>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border
                                <?= $s['total_points'] >= 100 ? 'bg-red-50 text-red-700 border-red-200' : ($s['total_points'] >= 50 ? 'bg-yellow-50 text-yellow-700 border-yellow-200' : 'bg-orange-50 text-orange-700 border-orange-200') ?>">
                                <?= $s['total_points'] ?> poin
                            </span>
                            <?php else: ?>
                            <span class="text-slate-300 text-xs">—</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <?php
                            $p = $s['total_points'];
                            if ($p >= 100) { $badge = ['bg-red-50 text-red-700 border-red-200', 'Kritis']; }
                            elseif ($p >= 50) { $badge = ['bg-yellow-50 text-yellow-700 border-yellow-200', 'Perhatian']; }
                            elseif ($p > 0)  { $badge = ['bg-orange-50 text-orange-700 border-orange-200', 'Ringan']; }
                            else             { $badge = ['bg-green-50 text-green-700 border-green-200', 'Baik']; }
                            ?>
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border <?= $badge[0] ?>"><?= $badge[1] ?></span>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <!-- Pagination -->
        <div class="p-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="text-xs text-slate-500 font-semibold">Show:</span>
                <select onchange="window.location.href=updateQS(window.location.href,'limit',this.value)"
                    class="border border-slate-300 rounded-lg px-2 py-1 text-sm outline-none bg-white">
                    <option value="20"  <?= $limit==20  ? 'selected':'' ?>>20</option>
                    <option value="50"  <?= $limit==50  ? 'selected':'' ?>>50</option>
                    <option value="100" <?= $limit==100 ? 'selected':'' ?>>100</option>
                </select>
                <span class="text-xs text-slate-500"><?= $totalData ?> total</span>
            </div>
            <?php if ($totalPages > 1):
                $qs = "&dorm_id=$dormId&from=$from&to=$to&limit=$limit"; ?>
            <div class="flex items-center gap-1.5">
                <?php if ($currentPage > 1): ?>
                    <a href="?page=<?= $currentPage-1 . $qs ?>" class="w-8 h-8 flex items-center justify-center bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:text-blue-600 transition shadow-sm"><i class="fa-solid fa-chevron-left"></i></a>
                <?php endif; ?>
                <span class="text-xs font-bold text-slate-600 px-2">Hal <?= $currentPage ?> / <?= $totalPages ?></span>
                <?php if ($currentPage < $totalPages): ?>
                    <a href="?page=<?= $currentPage+1 . $qs ?>" class="w-8 h-8 flex items-center justify-center bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:text-blue-600 transition shadow-sm"><i class="fa-solid fa-chevron-right"></i></a>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

</main>

<script>
function updateQS(uri,key,value){var re=new RegExp("([?&])"+key+"=.*?(&|$)","i");var sep=uri.indexOf('?')!==-1?"&":"?";return uri.match(re)?uri.replace(re,'$1'+key+"="+value+'$2'):uri+sep+key+"="+value;}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
