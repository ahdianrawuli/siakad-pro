<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Pelanggaran Asrama</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>@media print { .no-print{display:none} body{-webkit-print-color-adjust:exact} }</style>
</head>
<body class="bg-white p-8 text-sm text-gray-800">

    <div class="text-center mb-6 border-b-2 border-gray-800 pb-4">
        <h1 class="text-xl font-bold uppercase">Rekap Pelanggaran Santri Asrama</h1>
        <?php if ($dorm): ?><h2 class="text-base font-semibold"><?= htmlspecialchars($dorm['name']) ?></h2><?php endif; ?>
        <p class="text-gray-500 text-xs mt-1">
            Periode: <?= date('d F Y', strtotime($from)) ?> s/d <?= date('d F Y', strtotime($to)) ?>
            &nbsp;|&nbsp; Dicetak: <?= date('d F Y, H:i') ?>
        </p>
    </div>

    <div class="no-print mb-4 flex gap-2">
        <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded font-bold text-sm">Cetak</button>
        <a href="javascript:history.back()" class="bg-gray-200 text-gray-700 px-4 py-2 rounded font-bold text-sm">Kembali</a>
    </div>

    <table class="w-full border-collapse border border-gray-300 text-left">
        <thead>
            <tr class="bg-gray-100 text-xs uppercase">
                <th class="border border-gray-300 px-3 py-2 w-8 text-center">No</th>
                <th class="border border-gray-300 px-3 py-2">Nama Santri</th>
                <th class="border border-gray-300 px-3 py-2">NIS</th>
                <th class="border border-gray-300 px-3 py-2">Kamar</th>
                <th class="border border-gray-300 px-3 py-2 text-center">Jml Pelanggaran</th>
                <th class="border border-gray-300 px-3 py-2 text-center">Total Poin</th>
                <th class="border border-gray-300 px-3 py-2 text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($students)): ?>
                <tr><td colspan="7" class="border border-gray-300 px-3 py-4 text-center text-gray-400">Tidak ada data.</td></tr>
            <?php endif; ?>
            <?php $no=1; foreach ($students as $s):
                $p = $s['total_points'];
                $status = $p >= 100 ? 'Kritis' : ($p >= 50 ? 'Perhatian' : ($p > 0 ? 'Ringan' : 'Baik'));
            ?>
            <tr class="<?= $no%2===0?'bg-gray-50':'' ?>">
                <td class="border border-gray-300 px-3 py-2 text-center"><?= $no++ ?></td>
                <td class="border border-gray-300 px-3 py-2 font-bold"><?= htmlspecialchars($s['full_name']) ?></td>
                <td class="border border-gray-300 px-3 py-2 font-mono text-xs"><?= $s['nis'] ?></td>
                <td class="border border-gray-300 px-3 py-2 text-xs"><?= htmlspecialchars($s['dorm_name'] ?? '-') ?></td>
                <td class="border border-gray-300 px-3 py-2 text-center <?= $s['total_violations']>0?'font-bold text-red-600':'' ?>"><?= $s['total_violations'] ?: '-' ?></td>
                <td class="border border-gray-300 px-3 py-2 text-center font-bold"><?= $p ?: '-' ?></td>
                <td class="border border-gray-300 px-3 py-2 text-center text-xs font-bold <?= $p>=100?'text-red-600':($p>=50?'text-yellow-600':($p>0?'text-orange-600':'text-green-600')) ?>"><?= $status ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="mt-10 flex justify-end no-print">
        <div class="text-center">
            <p class="mb-16">Mengetahui, Pengasuh Asrama</p>
            <p class="font-bold border-b border-black inline-block px-8">___________________</p>
        </div>
    </div>

</body>
</html>
