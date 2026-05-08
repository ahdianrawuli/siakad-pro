<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/parent_sidebar.php'; ?>
<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">
<?php
$pageTitle    = 'Nilai Akademik';
$pageSubtitle = $student ? htmlspecialchars($student['full_name']) . ' — Kelas ' . htmlspecialchars($student['class_name'] ?? '-') : 'Pilih santri terlebih dahulu';
$pageBadge    = $activeYear ? 'TA: ' . htmlspecialchars($activeYear['name']) : null;
$pageBadgeIcon = 'fa-chart-bar';
$infoItems    = [
    'Halaman ini menampilkan nilai akademik santri per mata pelajaran.',
    'Status "Tuntas" berarti nilai akhir ≥ KKM.',
    'Hubungi wali kelas jika ada nilai yang tidak sesuai.',
];
require __DIR__ . '/../layouts/portal_header_card.php';
?>

    <?php $baseUrl = '/portal/orangtua/nilai'; require __DIR__ . '/_child_selector.php'; ?>

    <?php if (!$student): ?>
    <div class="bg-white rounded-2xl p-10 text-center text-slate-400 border border-slate-200">Akun belum terhubung ke data siswa.</div>
    <?php elseif (empty($grades)): ?>
    <div class="bg-white rounded-2xl p-10 text-center text-slate-400 border border-slate-200">Nilai belum tersedia.</div>
    <?php else: ?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Mata Pelajaran</th>
                        <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase">Tugas</th>
                        <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase">UTS</th>
                        <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase">UAS</th>
                        <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase">Akhir</th>
                        <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase">KKM</th>
                        <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($grades as $g):
                        $final = $g['final_score'] ?? $g['average'] ?? null;
                        $kkm   = $g['kkm'] ?? 70;
                        $pass  = $final !== null && $final >= $kkm;
                    ?>
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-3 font-medium text-slate-800"><?= htmlspecialchars($g['subject_name']) ?></td>
                        <td class="px-5 py-3 text-center text-slate-600"><?= $g['task_score'] ?? '-' ?></td>
                        <td class="px-5 py-3 text-center text-slate-600"><?= $g['mid_score'] ?? '-' ?></td>
                        <td class="px-5 py-3 text-center text-slate-600"><?= $g['final_exam_score'] ?? '-' ?></td>
                        <td class="px-5 py-3 text-center font-bold <?= $final !== null ? ($pass ? 'text-green-700' : 'text-red-600') : 'text-slate-400' ?>"><?= $final ?? '-' ?></td>
                        <td class="px-5 py-3 text-center text-slate-500"><?= $kkm ?></td>
                        <td class="px-5 py-3 text-center">
                            <?php if ($final !== null): ?>
                            <span class="px-2.5 py-1 rounded-full text-xs font-bold <?= $pass ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                                <?= $pass ? 'Tuntas' : 'Remedial' ?>
                            </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
