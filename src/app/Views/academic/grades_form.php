<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">
                <?= $schedule['subject_name'] ?> — <?= $schedule['class_name'] ?>
            </h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">
                <i class="fa-solid fa-user-tie mr-1 text-slate-400"></i> Pengampu: <?= $schedule['teacher_name'] ?? 'Guru Mata Pelajaran' ?>
            </p>
            <div class="mt-3 flex items-center gap-2">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-green-50 text-green-700 rounded-lg text-xs font-bold border border-green-100">
                    <i class="fa-solid fa-users"></i> <?= count($students) ?> Siswa
                </div>
                <button onclick="document.getElementById('infoModal').classList.remove('hidden')"
                    class="w-7 h-7 rounded-full bg-slate-100 text-slate-500 hover:bg-blue-100 hover:text-blue-600 flex items-center justify-center transition-colors border border-slate-200" title="Panduan Penggunaan">
                    <i class="fa-solid fa-circle-info text-sm"></i>
                </button>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="/academic/grades" class="px-4 py-2.5 bg-white text-slate-700 border border-slate-300 rounded-xl text-sm font-semibold hover:bg-slate-50 transition-all flex items-center gap-2">
                <i class="fa-solid fa-arrow-left text-slate-400"></i> Kembali
            </a>
        </div>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <!-- Info Bobot -->
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 mb-6 flex flex-wrap gap-4 items-center text-sm text-blue-800">
        <span class="font-bold flex items-center gap-1.5"><i class="fa-solid fa-circle-info"></i> Info Bobot Penilaian:</span>
        <span class="bg-white px-3 py-1.5 rounded-xl border border-blue-100 shadow-sm text-xs font-semibold">Harian (Avg): <strong><?= $weights['weight_daily'] ?>%</strong></span>
        <span class="bg-white px-3 py-1.5 rounded-xl border border-blue-100 shadow-sm text-xs font-semibold">UTS: <strong><?= $weights['weight_uts'] ?>%</strong></span>
        <span class="bg-white px-3 py-1.5 rounded-xl border border-blue-100 shadow-sm text-xs font-semibold">UAS: <strong><?= $weights['weight_uas'] ?>%</strong></span>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <form id="gradesForm" action="/academic/grades/store" method="POST">
            <?= \App\Core\Csrf::input() ?>
            <input type="hidden" name="schedule_id" value="<?= $schedule['id'] ?>">

            <div class="overflow-x-auto">
                <table class="min-w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-xs font-bold uppercase tracking-wider">
                            <th class="px-4 py-4 text-slate-500 text-center w-10">#</th>
                            <th class="px-4 py-4 text-slate-500 min-w-[200px]">Nama Siswa</th>
                            <th class="px-2 py-4 text-center bg-yellow-50 text-yellow-700 border-l border-yellow-100 w-20">UH 1</th>
                            <th class="px-2 py-4 text-center bg-yellow-50 text-yellow-700 w-20">UH 2</th>
                            <th class="px-2 py-4 text-center bg-yellow-50 text-yellow-700 border-r border-yellow-100 w-20">Tugas</th>
                            <th class="px-2 py-4 text-center bg-blue-50 text-blue-700 w-24">UTS</th>
                            <th class="px-2 py-4 text-center bg-green-50 text-green-700 border-r border-green-100 w-24">UAS</th>
                            <th class="px-4 py-4 text-center bg-slate-100 text-slate-700 w-24">Akhir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <?php
                        $no = 1;
                        foreach ($students as $s):
                            $uh1   = $gradeMap[$s['id']]['UH1']   ?? 0;
                            $uh2   = $gradeMap[$s['id']]['UH2']   ?? 0;
                            $tugas = $gradeMap[$s['id']]['TUGAS'] ?? 0;
                            $uts   = $gradeMap[$s['id']]['UTS']   ?? 0;
                            $uas   = $gradeMap[$s['id']]['UAS']   ?? 0;

                            $dc = 0; $dv = 0;
                            if ($uh1 > 0)   { $dc += $uh1;   $dv++; }
                            if ($uh2 > 0)   { $dc += $uh2;   $dv++; }
                            if ($tugas > 0) { $dc += $tugas; $dv++; }
                            $dailyAvg = $dv > 0 ? $dc / $dv : 0;
                            $final = ($dailyAvg * $weights['weight_daily'] / 100) + ($uts * $weights['weight_uts'] / 100) + ($uas * $weights['weight_uas'] / 100);
                        ?>
                        <tr class="hover:bg-slate-50/80 transition-colors text-sm group">
                            <td class="px-4 py-3 text-center text-slate-400 text-xs"><?= $no++ ?></td>
                            <td class="px-4 py-3">
                                <div class="font-extrabold text-slate-800"><?= $s['full_name'] ?></div>
                                <div class="text-[10px] text-slate-400 font-mono"><?= $s['nis'] ?></div>
                            </td>
                            <td class="p-0 border-l border-slate-100">
                                <input type="number" step="0.01" min="0" max="100" name="grades[<?= $s['id'] ?>][UH1]" value="<?= $uh1 ?: '' ?>"
                                    class="w-full h-12 text-center focus:bg-yellow-50 focus:ring-2 focus:ring-yellow-400 outline-none grade-input group-hover:bg-slate-50/50 text-sm" placeholder="–">
                            </td>
                            <td class="p-0">
                                <input type="number" step="0.01" min="0" max="100" name="grades[<?= $s['id'] ?>][UH2]" value="<?= $uh2 ?: '' ?>"
                                    class="w-full h-12 text-center focus:bg-yellow-50 focus:ring-2 focus:ring-yellow-400 outline-none grade-input group-hover:bg-slate-50/50 text-sm" placeholder="–">
                            </td>
                            <td class="p-0 border-r border-slate-100">
                                <input type="number" step="0.01" min="0" max="100" name="grades[<?= $s['id'] ?>][TUGAS]" value="<?= $tugas ?: '' ?>"
                                    class="w-full h-12 text-center focus:bg-yellow-50 focus:ring-2 focus:ring-yellow-400 outline-none grade-input group-hover:bg-slate-50/50 text-sm" placeholder="–">
                            </td>
                            <td class="p-0">
                                <input type="number" step="0.01" min="0" max="100" name="grades[<?= $s['id'] ?>][UTS]" value="<?= $uts ?: '' ?>"
                                    class="w-full h-12 text-center focus:bg-blue-50 focus:ring-2 focus:ring-blue-400 outline-none grade-input group-hover:bg-slate-50/50 text-sm" placeholder="–">
                            </td>
                            <td class="p-0 border-r border-slate-100">
                                <input type="number" step="0.01" min="0" max="100" name="grades[<?= $s['id'] ?>][UAS]" value="<?= $uas ?: '' ?>"
                                    class="w-full h-12 text-center focus:bg-green-50 focus:ring-2 focus:ring-green-400 outline-none grade-input group-hover:bg-slate-50/50 text-sm" placeholder="–">
                            </td>
                            <td class="px-4 py-3 text-center bg-slate-50">
                                <span class="final-score font-extrabold text-slate-800 text-sm"><?= number_format($final, 0) ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Footer -->
            <div class="p-4 bg-slate-50 border-t border-slate-200 flex justify-end">
                <button type="submit" class="px-8 py-2.5 bg-green-600 text-white rounded-xl font-bold hover:bg-green-700 shadow-md shadow-green-500/20 transition-all text-sm flex items-center gap-2">
                    <i class="fa-solid fa-save"></i> Simpan Semua Nilai
                </button>
            </div>
        </form>
    </div>
