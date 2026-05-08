<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/student_sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">
<?php
$pageTitle    = 'Nilai Akademik';
$pageSubtitle = htmlspecialchars($student['full_name']) . ' — Kelas ' . htmlspecialchars($student['class_name'] ?? '-') . ($activeYear ? ' | TA ' . htmlspecialchars($activeYear['name']) : '');
$pageBadge    = 'Total Mapel: ' . count($grades);
$pageBadgeIcon = 'fa-star';
$infoItems    = [
    'Halaman ini menampilkan rekap nilai akademik Anda per mata pelajaran.',
    'Kolom Nilai Akhir dihitung dari rata-rata Tugas, UTS, dan UAS.',
    'Status "Tuntas" berarti nilai akhir ≥ KKM, "Remedial" berarti di bawah KKM.',
    'Hubungi wali kelas jika ada nilai yang tidak sesuai.',
];
require __DIR__ . '/../layouts/portal_header_card.php';
?>

    <?php \App\Core\Session::flash(); ?>

    <!-- Filter -->
    <?php if (!empty($years ?? [])): ?>
    <div class="portal-filter-bar">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <label class="text-xs font-bold text-slate-500 uppercase">Tahun Ajaran</label>
            <select name="year_id" class="select2-portal py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none min-w-[180px]">
                <?php foreach ($years as $y): ?>
                <option value="<?= $y['id'] ?>" <?= ($y['id'] == ($selectedYear ?? '')) ? 'selected' : '' ?>><?= htmlspecialchars($y['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="bg-green-700 text-white px-5 py-2 rounded-xl text-sm font-bold hover:bg-green-800 transition">Tampilkan</button>
        </form>
    </div>
    <?php endif; ?>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Mata Pelajaran</th>
                    <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase">Tugas</th>
                    <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase">UTS</th>
                    <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase">UAS</th>
                    <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase">Nilai Akhir</th>
                    <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase">KKM</th>
                    <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($grades)): ?>
                <tr><td colspan="7" class="text-center py-12 text-slate-400"><i class="fa-solid fa-star text-3xl mb-2 block opacity-30"></i>Nilai belum tersedia.</td></tr>
                <?php else: ?>
                <?php foreach ($grades as $g):
                    $final = $g['final_score'] ?? $g['average'] ?? null;
                    $kkm   = $g['kkm'] ?? 70;
                    $pass  = $final !== null && $final >= $kkm;
                ?>
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-5 py-3 font-semibold text-slate-800"><?= htmlspecialchars($g['subject_name']) ?></td>
                    <td class="px-5 py-3 text-center text-slate-600"><?= $g['task_score'] ?? '-' ?></td>
                    <td class="px-5 py-3 text-center text-slate-600"><?= $g['mid_score'] ?? '-' ?></td>
                    <td class="px-5 py-3 text-center text-slate-600"><?= $g['final_exam_score'] ?? '-' ?></td>
                    <td class="px-5 py-3 text-center font-bold text-lg <?= $final !== null ? ($pass ? 'text-green-700' : 'text-red-600') : 'text-slate-400' ?>">
                        <?= $final ?? '-' ?>
                    </td>
                    <td class="px-5 py-3 text-center text-slate-500"><?= $kkm ?></td>
                    <td class="px-5 py-3 text-center">
                        <?php if ($final !== null): ?>
                        <span class="inline-block px-2.5 py-1 rounded-full text-xs font-bold <?= $pass ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                            <?= $pass ? 'Tuntas' : 'Remedial' ?>
                        </span>
                        <?php else: ?><span class="text-slate-400 text-xs">—</span><?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
</main>

<script>$(function(){ $('.select2-portal').select2({ width: 'resolve' }); });</script>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
