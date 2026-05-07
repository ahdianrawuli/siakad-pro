<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/student_sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">
    <h1 class="text-xl md:text-2xl font-bold text-gray-800 mb-6">Asrama</h1>

    <?php \App\Core\Session::flash(); ?>

    <!-- Info Kamar -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6 flex items-center gap-4">
        <div class="w-12 h-12 bg-green-100 text-green-700 rounded-xl flex items-center justify-center text-xl shrink-0">
            <i class="fa-solid fa-bed"></i>
        </div>
        <div>
            <p class="text-xs text-gray-500 uppercase font-bold">Kamar Asrama</p>
            <p class="font-bold text-gray-800 text-lg"><?= $dorm ? htmlspecialchars($dorm['name']) : 'Belum ditetapkan' ?></p>
            <?php if ($dorm): ?><p class="text-xs text-gray-500">Kapasitas: <?= $dorm['capacity'] ?> orang</p><?php endif; ?>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Nilai Karakter Asrama -->
        <div>
            <h2 class="font-bold text-gray-700 mb-3">Nilai Karakter Asrama</h2>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <?php if (empty($boardingGrades)): ?>
                    <p class="text-center py-8 text-gray-400 text-sm">Belum ada penilaian.</p>
                <?php else: ?>
                    <?php
                    $predicateColor = ['A'=>'green','B'=>'blue','C'=>'yellow','D'=>'red'];
                    $gradeCategories = [
                        'tahfidz_grade'   => ['label' => 'Tahfidz',   'desc' => 'tahfidz_desc'],
                        'language_grade'  => ['label' => 'Bahasa',    'desc' => 'language_desc'],
                        'character_grade' => ['label' => 'Akhlak',    'desc' => 'character_desc'],
                    ];
                    $g = !empty($boardingGrades) ? $boardingGrades[0] : [];
                    ?>
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                            <tr><th class="px-4 py-3 text-left">Kategori</th><th class="px-4 py-3 text-center">Predikat</th><th class="px-4 py-3 text-left">Keterangan</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                        <?php foreach ($gradeCategories as $col => $info):
                            $val = $g[$col] ?? '-';
                            $c = $predicateColor[$val] ?? 'gray';
                        ?>
                            <tr>
                                <td class="px-4 py-3 font-medium text-gray-800"><?= $info['label'] ?></td>
                                <td class="px-4 py-3 text-center"><span class="bg-<?= $c ?>-100 text-<?= $c ?>-700 text-xs font-bold px-2 py-0.5 rounded-full"><?= htmlspecialchars($val) ?></span></td>
                                <td class="px-4 py-3 text-gray-500 text-xs"><?= htmlspecialchars($g[$info['desc']] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- Perizinan -->
        <div>
            <h2 class="font-bold text-gray-700 mb-3">Riwayat Perizinan</h2>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                <?php if (empty($permits)): ?>
                    <p class="text-center py-8 text-gray-400 text-sm">Belum ada data izin.</p>
                <?php else: ?>
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                            <tr><th class="px-4 py-3 text-left">Tanggal</th><th class="px-4 py-3 text-left">Jenis</th><th class="px-4 py-3 text-center">Status</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                        <?php foreach ($permits as $p):
                            $sc = ['PENDING'=>'yellow','APPROVED'=>'green','REJECTED'=>'red','RETURNED'=>'blue'][$p['status']] ?? 'gray';
                        ?>
                            <tr>
                                <td class="px-4 py-3 text-gray-600"><?= date('d/m/Y', strtotime($p['start_date'])) ?></td>
                                <td class="px-4 py-3 font-medium text-gray-800"><?= $p['type'] ?></td>
                                <td class="px-4 py-3 text-center"><span class="bg-<?= $sc ?>-100 text-<?= $sc ?>-700 text-xs font-bold px-2 py-0.5 rounded-full"><?= $p['status'] ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Log Tahfidz/Tilawah -->
    <h2 class="font-bold text-gray-700 mt-6 mb-3">Catatan Tahfidz / Tilawah</h2>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <?php if (empty($worshipLogs)): ?>
            <p class="text-center py-8 text-gray-400 text-sm">Belum ada catatan.</p>
        <?php else: ?>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Tanggal</th>
                        <th class="px-4 py-3 text-left">Jenis</th>
                        <th class="px-4 py-3 text-left">Surat / Ayat</th>
                        <th class="px-4 py-3 text-center">Nilai</th>
                        <th class="px-4 py-3 text-left">Penyimak</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                <?php foreach ($worshipLogs as $w): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 text-gray-600"><?= date('d M Y', strtotime($w['date'])) ?></td>
                        <td class="px-4 py-3 font-medium text-gray-800"><?= $w['type'] ?></td>
                        <td class="px-4 py-3 text-gray-700"><?= htmlspecialchars($w['surah_name']) ?> (<?= htmlspecialchars($w['verses']) ?>)</td>
                        <td class="px-4 py-3 text-center"><span class="font-bold text-lg <?= $w['grade']==='A'?'text-green-600':($w['grade']==='B'?'text-blue-600':'text-yellow-600') ?>"><?= $w['grade'] ?></span></td>
                        <td class="px-4 py-3 text-gray-500 text-xs"><?= htmlspecialchars($w['teacher_name'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
