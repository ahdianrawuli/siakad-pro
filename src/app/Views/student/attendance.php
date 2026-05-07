<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/student_sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h1 class="text-xl md:text-2xl font-bold text-gray-800">Rekap Absensi</h1>
            <p class="text-sm text-gray-500"><?= htmlspecialchars($student['full_name']) ?> — Kelas <?= htmlspecialchars($student['class_name'] ?? '-') ?></p>
        </div>
        <form method="GET" class="flex items-center gap-2">
            <input type="month" name="month" value="<?= htmlspecialchars($month) ?>"
                   class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500">
            <button class="bg-blue-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-blue-700">Tampilkan</button>
        </form>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <!-- Rekap Kartu -->
    <div class="grid grid-cols-4 gap-3 mb-6">
        <?php
        $cards = [
            'H' => ['label'=>'Hadir',  'color'=>'green',  'icon'=>'circle-check'],
            'S' => ['label'=>'Sakit',  'color'=>'yellow', 'icon'=>'kit-medical'],
            'I' => ['label'=>'Izin',   'color'=>'blue',   'icon'=>'file-lines'],
            'A' => ['label'=>'Alpha',  'color'=>'red',    'icon'=>'circle-xmark'],
        ];
        foreach ($cards as $key => $c):
        ?>
        <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-4 text-center">
            <div class="text-2xl font-bold text-<?= $c['color'] ?>-600"><?= $recap[$key] ?></div>
            <div class="text-xs text-gray-500 mt-1 flex items-center justify-center gap-1">
                <i class="fa-solid fa-<?= $c['icon'] ?> text-<?= $c['color'] ?>-400"></i>
                <?= $c['label'] ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Tabel Log -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-gray-600 text-xs uppercase">
                <tr>
                    <th class="px-4 py-3 text-left">Tanggal</th>
                    <th class="px-4 py-3 text-center">Status</th>
                    <th class="px-4 py-3 text-left">Keterangan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                <?php if (empty($logs)): ?>
                <tr><td colspan="3" class="text-center py-8 text-gray-400">Tidak ada data absensi bulan ini.</td></tr>
                <?php else: ?>
                <?php foreach ($logs as $l):
                    $statusMap = ['H'=>['Hadir','green'],'S'=>['Sakit','yellow'],'I'=>['Izin','blue'],'A'=>['Alpha','red']];
                    [$label, $color] = $statusMap[$l['status']] ?? [$l['status'], 'gray'];
                ?>
                <tr class="hover:bg-gray-50">
                    <td class="px-4 py-3 font-medium text-gray-700"><?= date('d M Y', strtotime($l['date'])) ?></td>
                    <td class="px-4 py-3 text-center">
                        <span class="inline-block px-2 py-0.5 rounded-full text-xs font-bold bg-<?= $color ?>-100 text-<?= $color ?>-700"><?= $label ?></span>
                    </td>
                    <td class="px-4 py-3 text-gray-500"><?= htmlspecialchars($l['notes'] ?? '-') ?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
