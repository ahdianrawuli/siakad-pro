<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/student_sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">
    <h1 class="text-xl md:text-2xl font-bold text-gray-800 mb-2">Ekstrakurikuler</h1>
    <p class="text-sm text-gray-500 mb-6"><?= htmlspecialchars($student['full_name']) ?> — <?= htmlspecialchars($student['class_name'] ?? '-') ?></p>

    <?php \App\Core\Session::flash(); ?>

    <!-- Ekskul yang diikuti -->
    <h2 class="font-bold text-gray-700 mb-3">Ekskul Saya</h2>
    <?php if (empty($myEkskul)): ?>
        <div class="bg-white rounded-xl border border-gray-100 p-8 text-center text-gray-400 mb-6">
            <i class="fa-solid fa-person-running text-3xl mb-2"></i>
            <p class="text-sm">Belum terdaftar di ekskul manapun.</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
        <?php foreach ($myEkskul as $e): ?>
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
                <h3 class="font-bold text-gray-800"><?= htmlspecialchars($e['name']) ?></h3>
                <?php if ($e['description']): ?><p class="text-xs text-gray-500 mt-1"><?= htmlspecialchars($e['description']) ?></p><?php endif; ?>
                <div class="mt-3 flex flex-wrap gap-3 text-xs text-gray-600">
                    <?php if ($e['schedule_day']): ?><span><i class="fa-solid fa-calendar mr-1"></i><?= htmlspecialchars($e['schedule_day']) ?> <?= htmlspecialchars($e['schedule_time'] ?? '') ?></span><?php endif; ?>
                    <?php if ($e['location']): ?><span><i class="fa-solid fa-location-dot mr-1"></i><?= htmlspecialchars($e['location']) ?></span><?php endif; ?>
                    <?php if ($e['coach_name']): ?><span><i class="fa-solid fa-user mr-1"></i><?= htmlspecialchars($e['coach_name']) ?></span><?php endif; ?>
                </div>
                <span class="mt-3 inline-block text-xs font-bold px-2 py-0.5 rounded-full <?= $e['status'] === 'ACTIVE' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' ?>"><?= $e['status'] ?></span>
            </div>
        <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <!-- Riwayat Kehadiran Ekskul -->
    <h2 class="font-bold text-gray-700 mb-3">Riwayat Kehadiran</h2>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">Tanggal</th>
                    <th class="px-4 py-3 text-left">Ekskul</th>
                    <th class="px-4 py-3 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($attendance)): ?>
                <tr><td colspan="3" class="text-center py-8 text-gray-400">Belum ada data kehadiran.</td></tr>
                <?php else: ?>
                <?php foreach ($attendance as $a): ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 text-gray-600"><?= date('d M Y', strtotime($a['date'])) ?></td>
                    <td class="px-4 py-3 font-medium text-gray-800"><?= htmlspecialchars($a['ekskul_name']) ?></td>
                    <td class="px-4 py-3 text-center">
                        <?php
                        $statusMap = ['H'=>['Hadir','green'],'A'=>['Alpa','red'],'I'=>['Izin','yellow'],'S'=>['Sakit','blue']];
                        [$label,$color] = $statusMap[$a['status']] ?? [$a['status'],'gray'];
                        ?>
                        <span class="bg-<?= $color ?>-100 text-<?= $color ?>-700 text-xs font-bold px-2 py-0.5 rounded-full"><?= $label ?></span>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
