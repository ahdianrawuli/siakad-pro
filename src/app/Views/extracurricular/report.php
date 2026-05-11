<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Rapor Ekstrakurikuler</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Rekap kehadiran anggota per bulan per kegiatan ekstrakurikuler.</p>
            <div class="mt-3 flex items-center gap-2">
                <?php if ($selectedEkskul && !empty($members)): ?>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold border border-blue-100">
                    <i class="fa-solid fa-users"></i> Total Anggota: <?= count($members) ?>
                </div>
                <?php endif; ?>
                <?php if (($scope ?? 'GLOBAL') !== 'GLOBAL'): ?>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-amber-50 text-amber-700 rounded-lg text-xs font-bold border border-amber-200">
                    <i class="fa-solid fa-filter"></i> Unit: <?= $scope ?>
                </div>
                <?php endif; ?>
                <button onclick="document.getElementById('infoModal').classList.remove('hidden')"
                    class="w-7 h-7 rounded-full bg-slate-100 text-slate-500 hover:bg-blue-100 hover:text-blue-600 flex items-center justify-center transition-colors border border-slate-200" title="Panduan Penggunaan">
                    <i class="fa-solid fa-circle-info text-sm"></i>
                </button>
            </div>
        </div>
        <?php if ($selectedEkskul && !empty($members)): ?>
        <a href="/extracurricular/report/print?id=<?= $selectedEkskul ?>&month=<?= $month ?>" target="_blank"
            class="px-4 py-2.5 bg-slate-700 text-white rounded-xl text-sm font-semibold hover:bg-slate-800 transition-all flex items-center gap-2 w-fit">
            <i class="fa-solid fa-print"></i> Cetak Rapor
        </a>
        <?php endif; ?>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <div class="flex flex-col gap-6">

        <!-- Filter -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
            <form method="GET" action="/extracurricular/report" class="flex flex-wrap items-center gap-3">
                <div class="flex-1 min-w-[200px]">
                    <select name="id" onchange="this.form.submit()"
                        class="w-full py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                        <option value="">-- Pilih Ekstrakurikuler --</option>
                        <?php foreach ($ekskuls as $e): ?>
                            <option value="<?= $e['id'] ?>" <?= $selectedEkskul == $e['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($e['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <input type="month" name="month" value="<?= $month ?>" onchange="this.form.submit()"
                    class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
            </form>
        </div>

        <?php if (!$selectedEkskul): ?>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm py-16 text-center text-slate-400 text-sm font-medium">
            <i class="fa-solid fa-hand-pointer text-3xl mb-3 block text-slate-300"></i>
            Pilih ekstrakurikuler dan bulan untuk melihat rekap kehadiran.
        </div>
        <?php elseif (empty($members)): ?>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm py-16 text-center text-slate-400 text-sm font-medium">
            <i class="fa-solid fa-user-slash text-3xl mb-3 block text-slate-300"></i>
            Belum ada anggota terdaftar di ekstrakurikuler ini.
        </div>
        <?php else: ?>

        <!-- Table Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="px-5 py-3 bg-slate-50 border-b border-slate-200 flex items-center justify-between">
                <span class="text-sm font-bold text-slate-700"><?= htmlspecialchars($ekskulName) ?></span>
                <span class="text-xs text-slate-500"><?= date('F Y', strtotime($month . '-01')) ?></span>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full whitespace-nowrap text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">No</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Santri</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Kelas</th>
                            <th class="px-5 py-4 text-xs font-bold text-green-600 uppercase tracking-wider text-center">Hadir</th>
                            <th class="px-5 py-4 text-xs font-bold text-blue-600 uppercase tracking-wider text-center">Izin</th>
                            <th class="px-5 py-4 text-xs font-bold text-yellow-600 uppercase tracking-wider text-center">Sakit</th>
                            <th class="px-5 py-4 text-xs font-bold text-red-600 uppercase tracking-wider text-center">Alpa</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Total</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">% Hadir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <?php $no = 1; foreach ($members as $m):
                            $s = $summary[$m['student_id']] ?? ['HADIR'=>0,'IZIN'=>0,'SAKIT'=>0,'ALPA'=>0,'total'=>0];
                            $pct = $s['total'] > 0 ? round($s['HADIR'] / $s['total'] * 100) : 0;
                        ?>
                        <tr class="hover:bg-slate-50/80 transition-colors text-sm">
                            <td class="px-5 py-4 text-slate-500 font-semibold"><?= $no++ ?></td>
                            <td class="px-5 py-4">
                                <div class="font-extrabold text-slate-800"><?= htmlspecialchars($m['full_name']) ?></div>
                                <div class="text-[10px] text-slate-400 font-mono mt-0.5">NIS: <?= $m['nis'] ?></div>
                            </td>
                            <td class="px-5 py-4 text-slate-600"><?= $m['class_name'] ?? '-' ?></td>
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-green-50 text-green-700 font-bold text-sm border border-green-200"><?= $s['HADIR'] ?></span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-blue-50 text-blue-700 font-bold text-sm border border-blue-200"><?= $s['IZIN'] ?></span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-yellow-50 text-yellow-700 font-bold text-sm border border-yellow-200"><?= $s['SAKIT'] ?></span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-red-50 text-red-700 font-bold text-sm border border-red-200"><?= $s['ALPA'] ?></span>
                            </td>
                            <td class="px-5 py-4 text-center font-bold text-slate-700"><?= $s['total'] ?></td>
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-bold border
                                    <?= $pct >= 75 ? 'bg-green-50 text-green-700 border-green-200' : ($pct >= 50 ? 'bg-yellow-50 text-yellow-700 border-yellow-200' : 'bg-red-50 text-red-700 border-red-200') ?>">
                                    <?= $pct ?>%
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php endif; ?>
    </div>
</main>

<!-- Modal Info -->
<div id="infoModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-blue-50 flex justify-between items-center">
            <h3 class="font-bold text-blue-800 flex items-center gap-2"><i class="fa-solid fa-circle-info text-blue-500"></i> Panduan Rapor Ekskul</h3>
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-blue-100 text-blue-500 hover:bg-blue-200 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="p-6 space-y-5 text-sm text-slate-600">
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">1</span> Cara Penggunaan</h4>
                <ol class="list-decimal list-inside space-y-1.5 text-slate-500">
                    <li>Pilih <strong class="text-slate-700">Ekstrakurikuler</strong> dan <strong class="text-slate-700">Bulan</strong> yang ingin dilihat.</li>
                    <li>Tabel menampilkan rekap Hadir, Izin, Sakit, Alpa, dan persentase kehadiran.</li>
                    <li>Klik <strong class="text-slate-700">Cetak Rapor</strong> untuk mencetak halaman ini.</li>
                </ol>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">2</span> Relasi ke Menu Lain</h4>
                <div class="space-y-2">
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-calendar-check text-green-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Absensi Ekskul</div><div class="text-[11px] text-slate-400">Data diambil dari pencatatan di menu <strong>Ekstrakurikuler → Absensi</strong>.</div></div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-users text-purple-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Anggota Ekskul</div><div class="text-[11px] text-slate-400">Hanya anggota terdaftar yang muncul di rapor ini.</div></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end">
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="px-5 py-2 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700 transition">Mengerti</button>
        </div>
    </div>
</div>

<script>
    window.onclick = function(e) {
        if (e.target == document.getElementById('infoModal')) document.getElementById('infoModal').classList.add('hidden');
    }
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
