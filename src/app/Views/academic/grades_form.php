<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <!-- Header -->
    <div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl font-extrabold text-slate-800"><?= $title ?></h3>
            <p class="text-slate-500 text-sm mt-1">Kelas <?= htmlspecialchars($schedule['class_name']) ?> | Bobot: Harian <?= $weights['weight_daily'] ?>%, UTS <?= $weights['weight_uts'] ?>%, UAS <?= $weights['weight_uas'] ?>%</p>
        </div>
        <a href="/academic/grades" class="px-4 py-2.5 bg-slate-200 text-slate-700 rounded-xl text-sm font-semibold hover:bg-slate-300 transition flex items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <!-- Rekap Nilai yang Sudah Ada -->
    <?php if (!empty($harianColumns) || !empty($gradeMap)): ?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-6">
        <div class="p-4 bg-slate-50 border-b border-slate-100 flex justify-between items-center">
            <h4 class="font-bold text-slate-700 text-sm"><i class="fa-solid fa-table mr-2 text-slate-400"></i>Rekap Nilai Saat Ini</h4>
            <a href="/academic/grades/print?schedule_id=<?= $schedule['id'] ?>" target="_blank"
                class="px-3 py-1.5 bg-slate-600 text-white rounded-lg text-xs font-bold hover:bg-slate-700 transition flex items-center gap-1.5">
                <i class="fa-solid fa-print"></i> Cetak
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-xs text-left">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-3 py-3 font-bold text-slate-500 uppercase">Nama</th>
                        <?php foreach ($harianColumns as $key => $col): ?>
                        <th class="px-2 py-3 font-bold text-slate-500 text-center" title="<?= htmlspecialchars($col['description'] ?? '') ?>">
                            <div class="uppercase text-[10px]"><?= $col['category'] ?> <?= $col['seq_num'] ?></div>
                            <?php if (!empty($col['date'])): ?><div class="text-[9px] text-slate-400 font-normal"><?= date('d/m', strtotime($col['date'])) ?></div><?php endif; ?>
                            <?php if (!empty($col['description'])): ?><div class="text-[9px] text-slate-400 font-normal truncate max-w-[60px]"><?= htmlspecialchars($col['description']) ?></div><?php endif; ?>
                        </th>
                        <?php endforeach; ?>
                        <th class="px-2 py-3 font-bold text-slate-500 uppercase text-center bg-blue-50">UTS</th>
                        <th class="px-2 py-3 font-bold text-slate-500 uppercase text-center bg-green-50">UAS</th>
                        <th class="px-2 py-3 font-bold text-slate-500 uppercase text-center bg-amber-50">Rata² Harian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php
                    $totalHarianCount = count($harianColumns); // Jumlah UH/Tugas yang sudah ada
                    foreach ($students as $s):
                        // Rata-rata: total semua nilai / jumlah kolom (0 jika tidak ikut)
                        $sumHarian = 0;
                        foreach ($harianColumns as $key => $col) {
                            $sumHarian += (float)($gradeMap[$s['id']][$key] ?? 0);
                        }
                        $avgHarian = $totalHarianCount > 0 ? round($sumHarian / $totalHarianCount, 1) : '-';
                    ?>
                    <tr class="hover:bg-slate-50/80">
                        <td class="px-3 py-2 font-semibold text-slate-800"><?= htmlspecialchars($s['full_name']) ?></td>
                        <?php foreach ($harianColumns as $key => $col): ?>
                        <td class="px-2 py-2 text-center"><?= $gradeMap[$s['id']][$key] ?? '<span class="text-slate-300">-</span>' ?></td>
                        <?php endforeach; ?>
                        <td class="px-2 py-2 text-center bg-blue-50 font-bold"><?= $gradeMap[$s['id']]['UTS'] ?? '<span class="text-slate-300">-</span>' ?></td>
                        <td class="px-2 py-2 text-center bg-green-50 font-bold"><?= $gradeMap[$s['id']]['UAS'] ?? '<span class="text-slate-300">-</span>' ?></td>
                        <td class="px-2 py-2 text-center bg-amber-50 font-bold"><?= $avgHarian ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- Form Input Nilai Baru -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="p-5 bg-slate-50 border-b border-slate-100">
            <h4 class="font-bold text-slate-700 text-sm"><i class="fa-solid fa-pen mr-2 text-slate-400"></i>Input Nilai</h4>
        </div>
        <form action="/academic/grades/store" method="POST" class="p-5">
            <?= \App\Core\Csrf::input() ?>
            <input type="hidden" name="schedule_id" value="<?= $schedule['id'] ?>">

            <!-- Pilih Jenis -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-5 p-4 bg-slate-50 rounded-xl border border-slate-200">
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Jenis Penilaian</label>
                    <select name="grade_type" id="gradeType" onchange="toggleCategory()" class="w-full py-2 px-3 bg-white border border-slate-200 rounded-lg text-sm outline-none">
                        <option value="HARIAN">Harian (UH/Tugas/Quiz)</option>
                        <option value="UTS">UTS</option>
                        <option value="UAS">UAS</option>
                    </select>
                </div>
                <div id="categoryBox">
                    <label class="block text-xs font-bold text-slate-600 mb-1">Kategori</label>
                    <select name="category" class="w-full py-2 px-3 bg-white border border-slate-200 rounded-lg text-sm outline-none">
                        <option value="UH">Ulangan Harian</option>
                        <option value="TUGAS">Tugas</option>
                        <option value="QUIZ">Quiz</option>
                    </select>
                </div>
                <div id="seqBox">
                    <label class="block text-xs font-bold text-slate-600 mb-1">Ke-</label>
                    <input type="number" name="seq_num" value="<?= count($harianColumns) + 1 ?>" min="1" class="w-full py-2 px-3 bg-white border border-slate-200 rounded-lg text-sm outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-slate-600 mb-1">Tanggal</label>
                    <input type="date" name="grade_date" value="<?= date('Y-m-d') ?>" class="w-full py-2 px-3 bg-white border border-slate-200 rounded-lg text-sm outline-none">
                </div>
            </div>
            <div class="mb-5">
                <label class="block text-xs font-bold text-slate-600 mb-1">Keterangan (opsional)</label>
                <input type="text" name="description" placeholder="cth: Bab 3 - Aljabar Linear" class="w-full py-2 px-3 bg-slate-50 border border-slate-200 rounded-lg text-sm outline-none">
            </div>

            <!-- Tabel Input -->
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase w-10 text-center">#</th>
                            <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">NIS</th>
                            <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase">Nama Siswa</th>
                            <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase w-32 text-center">Nilai (0-100)</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($students as $i => $s): ?>
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-4 py-2 text-center text-slate-400 text-xs"><?= $i+1 ?></td>
                            <td class="px-4 py-2 font-mono text-xs text-slate-500"><?= $s['nis'] ?></td>
                            <td class="px-4 py-2 font-semibold text-slate-800"><?= htmlspecialchars($s['full_name']) ?></td>
                            <td class="px-4 py-2 text-center">
                                <input type="number" name="scores[<?= $s['id'] ?>]" min="0" max="100" step="0.1"
                                    class="w-20 py-1.5 px-2 bg-slate-50 border border-slate-200 rounded-lg text-sm text-center outline-none focus:ring-2 focus:ring-blue-500/50">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="mt-5 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-xl font-bold text-sm hover:bg-blue-700 shadow-md transition">
                    <i class="fa-solid fa-save mr-2"></i> Simpan Nilai
                </button>
            </div>
        </form>
    </div>
</main>

<script>
function toggleCategory() {
    var t = document.getElementById('gradeType').value;
    document.getElementById('categoryBox').style.display = t === 'HARIAN' ? '' : 'none';
    document.getElementById('seqBox').style.display = t === 'HARIAN' ? '' : 'none';
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
