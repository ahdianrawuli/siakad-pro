<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">
    <div class="mb-6 flex items-center gap-3">
        <a href="/portal/orangtua" class="text-gray-400 hover:text-gray-700">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-gray-800"><?= htmlspecialchars($student['full_name']) ?></h1>
            <p class="text-sm text-gray-500">NIS: <?= $student['nis'] ?> | Kelas: <?= htmlspecialchars($student['class_name'] ?? '-') ?></p>
        </div>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        <!-- ABSENSI -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
                <h2 class="font-bold text-gray-800"><i class="fa-solid fa-calendar-check text-blue-500 mr-2"></i>Absensi Bulan Ini</h2>
            </div>
            <div class="p-5">
                <div class="grid grid-cols-4 gap-2 mb-4">
                    <?php
                    $cards = ['H'=>['Hadir','green'],'S'=>['Sakit','yellow'],'I'=>['Izin','blue'],'A'=>['Alpha','red']];
                    foreach ($cards as $k => [$lbl, $col]):
                    ?>
                    <div class="text-center bg-<?= $col ?>-50 rounded-lg p-2">
                        <div class="text-xl font-bold text-<?= $col ?>-600"><?= $recap[$k] ?></div>
                        <div class="text-xs text-<?= $col ?>-500"><?= $lbl ?></div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php if (!empty($attendance)): ?>
                <div class="space-y-1 max-h-40 overflow-y-auto">
                    <?php foreach (array_slice($attendance, 0, 10) as $a):
                        $statusMap = ['H'=>['Hadir','green'],'S'=>['Sakit','yellow'],'I'=>['Izin','blue'],'A'=>['Alpha','red']];
                        [$lbl, $col] = $statusMap[$a['status']] ?? [$a['status'], 'gray'];
                    ?>
                    <div class="flex items-center justify-between text-sm py-1 border-b border-gray-50">
                        <span class="text-gray-600"><?= date('d M', strtotime($a['date'])) ?></span>
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-<?= $col ?>-100 text-<?= $col ?>-700"><?= $lbl ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php else: ?>
                <p class="text-sm text-gray-400 text-center py-4">Belum ada data absensi.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- TAGIHAN -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800"><i class="fa-solid fa-file-invoice-dollar text-orange-500 mr-2"></i>Tagihan</h2>
            </div>
            <div class="divide-y divide-gray-50">
                <?php if (empty($bills)): ?>
                <p class="text-sm text-gray-400 text-center py-8">Tidak ada tagihan.</p>
                <?php else: ?>
                <?php foreach ($bills as $b): ?>
                <div class="px-5 py-3 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-800"><?= htmlspecialchars($b['title']) ?></p>
                        <p class="text-xs text-gray-400">Rp <?= number_format($b['amount'], 0, ',', '.') ?></p>
                    </div>
                    <span class="px-2 py-0.5 rounded-full text-xs font-bold <?= $b['status'] === 'PAID' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                        <?= $b['status'] === 'PAID' ? 'Lunas' : 'Belum Bayar' ?>
                    </span>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- NILAI -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden lg:col-span-2">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800">
                    <i class="fa-solid fa-star text-yellow-500 mr-2"></i>Nilai Akademik
                    <?php if ($activeYear): ?><span class="text-sm font-normal text-gray-400">— TA <?= htmlspecialchars($activeYear['name']) ?></span><?php endif; ?>
                </h2>
            </div>
            <?php if (empty($grades)): ?>
            <p class="text-sm text-gray-400 text-center py-8">Nilai belum tersedia.</p>
            <?php else: ?>
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                        <tr>
                            <th class="px-4 py-2 text-left">Mata Pelajaran</th>
                            <th class="px-4 py-2 text-center">Tugas</th>
                            <th class="px-4 py-2 text-center">UTS</th>
                            <th class="px-4 py-2 text-center">UAS</th>
                            <th class="px-4 py-2 text-center">Akhir</th>
                            <th class="px-4 py-2 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50">
                        <?php foreach ($grades as $g):
                            $final = $g['final_score'] ?? $g['average'] ?? null;
                            $pass  = $final !== null && $final >= ($g['kkm'] ?? 70);
                        ?>
                        <tr>
                            <td class="px-4 py-2 font-medium text-gray-800"><?= htmlspecialchars($g['subject_name']) ?></td>
                            <td class="px-4 py-2 text-center text-gray-600"><?= $g['task_score'] ?? '-' ?></td>
                            <td class="px-4 py-2 text-center text-gray-600"><?= $g['mid_score'] ?? '-' ?></td>
                            <td class="px-4 py-2 text-center text-gray-600"><?= $g['final_exam_score'] ?? '-' ?></td>
                            <td class="px-4 py-2 text-center font-bold <?= $final !== null ? ($pass ? 'text-green-700' : 'text-red-600') : 'text-gray-400' ?>"><?= $final ?? '-' ?></td>
                            <td class="px-4 py-2 text-center">
                                <?php if ($final !== null): ?>
                                <span class="px-2 py-0.5 rounded-full text-xs font-bold <?= $pass ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' ?>">
                                    <?= $pass ? 'Tuntas' : 'Remedial' ?>
                                </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>
        </div>

        <!-- PELANGGARAN -->
        <?php if (!empty($violations)): ?>
        <div class="bg-white rounded-xl shadow-sm border border-red-100 overflow-hidden lg:col-span-2">
            <div class="px-5 py-4 border-b border-red-100">
                <h2 class="font-bold text-red-700"><i class="fa-solid fa-triangle-exclamation mr-2"></i>Catatan Pelanggaran Terbaru</h2>
            </div>
            <div class="divide-y divide-gray-50">
                <?php foreach ($violations as $v): ?>
                <div class="px-5 py-3 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-800"><?= htmlspecialchars($v['violation_name']) ?></p>
                        <p class="text-xs text-gray-400"><?= date('d M Y', strtotime($v['date'])) ?></p>
                    </div>
                    <span class="text-xs font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded-full">-<?= $v['points'] ?> poin</span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

    </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
