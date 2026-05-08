<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/parent_sidebar.php'; ?>
<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">
    <div class="mb-5">
        <h1 class="text-xl font-bold text-gray-800"><i class="fa-solid fa-house text-green-600 mr-2"></i>Asrama</h1>
    </div>

    <?php $baseUrl = '/portal/orangtua/asrama'; require __DIR__ . '/_child_selector.php'; ?>

    <?php if (!$student): ?>
    <div class="bg-white rounded-xl p-10 text-center text-gray-400">Akun belum terhubung ke data siswa.</div>
    <?php else: ?>

    <!-- Info Asrama -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5 mb-6">
        <h2 class="font-semibold text-gray-700 mb-3">Informasi Asrama</h2>
        <?php if ($dorm): ?>
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4 text-sm">
            <div><span class="text-gray-500">Nama Asrama</span><p class="font-medium text-gray-800"><?= htmlspecialchars($dorm['name']) ?></p></div>
            <div><span class="text-gray-500">Kapasitas</span><p class="font-medium text-gray-800"><?= $dorm['capacity'] ?> orang</p></div>
            <div><span class="text-gray-500">Jenis</span><p class="font-medium text-gray-800"><?= $dorm['gender'] === 'L' ? 'Putra' : 'Putri' ?></p></div>
        </div>
        <?php else: ?>
        <p class="text-gray-400 text-sm">Belum ditempatkan di asrama.</p>
        <?php endif; ?>
    </div>

    <!-- Riwayat Izin -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-3 border-b border-gray-100 font-semibold text-gray-700">Riwayat Perizinan</div>
        <?php if (empty($permits)): ?>
        <p class="text-center text-gray-400 py-8">Tidak ada riwayat izin.</p>
        <?php else: ?>
        <table class="w-full text-sm">
            <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                <tr>
                    <th class="px-4 py-2 text-left">Jenis</th>
                    <th class="px-4 py-2 text-left">Alasan</th>
                    <th class="px-4 py-2 text-left">Mulai</th>
                    <th class="px-4 py-2 text-left">Selesai</th>
                    <th class="px-4 py-2 text-center">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                <?php foreach ($permits as $p):
                    $statusMap = ['PENDING'=>['Menunggu','yellow'],'APPROVED'=>['Disetujui','green'],'REJECTED'=>['Ditolak','red'],'RETURNED'=>['Kembali','blue']];
                    [$slbl,$scol] = $statusMap[$p['status']] ?? [$p['status'],'gray'];
                    $typeMap = ['KELUAR'=>'Keluar','PULANG'=>'Pulang','SAKIT'=>'Sakit'];
                ?>
                <tr>
                    <td class="px-4 py-2 text-gray-700"><?= $typeMap[$p['type']] ?? $p['type'] ?></td>
                    <td class="px-4 py-2 text-gray-600 max-w-xs truncate"><?= htmlspecialchars($p['reason']) ?></td>
                    <td class="px-4 py-2 text-gray-500 text-xs"><?= date('d M Y', strtotime($p['start_date'])) ?></td>
                    <td class="px-4 py-2 text-gray-500 text-xs"><?= date('d M Y', strtotime($p['end_date'])) ?></td>
                    <td class="px-4 py-2 text-center">
                        <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-<?= $scol ?>-100 text-<?= $scol ?>-700"><?= $slbl ?></span>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
