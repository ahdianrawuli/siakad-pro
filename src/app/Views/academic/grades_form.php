<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4">
        <div>
            <h3 class="text-2xl font-bold text-gray-800">
                <?= $schedule['subject_name'] ?> - <?= $schedule['class_name'] ?>
            </h3>
            <p class="text-gray-500 text-sm">
                <i class="fa-solid fa-user-tie mr-1"></i> Pengampu: <?= $schedule['teacher_name'] ?? 'Guru Mata Pelajaran' ?>
            </p>
        </div>
        <div class="flex gap-2">
            <a href="/academic/grades" class="bg-gray-200 text-gray-700 px-4 py-2 rounded-lg font-bold hover:bg-gray-300 transition text-sm">Kembali</a>
            <button type="submit" form="gradesForm" class="bg-green-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-green-700 shadow-lg transition text-sm">
                <i class="fa-solid fa-save mr-2"></i> Simpan Nilai
            </button>
        </div>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6 flex flex-wrap gap-6 items-center text-sm text-blue-800">
        <span class="font-bold"><i class="fa-solid fa-circle-info mr-2"></i>Info Bobot:</span>
        <span class="bg-white px-3 py-1 rounded border border-blue-100 shadow-sm" title="Rata-rata UH1, UH2, & Tugas">Harian (Avg): <strong><?= $weights['weight_daily'] ?>%</strong></span>
        <span class="bg-white px-3 py-1 rounded border border-blue-100 shadow-sm">UTS: <strong><?= $weights['weight_uts'] ?>%</strong></span>
        <span class="bg-white px-3 py-1 rounded border border-blue-100 shadow-sm">UAS: <strong><?= $weights['weight_uas'] ?>%</strong></span>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <form id="gradesForm" action="/academic/grades/store" method="POST">
            <?= \App\Core\Csrf::input() ?>
            <input type="hidden" name="schedule_id" value="<?= $schedule['id'] ?>">

            <div class="overflow-x-auto">
                <table class="min-w-full text-left border-collapse">
                    <thead class="bg-gray-50 text-gray-600 uppercase text-xs font-bold border-b border-gray-200">
                        <tr>
                            <th class="px-4 py-4 w-10 text-center">#</th>
                            <th class="px-4 py-4 min-w-[200px]">Nama Siswa</th>
                            <th class="px-2 py-4 w-20 text-center bg-yellow-50 text-yellow-700 border-l border-yellow-100">UH 1</th>
                            <th class="px-2 py-4 w-20 text-center bg-yellow-50 text-yellow-700">UH 2</th>
                            <th class="px-2 py-4 w-20 text-center bg-yellow-50 text-yellow-700 border-r border-yellow-100">Tugas</th>
                            
                            <th class="px-2 py-4 w-24 text-center bg-blue-50 text-blue-700">UTS</th>
                            <th class="px-2 py-4 w-24 text-center bg-green-50 text-green-700">UAS</th>
                            <th class="px-4 py-4 w-24 text-center bg-gray-100 text-gray-800">Akhir</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php 
                        $no = 1;
                        foreach($students as $s): 
                            // Ambil Data Sesuai Kolom DB
                            $uh1 = $gradeMap[$s['id']]['UH1'] ?? 0;
                            $uh2 = $gradeMap[$s['id']]['UH2'] ?? 0;
                            $tugas = $gradeMap[$s['id']]['TUGAS'] ?? 0;
                            $uts = $gradeMap[$s['id']]['UTS'] ?? 0;
                            $uas = $gradeMap[$s['id']]['UAS'] ?? 0;
                            
                            // Hitung Rata-rata Harian (UH1+UH2+Tugas / 3)
                            // Jika nilai 0, tetap dibagi 3 atau bisa disesuaikan logikanya
                            $avgDaily = ($uh1 + $uh2 + $tugas) / 3;

                            // Hitung Nilai Akhir
                            $final = ($avgDaily * $weights['weight_daily']/100) + 
                                     ($uts * $weights['weight_uts']/100) + 
                                     ($uas * $weights['weight_uas']/100);
                        ?>
                        <tr class="hover:bg-gray-50 transition text-sm group">
                            <td class="px-4 py-3 text-center text-gray-500"><?= $no++ ?></td>
                            <td class="px-4 py-3 font-bold text-gray-800">
                                <?= $s['full_name'] ?><br>
                                <span class="text-[10px] text-gray-400 font-normal"><?= $s['nis'] ?></span>
                            </td>
                            
                            <td class="p-0 border-l border-gray-100">
                                <input type="number" step="0.01" min="0" max="100" 
                                       name="grades[<?= $s['id'] ?>][UH1]" 
                                       value="<?= $uh1 ?: '' ?>" 
                                       class="w-full h-12 text-center focus:bg-yellow-50 focus:ring-2 focus:ring-yellow-400 outline-none grade-input group-hover:bg-gray-50"
                                       placeholder="-">
                            </td>
                            <td class="p-0">
                                <input type="number" step="0.01" min="0" max="100" 
                                       name="grades[<?= $s['id'] ?>][UH2]" 
                                       value="<?= $uh2 ?: '' ?>" 
                                       class="w-full h-12 text-center focus:bg-yellow-50 focus:ring-2 focus:ring-yellow-400 outline-none grade-input group-hover:bg-gray-50"
                                       placeholder="-">
                            </td>
                            <td class="p-0 border-r border-gray-100">
                                <input type="number" step="0.01" min="0" max="100" 
                                       name="grades[<?= $s['id'] ?>][TUGAS]" 
                                       value="<?= $tugas ?: '' ?>" 
                                       class="w-full h-12 text-center focus:bg-yellow-50 focus:ring-2 focus:ring-yellow-400 outline-none grade-input group-hover:bg-gray-50"
                                       placeholder="-">
                            </td>

                            <td class="p-0">
                                <input type="number" step="0.01" min="0" max="100" 
                                       name="grades[<?= $s['id'] ?>][UTS]" 
                                       value="<?= $uts ?: '' ?>" 
                                       class="w-full h-12 text-center focus:bg-blue-50 focus:ring-2 focus:ring-blue-400 outline-none grade-input group-hover:bg-gray-50"
                                       placeholder="-">
                            </td>

                            <td class="p-0 border-r border-gray-100">
                                <input type="number" step="0.01" min="0" max="100" 
                                       name="grades[<?= $s['id'] ?>][UAS]" 
                                       value="<?= $uas ?: '' ?>" 
                                       class="w-full h-12 text-center focus:bg-green-50 focus:ring-2 focus:ring-green-400 outline-none grade-input group-hover:bg-gray-50"
                                       placeholder="-">
                            </td>

                            <td class="px-4 py-3 text-center font-bold text-gray-800 bg-gray-50">
                                <span class="final-score"><?= number_format($final, 0) ?></span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</main>

