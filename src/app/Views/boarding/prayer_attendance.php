<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Absensi Sholat</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Pencatatan kehadiran sholat 5 waktu & sunnah santri.</p>
            <div class="mt-3 flex items-center gap-2">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold border border-blue-100">
                    <i class="fa-solid fa-mosque"></i> <?= $totalData ?> Santri
                </div>
                <?php if ($scope !== 'GLOBAL'): ?>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-amber-50 text-amber-700 rounded-lg text-xs font-bold border border-amber-200">
                    <i class="fa-solid fa-filter"></i> <?= $scope ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="/boarding/prayer/print?class_id=<?= urlencode($classId) ?>&date_from=<?= $dateFrom ?>&date_to=<?= $dateTo ?>" target="_blank"
                class="px-4 py-2.5 bg-slate-600 text-white rounded-xl text-sm font-semibold hover:bg-slate-700 transition flex items-center gap-2">
                <i class="fa-solid fa-print"></i> Cetak Rekap
            </a>
            <a href="/boarding/prayer/types"
                class="px-4 py-2.5 bg-purple-600 text-white rounded-xl text-sm font-semibold hover:bg-purple-700 transition flex items-center gap-2">
                <i class="fa-solid fa-gear"></i> Jenis Sholat
            </a>
        </div>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <!-- Filter -->
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 mb-6">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <select name="class_id" onchange="this.form.submit()" class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none">
                <option value="">Semua Kelas</option>
                <?php foreach ($classrooms as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $classId==$c['id']?'selected':'' ?>><?= htmlspecialchars($c['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <div class="relative flex-1 min-w-[180px]">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fa-solid fa-magnifying-glass text-xs"></i></span>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nama / NIS..."
                    class="w-full pl-9 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none">
            </div>
            <input type="date" name="date" value="<?= $date ?>"
                class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none" title="Tanggal input">
            <span class="text-slate-400 text-xs">Rekap:</span>
            <input type="date" name="date_from" value="<?= $dateFrom ?? date('Y-m-01') ?>"
                class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none">
            <span class="text-slate-400 text-xs">s/d</span>
            <input type="date" name="date_to" value="<?= $dateTo ?? date('Y-m-d') ?>"
                class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none">
            <button type="submit" class="bg-slate-800 text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-slate-900 transition">Terapkan</button>
        </form>
    </div>

    <?php if (!empty($students)): ?>
    <form action="/boarding/prayer/store" method="POST">
        <?= \App\Core\Csrf::input() ?>
        <input type="hidden" name="date" value="<?= $date ?>">
        <input type="hidden" name="class_id" value="<?= $classId ?>">

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase w-10 text-center">#</th>
                            <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Nama</th>
                            <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Kelas</th>
                            <?php foreach ($prayerTypes as $pt): ?>
                            <th class="px-3 py-3 text-xs font-bold text-slate-500 uppercase text-center whitespace-nowrap">
                                <?= $pt['name'] ?>
                                <div class="text-[9px] font-normal text-slate-400"><?= $pt['category'] ?></div>
                            </th>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($students as $i => $s): ?>
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-4 py-2 text-center text-slate-400 text-xs"><?= $i+1 ?></td>
                            <td class="px-4 py-2 font-semibold text-slate-800 text-xs"><?= htmlspecialchars($s['full_name']) ?></td>
                            <td class="px-4 py-2 text-xs text-slate-500"><?= htmlspecialchars($s['class_name'] ?? '-') ?></td>
                            <?php foreach ($prayerTypes as $pt):
                                $ex = $existing[$s['id']][$pt['id']] ?? '';
                            ?>
                            <td class="px-2 py-2 text-center">
                                <select name="prayer[<?= $s['id'] ?>][<?= $pt['id'] ?>]"
                                    class="w-full text-[10px] py-1 px-1 border border-slate-200 rounded-lg outline-none bg-slate-50">
                                    <option value="">-</option>
                                    <option value="HADIR" <?= $ex==='HADIR'?'selected':'' ?>>✓ Hadir</option>
                                    <option value="TERLAMBAT" <?= $ex==='TERLAMBAT'?'selected':'' ?>>⏱ Terlambat</option>
                                    <option value="IZIN" <?= $ex==='IZIN'?'selected':'' ?>>📋 Izin</option>
                                    <option value="SAKIT" <?= $ex==='SAKIT'?'selected':'' ?>>🏥 Sakit</option>
                                    <option value="TIDAK_HADIR" <?= $ex==='TIDAK_HADIR'?'selected':'' ?>>✗ Tidak Hadir</option>
                                </select>
                            </td>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="p-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
                <div class="text-xs text-slate-500">✓ = Hadir &nbsp; ⏱ = Terlambat &nbsp; 📋 = Izin &nbsp; 🏥 = Sakit &nbsp; ✗ = Tidak Hadir</div>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-xl font-bold text-sm hover:bg-blue-700 shadow-md transition">
                    <i class="fa-solid fa-save mr-2"></i> Simpan
                </button>
            </div>
        </div>
    </form>

    <!-- Pagination -->
    <?php if ($totalPages > 1): ?>
    <div class="mt-4 flex justify-center gap-1.5">
        <?php $qs = "&class_id=$classId&date=$date&search=" . urlencode($search) . "&date_from=$dateFrom&date_to=$dateTo"; ?>
        <?php if ($currentPage > 1): ?>
            <a href="?page=<?= $currentPage-1 . $qs ?>" class="w-9 h-9 flex items-center justify-center bg-white border border-slate-200 rounded-lg text-xs font-bold hover:text-blue-600 transition shadow-sm"><i class="fa-solid fa-chevron-left"></i></a>
        <?php endif; ?>
        <span class="text-xs font-bold text-slate-600 px-3 py-2">Hal <?= $currentPage ?>/<?= $totalPages ?></span>
        <?php if ($currentPage < $totalPages): ?>
            <a href="?page=<?= $currentPage+1 . $qs ?>" class="w-9 h-9 flex items-center justify-center bg-white border border-slate-200 rounded-lg text-xs font-bold hover:text-blue-600 transition shadow-sm"><i class="fa-solid fa-chevron-right"></i></a>
        <?php endif; ?>
    </div>
    <?php endif; ?>
    <?php else: ?>
    <div class="text-center py-16 text-slate-400">Pilih kelas untuk mulai input absensi sholat.</div>
    <?php endif; ?>

</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
