<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/parent_sidebar.php'; ?>
<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">
    <div class="mb-5 flex items-center justify-between">
        <h1 class="text-xl font-bold text-gray-800"><i class="fa-solid fa-clipboard-check text-blue-500 mr-2"></i>Absensi</h1>
        <form method="GET" class="flex gap-2">
            <?php if ($student): ?><input type="hidden" name="student_id" value="<?= $student['id'] ?>"><?php endif; ?>
            <input type="month" name="month" value="<?= htmlspecialchars($month) ?>"
                   class="border border-gray-300 rounded-lg px-3 py-1.5 text-sm focus:ring-teal-500 focus:border-teal-500">
            <button class="bg-teal-600 text-white px-3 py-1.5 rounded-lg text-sm">Tampilkan</button>
        </form>
    </div>

    <?php $baseUrl = '/portal/orangtua/absensi'; require __DIR__ . '/_child_selector.php'; ?>

    <?php if (!$student): ?>
    <div class="bg-white rounded-xl p-10 text-center text-gray-400">Akun belum terhubung ke data siswa.</div>
    <?php else: ?>

    <div class="grid grid-cols-4 gap-3 mb-6">
        <?php foreach (['H'=>['Hadir','green'],'S'=>['Sakit','yellow'],'I'=>['Izin','blue'],'A'=>['Alpha','red']] as $k=>[$lbl,$col]): ?>
        <div class="bg-white rounded-xl border p-4 text-center">
            <div class="text-3xl font-bold text-<?= $col ?>-600"><?= $recap[$k] ?></div>
            <div class="text-sm text-gray-500 mt-1"><?= $lbl ?></div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 text-sm font-semibold text-gray-600">
            Riwayat — <?= date('F Y', strtotime($month . '-01')) ?>
        </div>
        <?php if (empty($attendance)): ?>
        <p class="text-center text-gray-400 py-10">Tidak ada data absensi bulan ini.</p>
        <?php else: ?>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                <tr>
                    <th class="px-4 py-2 text-left">Tanggal</th>
                    <th class="px-4 py-2 text-center">Status</th>
                    <th class="px-4 py-2 text-left">Keterangan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php foreach ($attendance as $a):
                    $map = ['H'=>['Hadir','green'],'S'=>['Sakit','yellow'],'I'=>['Izin','blue'],'A'=>['Alpha','red']];
                    [$lbl,$col] = $map[$a['status']] ?? [$a['status'],'gray'];
                ?>
                <tr>
                    <td class="px-4 py-2 text-gray-700"><?= date('d M Y', strtotime($a['date'])) ?></td>
                    <td class="px-4 py-2 text-center">
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-<?= $col ?>-100 text-<?= $col ?>-700"><?= $lbl ?></span>
                    </td>
                    <td class="px-4 py-2 text-gray-500"><?= htmlspecialchars($a['notes'] ?? '-') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