<script>
    // Live Calculator
    const wH = <?= $weights['weight_daily'] ?> / 100;
    const wU = <?= $weights['weight_uts'] ?> / 100;
    const wA = <?= $weights['weight_uas'] ?> / 100;

    function calculateRow(row) {
        // Ambil nilai, default 0 jika kosong
        const uh1 = parseFloat(row.querySelector('input[name*="[UH1]"]').value) || 0;
        const uh2 = parseFloat(row.querySelector('input[name*="[UH2]"]').value) || 0;
        const tugas = parseFloat(row.querySelector('input[name*="[TUGAS]"]').value) || 0;
        
        const uts = parseFloat(row.querySelector('input[name*="[UTS]"]').value) || 0;
        const uas = parseFloat(row.querySelector('input[name*="[UAS]"]').value) || 0;

        // Rata-rata Harian = (UH1 + UH2 + Tugas) / 3
        // Note: Anda bisa ubah logikanya, misal jika UH2 kosong, bagi 2. 
        // Disini saya pukul rata bagi 3 agar user termotivasi isi semua.
        let dailyComponents = 0;
        let divider = 0;
        
        // Logika Dinamis: Hanya bagi dengan jumlah kolom yang diisi (Opsional)
        // Jika ingin simple bagi 3, pakai: const dailyAvg = (uh1 + uh2 + tugas) / 3;
        if(uh1 > 0) { dailyComponents += uh1; divider++; }
        if(uh2 > 0) { dailyComponents += uh2; divider++; }
        if(tugas > 0) { dailyComponents += tugas; divider++; }
        
        const dailyAvg = divider > 0 ? (dailyComponents / divider) : 0;

        const final = (dailyAvg * wH) + (uts * wU) + (uas * wA);
        row.querySelector('.final-score').textContent = final.toFixed(0);
    }

    document.querySelectorAll('.grade-input').forEach(input => {
        input.addEventListener('input', function() {
            calculateRow(this.closest('tr'));
        });
    });
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
