<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/student_sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">
<?php
$pageTitle    = 'Asrama';
$pageSubtitle = $dorm ? htmlspecialchars($dorm['name']) : 'Belum ditetapkan';
$pageBadge    = $dorm ? 'Kapasitas: ' . $dorm['capacity'] . ' orang' : null;
$pageBadgeIcon = 'fa-bed';
$infoItems    = [
    'Halaman ini menampilkan informasi kamar asrama dan penilaian karakter.',
    'Nilai Karakter meliputi Tahfidz, Bahasa, dan Akhlak.',
    'Riwayat Perizinan menampilkan izin keluar asrama yang pernah diajukan.',
    'Catatan Tahfidz/Tilawah berisi rekap setoran hafalan Anda.',
];
require __DIR__ . '/../layouts/portal_header_card.php';
?>

    <?php \App\Core\Session::flash(); ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- Nilai Karakter Asrama -->
        <div>
            <h2 class="font-bold text-slate-700 mb-3 flex items-center gap-2">
                <i class="fa-solid fa-star text-yellow-500"></i> Nilai Karakter Asrama
            </h2>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <?php if (empty($boardingGrades)): ?>
                    <p class="text-center py-8 text-slate-400 text-sm">Belum ada penilaian.</p>
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
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Kategori</th>
                                <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase">Predikat</th>
                                <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Keterangan</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                        <?php foreach ($gradeCategories as $col => $info):
                            $val = $g[$col] ?? '-';
                            $c = $predicateColor[$val] ?? 'gray';
                        ?>
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-3 font-medium text-slate-800"><?= $info['label'] ?></td>
                                <td class="px-5 py-3 text-center"><span class="bg-<?= $c ?>-100 text-<?= $c ?>-700 text-xs font-bold px-2.5 py-1 rounded-full"><?= htmlspecialchars($val) ?></span></td>
                                <td class="px-5 py-3 text-slate-500 text-xs"><?= htmlspecialchars($g[$info['desc']] ?? '-') ?></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>

        <!-- Perizinan -->
        <div>
            <h2 class="font-bold text-slate-700 mb-3 flex items-center gap-2">
                <i class="fa-solid fa-door-open text-blue-500"></i> Riwayat Perizinan
            </h2>
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <?php if (empty($permits)): ?>
                    <p class="text-center py-8 text-slate-400 text-sm">Belum ada data izin.</p>
                <?php else: ?>
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 border-b border-slate-200">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Tanggal</th>
                                <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Jenis</th>
                                <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                        <?php foreach ($permits as $p):
                            $sc = ['PENDING'=>'yellow','APPROVED'=>'green','REJECTED'=>'red','RETURNED'=>'blue'][$p['status']] ?? 'gray';
                        ?>
                            <tr class="hover:bg-slate-50">
                                <td class="px-5 py-3 text-slate-600"><?= date('d/m/Y', strtotime($p['start_date'])) ?></td>
                                <td class="px-5 py-3 font-medium text-slate-800"><?= $p['type'] ?></td>
                                <td class="px-5 py-3 text-center"><span class="bg-<?= $sc ?>-100 text-<?= $sc ?>-700 text-xs font-bold px-2.5 py-1 rounded-full"><?= $p['status'] ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Log Tahfidz/Tilawah -->
    <h2 class="font-bold text-slate-700 mt-6 mb-3 flex items-center gap-2">
        <i class="fa-solid fa-book-quran text-green-600"></i> Catatan Tahfidz / Tilawah
    </h2>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <?php if (empty($worshipLogs)): ?>
            <p class="text-center py-8 text-slate-400 text-sm">Belum ada catatan.</p>
        <?php else: ?>
            <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Tanggal</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Jenis</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Surat / Ayat</th>
                        <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase">Nilai</th>
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Penyimak</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                <?php foreach ($worshipLogs as $w): ?>
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-3 text-slate-600"><?= date('d M Y', strtotime($w['date'])) ?></td>
                        <td class="px-5 py-3 font-medium text-slate-800"><?= $w['type'] ?></td>
                        <td class="px-5 py-3 text-slate-700"><?= htmlspecialchars($w['surah_name']) ?> (<?= htmlspecialchars($w['verses']) ?>)</td>
                        <td class="px-5 py-3 text-center"><span class="font-bold text-lg <?= $w['grade']==='A'?'text-green-600':($w['grade']==='B'?'text-blue-600':'text-yellow-600') ?>"><?= $w['grade'] ?></span></td>
                        <td class="px-5 py-3 text-slate-500 text-xs"><?= htmlspecialchars($w['teacher_name'] ?? '-') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        <?php endif; ?>
    </div>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
