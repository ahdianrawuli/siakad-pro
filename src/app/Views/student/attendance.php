<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/student_sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">
<?php
$pageTitle    = 'Rekap Absensi';
$pageSubtitle = htmlspecialchars($student['full_name']) . ' — Kelas ' . htmlspecialchars($student['class_name'] ?? '-');
$pageBadge    = 'Bulan: ' . date('F Y', strtotime($month . '-01'));
$pageBadgeIcon = 'fa-calendar-check';
$infoItems    = [
    'Halaman ini menampilkan rekap kehadiran Anda per bulan.',
    'Gunakan filter bulan untuk melihat data absensi di bulan tertentu.',
    'H = Hadir, S = Sakit, I = Izin, A = Alpha (tanpa keterangan).',
    'Hubungi wali kelas jika ada data yang tidak sesuai.',
];
require __DIR__ . '/../layouts/portal_header_card.php';
?>

    <?php \App\Core\Session::flash(); ?>

    <!-- Filter -->
    <div class="portal-filter-bar">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <label class="text-xs font-bold text-slate-500 uppercase">Bulan</label>
            <input type="month" name="month" value="<?= htmlspecialchars($month) ?>"
                   class="border border-slate-200 bg-slate-50 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
            <button type="submit" class="bg-green-700 text-white px-5 py-2 rounded-xl text-sm font-bold hover:bg-green-800 transition">Tampilkan</button>
        </form>
    </div>

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
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 text-center">
            <div class="text-2xl font-extrabold text-<?= $c['color'] ?>-600"><?= $recap[$key] ?></div>
            <div class="text-xs text-slate-500 mt-1 flex items-center justify-center gap-1">
                <i class="fa-solid fa-<?= $c['icon'] ?> text-<?= $c['color'] ?>-400"></i>
                <?= $c['label'] ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <!-- Tabel Log -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Tanggal</th>
                    <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase">Status</th>
                    <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Keterangan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php if (empty($logs)): ?>
                <tr><td colspan="3" class="text-center py-12 text-slate-400"><i class="fa-solid fa-calendar-xmark text-3xl mb-2 block opacity-30"></i>Tidak ada data absensi bulan ini.</td></tr>
                <?php else: ?>
                <?php foreach ($logs as $l):
                    $statusMap = ['H'=>['Hadir','green'],'S'=>['Sakit','yellow'],'I'=>['Izin','blue'],'A'=>['Alpha','red']];
                    [$label, $color] = $statusMap[$l['status']] ?? [$l['status'], 'gray'];
                ?>
                <tr class="hover:bg-slate-50 transition">
                    <td class="px-5 py-3 font-medium text-slate-700"><?= date('d M Y', strtotime($l['date'])) ?></td>
                    <td class="px-5 py-3 text-center">
                        <span class="inline-block px-2.5 py-1 rounded-full text-xs font-bold bg-<?= $color ?>-100 text-<?= $color ?>-700"><?= $label ?></span>
                    </td>
                    <td class="px-5 py-3 text-slate-500"><?= htmlspecialchars($l['notes'] ?? '-') ?></td>
                </tr>
                <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
        </div>
    </div>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
