<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Absensi Per Mata Pelajaran</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Input kehadiran siswa berdasarkan jadwal pelajaran.</p>
        </div>
        <a href="/student-affairs/attendance" class="px-4 py-2.5 bg-slate-200 text-slate-700 rounded-xl text-sm font-semibold hover:bg-slate-300 transition flex items-center gap-2 w-fit">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <!-- Pilih Jadwal -->
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 mb-6">
        <form method="GET" action="/attendance/students/subject" class="flex flex-wrap items-center gap-3">
            <select name="schedule_id" onchange="this.form.submit()" class="flex-1 min-w-[250px] py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                <option value="">-- Pilih Jadwal Pelajaran --</option>
                <?php foreach ($schedules as $s): ?>
                    <option value="<?= $s['id'] ?>" <?= $selectedSchedule == $s['id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($s['class_name']) ?> — <?= htmlspecialchars($s['subject_name']) ?> (<?= $s['day'] ?> <?= substr($s['start_time'],0,5) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            <input type="date" name="date" value="<?= $selectedDate ?>" onchange="this.form.submit()"
                class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
        </form>
    </div>

    <?php if ($schedule && !empty($students)): ?>
    <!-- Info Jadwal -->
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 mb-6 flex items-center gap-3">
        <i class="fa-solid fa-book-open text-blue-500 text-lg"></i>
        <div class="text-sm">
            <span class="font-bold text-blue-800"><?= htmlspecialchars($schedule['subject_name']) ?></span>
            — Kelas <?= htmlspecialchars($schedule['class_name']) ?>
            | <?= $schedule['day'] ?> <?= substr($schedule['start_time'],0,5) ?>–<?= substr($schedule['end_time'],0,5) ?>
            | Tanggal: <strong><?= date('d/m/Y', strtotime($selectedDate)) ?></strong>
        </div>
    </div>

    <!-- Form Absensi -->
    <form action="/attendance/students/store" method="POST">
        <?= \App\Core\Csrf::input() ?>
        <input type="hidden" name="classroom_id" value="<?= $schedule['classroom_id'] ?>">
        <input type="hidden" name="schedule_id" value="<?= $selectedSchedule ?>">
        <input type="hidden" name="date" value="<?= $selectedDate ?>">

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full text-left text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase w-10 text-center">#</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase">NIS</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase">Nama Siswa</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase text-center">H</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase text-center">S</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase text-center">I</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase text-center">A</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase">Ket.</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php foreach ($students as $i => $s):
                            $ex = $existing[$s['id']] ?? null;
                            $st = $ex['status'] ?? 'H';
                        ?>
                        <tr class="hover:bg-slate-50/80">
                            <td class="px-5 py-3 text-center text-slate-400 text-xs"><?= $i+1 ?></td>
                            <td class="px-5 py-3 font-mono text-xs text-slate-500"><?= $s['nis'] ?></td>
                            <td class="px-5 py-3 font-semibold text-slate-800"><?= htmlspecialchars($s['full_name']) ?></td>
                            <?php foreach (['H','S','I','A'] as $v): ?>
                            <td class="px-5 py-3 text-center">
                                <input type="radio" name="attendance[<?= $s['id'] ?>]" value="<?= $v ?>" <?= $st === $v ? 'checked' : '' ?>
                                    class="w-4 h-4 text-blue-600 focus:ring-blue-500">
                            </td>
                            <?php endforeach; ?>
                            <td class="px-5 py-3">
                                <input type="text" name="notes[<?= $s['id'] ?>]" value="<?= htmlspecialchars($ex['notes'] ?? '') ?>"
                                    class="w-full px-2 py-1.5 bg-slate-50 border border-slate-200 rounded-lg text-xs outline-none" placeholder="Opsional">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="p-4 bg-slate-50 border-t border-slate-200 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-xl font-bold text-sm hover:bg-blue-700 shadow-md transition">
                    <i class="fa-solid fa-save mr-2"></i> Simpan Absensi
                </button>
            </div>
        </div>
    </form>

    <?php elseif ($selectedSchedule): ?>
    <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5 text-sm text-amber-700">
        <i class="fa-solid fa-triangle-exclamation mr-2"></i> Tidak ada siswa aktif di kelas ini.
    </div>
    <?php endif; ?>

</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
