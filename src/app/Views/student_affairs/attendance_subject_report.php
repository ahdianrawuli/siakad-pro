<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Report Absensi Per Mapel</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Rekap kehadiran siswa berdasarkan mata pelajaran.</p>
        </div>
        <?php if ($schedule && !empty($report)): ?>
        <a href="/attendance/students/subject/print?schedule_id=<?= $selectedSchedule ?>&date_from=<?= $dateFrom ?>&date_to=<?= $dateTo ?>" target="_blank"
            class="px-4 py-2.5 bg-slate-600 text-white rounded-xl text-sm font-semibold hover:bg-slate-700 transition flex items-center gap-2 w-fit">
            <i class="fa-solid fa-print"></i> Cetak
        </a>
        <?php endif; ?>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <!-- Filter -->
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 mb-6">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <select name="schedule_id" class="flex-1 min-w-[250px] py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                <option value="">-- Pilih Jadwal --</option>
                <?php foreach ($schedules as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= $selectedSchedule == $s['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($s['class_name']) ?> — <?= htmlspecialchars($s['subject_name']) ?> (<?= $s['day'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="date" name="date_from" value="<?= $dateFrom ?>" class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none">
            <span class="text-slate-400 text-sm">s/d</span>
            <input type="date" name="date_to" value="<?= $dateTo ?>" class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none">
            <button type="submit" class="bg-slate-800 text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-slate-900 transition">Tampilkan</button>
        </form>
    </div>

    <?php if ($schedule && !empty($report)): ?>
    <!-- Info -->
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 mb-6 flex items-center gap-3 text-sm">
        <i class="fa-solid fa-book-open text-blue-500"></i>
        <span><strong class="text-blue-800"><?= htmlspecialchars($schedule['subject_name']) ?></strong> — Kelas <?= htmlspecialchars($schedule['class_name']) ?>
        | Periode: <?= date('d/m/Y', strtotime($dateFrom)) ?> – <?= date('d/m/Y', strtotime($dateTo)) ?></span>
    </div>

    <!-- Table -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase w-10 text-center">No</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase">NIS</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase">Nama Siswa</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase text-center">Hadir</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase text-center">Sakit</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase text-center">Izin</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase text-center">Alpa</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase text-center">Total</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase text-center">% Hadir</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($report as $i => $r):
                        $pct = $r['total'] > 0 ? round($r['hadir'] / $r['total'] * 100) : 0;
                    ?>
                    <tr class="hover:bg-slate-50/80">
                        <td class="px-5 py-3 text-center text-slate-400 text-xs"><?= $i+1 ?></td>
                        <td class="px-5 py-3 font-mono text-xs text-slate-500"><?= $r['nis'] ?></td>
                        <td class="px-5 py-3 font-semibold text-slate-800"><?= htmlspecialchars($r['full_name']) ?></td>
                        <td class="px-5 py-3 text-center font-bold text-green-600"><?= $r['hadir'] ?></td>
                        <td class="px-5 py-3 text-center"><?= $r['sakit'] ?></td>
                        <td class="px-5 py-3 text-center"><?= $r['izin'] ?></td>
                        <td class="px-5 py-3 text-center font-bold text-red-600"><?= $r['alpa'] ?></td>
                        <td class="px-5 py-3 text-center"><?= $r['total'] ?></td>
                        <td class="px-5 py-3 text-center font-bold <?= $pct >= 75 ? 'text-green-600' : 'text-red-600' ?>"><?= $pct ?>%</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <?php elseif ($selectedSchedule): ?>
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 text-sm text-amber-700">
        <i class="fa-solid fa-triangle-exclamation mr-2"></i> Belum ada data absensi untuk jadwal dan periode ini.
    </div>
    <?php endif; ?>

</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
