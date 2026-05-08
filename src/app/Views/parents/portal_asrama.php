<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/parent_sidebar.php'; ?>
<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">
<?php
$pageTitle    = 'Asrama';
$pageSubtitle = $student ? htmlspecialchars($student['full_name']) : 'Pilih santri terlebih dahulu';
$pageBadge    = $dorm ? htmlspecialchars($dorm['name']) : null;
$pageBadgeIcon = 'fa-house';
$infoItems    = [
    'Halaman ini menampilkan informasi asrama dan riwayat perizinan santri.',
    'Status izin: Menunggu (belum diproses), Disetujui, Ditolak, Kembali.',
    'Hubungi pengurus asrama jika ada informasi yang tidak sesuai.',
];
require __DIR__ . '/../layouts/portal_header_card.php';
?>

    <?php $baseUrl = '/portal/orangtua/asrama'; require __DIR__ . '/_child_selector.php'; ?>

    <?php if (!$student): ?>
    <div class="bg-white rounded-2xl p-10 text-center text-slate-400 border border-slate-200">Akun belum terhubung ke data siswa.</div>
    <?php else: ?>

    <!-- Info Asrama -->
    <?php if ($dorm): ?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5 mb-6">
        <h2 class="font-semibold text-slate-700 mb-3 flex items-center gap-2"><i class="fa-solid fa-bed text-green-600"></i> Informasi Asrama</h2>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
            <div class="bg-slate-50 rounded-xl p-3"><span class="text-xs text-slate-400 block mb-1">Nama Asrama</span><p class="font-semibold text-slate-800"><?= htmlspecialchars($dorm['name']) ?></p></div>
            <div class="bg-slate-50 rounded-xl p-3"><span class="text-xs text-slate-400 block mb-1">Kapasitas</span><p class="font-semibold text-slate-800"><?= $dorm['capacity'] ?> orang</p></div>
            <div class="bg-slate-50 rounded-xl p-3"><span class="text-xs text-slate-400 block mb-1">Jenis</span><p class="font-semibold text-slate-800"><?= $dorm['gender'] === 'L' ? 'Putra' : 'Putri' ?></p></div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Riwayat Izin -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-200 font-semibold text-slate-700 flex items-center gap-2">
            <i class="fa-solid fa-door-open text-blue-500"></i> Riwayat Perizinan
        </div>
        <?php if (empty($permits)): ?>
        <p class="text-center text-slate-400 py-8">Tidak ada riwayat izin.</p>
        <?php else: ?>
        <div class="overflow-x-auto">
        <table class="min-w-full text-sm">
            <thead class="bg-slate-50 border-b border-slate-200">
                <tr>
                    <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Jenis</th>
                    <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Alasan</th>
                    <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Mulai</th>
                    <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Selesai</th>
                    <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <?php foreach ($permits as $p):
                    $statusMap = ['PENDING'=>['Menunggu','yellow'],'APPROVED'=>['Disetujui','green'],'REJECTED'=>['Ditolak','red'],'RETURNED'=>['Kembali','blue']];
                    [$slbl,$scol] = $statusMap[$p['status']] ?? [$p['status'],'gray'];
                    $typeMap = ['KELUAR'=>'Keluar','PULANG'=>'Pulang','SAKIT'=>'Sakit'];
                ?>
                <tr class="hover:bg-slate-50">
                    <td class="px-5 py-3 text-slate-700"><?= $typeMap[$p['type']] ?? $p['type'] ?></td>
                    <td class="px-5 py-3 text-slate-600 max-w-xs truncate"><?= htmlspecialchars($p['reason']) ?></td>
                    <td class="px-5 py-3 text-slate-500 text-xs"><?= date('d M Y', strtotime($p['start_date'])) ?></td>
                    <td class="px-5 py-3 text-slate-500 text-xs"><?= date('d M Y', strtotime($p['end_date'])) ?></td>
                    <td class="px-5 py-3 text-center">
                        <span class="px-2.5 py-1 rounded-full text-xs font-bold bg-<?= $scol ?>-100 text-<?= $scol ?>-700"><?= $slbl ?></span>
                    </td>
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
