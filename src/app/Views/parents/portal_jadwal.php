<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/parent_sidebar.php'; ?>
<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">
    <div class="mb-5">
        <h1 class="text-xl font-bold text-gray-800"><i class="fa-solid fa-calendar-days text-indigo-500 mr-2"></i>Jadwal Pelajaran</h1>
        <?php if ($student): ?><p class="text-sm text-gray-500">Kelas: <?= htmlspecialchars($student['class_name'] ?? '-') ?></p><?php endif; ?>
    </div>

    <?php $baseUrl = '/portal/orangtua/jadwal'; require __DIR__ . '/_child_selector.php'; ?>

    <?php if (!$student): ?>
    <div class="bg-white rounded-xl p-10 text-center text-gray-400">Akun belum terhubung ke data siswa.</div>
    <?php else: ?>
    <div class="space-y-4">
        <?php foreach ($days as $day):
            if (empty($grouped[$day])) continue;
        ?>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-3 bg-indigo-50 border-b border-indigo-100 font-semibold text-indigo-700"><?= $day ?></div>
            <table class="w-full text-sm">
                <tbody class="divide-y divide-gray-50">
                    <?php foreach ($grouped[$day] as $sc): ?>
                    <tr>
                        <td class="px-4 py-2 text-gray-500 w-32"><?= substr($sc['start_time'],0,5) ?> – <?= substr($sc['end_time'],0,5) ?></td>
                        <td class="px-4 py-2 font-medium text-gray-800"><?= htmlspecialchars($sc['subject_name']) ?></td>
                        <td class="px-4 py-2 text-gray-500"><?= htmlspecialchars($sc['teacher_name'] ?? '-') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endforeach; ?>
        <?php if (empty(array_filter($grouped))): ?>
        <div class="bg-white rounded-xl p-10 text-center text-gray-400">Jadwal belum tersedia.</div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
