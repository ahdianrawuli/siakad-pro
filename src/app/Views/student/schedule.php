<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/student_sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">
    <div class="mb-6">
        <h1 class="text-xl md:text-2xl font-bold text-gray-800">Jadwal Pelajaran</h1>
        <p class="text-sm text-gray-500">Kelas <span class="font-semibold text-blue-700"><?= htmlspecialchars($student['class_name'] ?? '-') ?></span></p>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <?php
    $days = ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    $dayColors = ['Senin'=>'blue','Selasa'=>'green','Rabu'=>'purple','Kamis'=>'orange','Jumat'=>'red','Sabtu'=>'gray'];
    ?>

    <?php if (empty($byDay)): ?>
        <div class="bg-white rounded-xl border border-gray-200 p-10 text-center text-gray-400">
            <i class="fa-solid fa-calendar-xmark text-4xl mb-3"></i>
            <p class="font-medium">Jadwal belum tersedia untuk kelas Anda.</p>
        </div>
    <?php else: ?>
        <div class="space-y-4">
            <?php foreach ($days as $day): ?>
                <?php if (empty($byDay[$day])) continue; ?>
                <?php $color = $dayColors[$day] ?? 'gray'; ?>
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="bg-<?= $color ?>-600 px-4 py-2">
                        <h3 class="font-bold text-white text-sm"><?= $day ?></h3>
                    </div>
                    <div class="divide-y divide-gray-100">
                        <?php foreach ($byDay[$day] as $s): ?>
                        <div class="flex items-center px-4 py-3 gap-4">
                            <div class="text-xs font-mono text-gray-500 w-24 shrink-0">
                                <?= substr($s['start_time'],0,5) ?> – <?= substr($s['end_time'],0,5) ?>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-gray-800 text-sm"><?= htmlspecialchars($s['subject_name']) ?></p>
                                <p class="text-xs text-gray-500"><?= htmlspecialchars($s['teacher_name'] ?? '-') ?></p>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
