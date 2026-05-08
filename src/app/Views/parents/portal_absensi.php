<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/parent_sidebar.php'; ?>
<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">
<?php
$pageTitle    = 'Absensi Santri';
$pageSubtitle = $student ? htmlspecialchars($student['full_name']) . ' — Kelas ' . htmlspecialchars($student['class_name'] ?? '-') : 'Pilih santri terlebih dahulu';
$pageBadge    = 'Bulan: ' . date('F Y', strtotime($month . '-01'));
$pageBadgeIcon = 'fa-clipboard-check';
$infoItems    = [
    'Halaman ini menampilkan rekap kehadiran santri per bulan.',
    'Gunakan filter bulan untuk melihat data di bulan tertentu.',
    'H = Hadir, S = Sakit, I = Izin, A = Alpha.',
    'Hubungi wali kelas jika ada data yang tidak sesuai.',
];
require __DIR__ . '/../layouts/portal_header_card.php';
?>

    <!-- Filter -->
    <div class="portal-filter-bar">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <?php if ($student): ?><input type="hidden" name="student_id" value="<?= $student['id'] ?>"><?php endif; ?>
            <label class="text-xs font-bold text-slate-500 uppercase">Bulan</label>
            <input type="month" name="month" value="<?= htmlspecialchars($month) ?>"
                   class="border border-slate-200 bg-slate-50 rounded-xl px-3 py-2 text-sm focus:ring-2 focus:ring-green-500 outline-none">
            <button class="bg-green-700 text-white px-5 py-2 rounded-xl text-sm font-bold hover:bg-green-800 transition">Tampilkan</button>
        </form>
    </div>

    <?php $baseUrl = '/portal/orangtua/absensi'; require __DIR__ . '/_child_selector.php'; ?>

    <?php if (!$student): ?>
    <div class="bg-white rounded-2xl p-10 text-center text-slate-400 border border-slate-200">Akun belum terhubung ke data siswa.</div>
    <?php else: ?>

    <div class="grid grid-cols-4 gap-3 mb-6">
        <?php foreach (['H'=>['Hadir','green'],'S'=>['Sakit','yellow'],'I'=>['Izin','blue'],'A'=>['Alpha','red']] as $k=>[$lbl,$col]): ?>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-4 text-center">
            <div class="text-3xl font-extrabold text-<?= $col ?>-600"><?= $recap[$k] ?></div>
            <div class="text-xs text-slate-500 mt-1"><?= $lbl ?></div>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-200 text-sm font-semibold text-slate-600">
            Riwayat — <?= date('F Y', strtotime($month . '-01')) ?>
        </div>
        <?php if (empty($attendance)): ?>
        <p class="text-center text-slate-400 py-10">Tidak ada data absensi bulan ini.</p>
        <?php else: ?>
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
                <?php foreach ($attendance as $a):
                    $map = ['H'=>['Hadir','green'],'S'=>['Sakit','yellow'],'I'=>['Izin','blue'],'A'=>['Alpha','red']];
                    [$lbl,$col] = $map[$a['status']] ?? [$a['status'],'gray'];
                ?>
                <tr class="hover:bg-slate-50">
                    <td class="px-5 py-3 text-slate-700"><?= date('d M Y', strtotime($a['date'])) ?></td>
                    <td class="px-5 py-3 text-center">
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-<?= $col ?>-100 text-<?= $col ?>-700"><?= $lbl ?></span>
                    </td>
                    <td class="px-5 py-3 text-slate-500"><?= htmlspecialchars($a['notes'] ?? '-') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