</main>

<!-- Modal Info -->
<div id="infoModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-blue-50 flex justify-between items-center">
            <h3 class="font-bold text-blue-800 flex items-center gap-2"><i class="fa-solid fa-circle-info text-blue-500"></i> Panduan Input Nilai</h3>
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-blue-100 text-blue-500 hover:bg-blue-200 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="p-6 space-y-5 text-sm text-slate-600 overflow-y-auto max-h-[70vh]">
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">1</span> Cara Penggunaan</h4>
                <ol class="list-decimal list-inside space-y-1.5 text-slate-500">
                    <li>Isi nilai pada kolom <strong class="text-yellow-700">UH 1, UH 2, Tugas</strong> (nilai harian), <strong class="text-blue-700">UTS</strong>, dan <strong class="text-green-700">UAS</strong>.</li>
                    <li>Kolom <strong class="text-slate-700">Akhir</strong> dihitung otomatis secara real-time sesuai bobot.</li>
                    <li>Kosongkan kolom jika nilai belum tersedia — tidak akan mempengaruhi perhitungan.</li>
                    <li>Klik <strong class="text-slate-700">Simpan Semua Nilai</strong> setelah selesai mengisi.</li>
                </ol>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">2</span> Rumus Nilai Akhir</h4>
                <div class="bg-slate-50 rounded-xl p-3 border border-slate-200 font-mono text-xs text-slate-700">
                    Harian = Avg(UH1, UH2, Tugas) × <?= $weights['weight_daily'] ?>%<br>
                    + UTS × <?= $weights['weight_uts'] ?>%<br>
                    + UAS × <?= $weights['weight_uas'] ?>%<br>
                    <span class="text-blue-600 font-bold">= Nilai Akhir</span>
                </div>
                <p class="text-[11px] text-slate-400 mt-1.5">Bobot dapat diubah di <strong>Akademik → Bobot Penilaian</strong>.</p>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">3</span> Relasi ke Menu Lain</h4>
                <div class="space-y-2">
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-sliders text-purple-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Bobot Penilaian</div><div class="text-[11px] text-slate-400">Persentase bobot dikonfigurasi di <strong>Akademik → Bobot Penilaian</strong>.</div></div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-file-invoice text-orange-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Rapor Siswa</div><div class="text-[11px] text-slate-400">Nilai akhir yang tersimpan akan otomatis muncul di rapor siswa.</div></div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-book-open text-green-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Jurnal Guru</div><div class="text-[11px] text-slate-400">Data kehadiran dari jurnal mengajar dapat dijadikan acuan nilai keaktifan.</div></div>
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
    const wH = <?= $weights['weight_daily'] ?> / 100;
    const wU = <?= $weights['weight_uts'] ?> / 100;
    const wA = <?= $weights['weight_uas'] ?> / 100;

    function calculateRow(row) {
        const uh1   = parseFloat(row.querySelector('input[name*="[UH1]"]').value)   || 0;
        const uh2   = parseFloat(row.querySelector('input[name*="[UH2]"]').value)   || 0;
        const tugas = parseFloat(row.querySelector('input[name*="[TUGAS]"]').value) || 0;
        const uts   = parseFloat(row.querySelector('input[name*="[UTS]"]').value)   || 0;
        const uas   = parseFloat(row.querySelector('input[name*="[UAS]"]').value)   || 0;

        let dc = 0, dv = 0;
        if (uh1 > 0)   { dc += uh1;   dv++; }
        if (uh2 > 0)   { dc += uh2;   dv++; }
        if (tugas > 0) { dc += tugas; dv++; }
        const dailyAvg = dv > 0 ? dc / dv : 0;

        const final = (dailyAvg * wH) + (uts * wU) + (uas * wA);
        row.querySelector('.final-score').textContent = final.toFixed(0);
    }

    document.querySelectorAll('.grade-input').forEach(input => {
        input.addEventListener('input', function() { calculateRow(this.closest('tr')); });
    });

    window.onclick = function(e) { if (e.target == document.getElementById('infoModal')) document.getElementById('infoModal').classList.add('hidden'); }
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
