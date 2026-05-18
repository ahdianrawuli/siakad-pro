<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap <?= htmlspecialchars($type['name']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>@media print{.no-print{display:none}body{-webkit-print-color-adjust:exact}}</style>
</head>
<body class="bg-white p-8 text-sm text-gray-800">

    <div class="text-center mb-6 border-b-2 border-gray-800 pb-4">
        <h1 class="text-xl font-bold uppercase">Rekap Absensi: <?= htmlspecialchars($type['name']) ?></h1>
        <?php if ($classroom): ?><h2 class="text-base font-semibold">Kelas <?= htmlspecialchars($classroom['name']) ?></h2><?php endif; ?>
        <p class="text-gray-500 text-xs mt-1">Periode: <?= date('d F Y', strtotime($dateFrom)) ?> s/d <?= date('d F Y', strtotime($dateTo)) ?> | Dicetak: <?= date('d F Y, H:i') ?></p>
    </div>

    <div class="no-print mb-4 flex gap-2">
        <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded font-bold text-sm">Cetak</button>
        <a href="javascript:history.back()" class="bg-gray-200 text-gray-700 px-4 py-2 rounded font-bold text-sm">Kembali</a>
    </div>

    <table class="w-full border-collapse border border-gray-300 text-left">
        <thead>
            <tr class="bg-gray-100 text-xs uppercase">
                <th class="border border-gray-300 px-3 py-2 w-8 text-center">No</th>
                <th class="border border-gray-300 px-3 py-2">Nama</th>
                <?php if ($type['target'] !== 'GURU'): ?><th class="border border-gray-300 px-3 py-2">Kelas</th><?php endif; ?>
                <th class="border border-gray-300 px-3 py-2">Tanggal</th>
                <th class="border border-gray-300 px-3 py-2 text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($report)): ?>
                <tr><td colspan="5" class="border border-gray-300 px-3 py-4 text-center text-gray-400">Tidak ada data.</td></tr>
            <?php endif; ?>
            <?php $no=1; foreach ($report as $r): ?>
            <tr class="<?= $no%2===0?'bg-gray-50':'' ?>">
                <td class="border border-gray-300 px-3 py-2 text-center"><?= $no++ ?></td>
                <td class="border border-gray-300 px-3 py-2 font-bold"><?= htmlspecialchars($r['name']) ?></td>
                <?php if ($type['target'] !== 'GURU'): ?><td class="border border-gray-300 px-3 py-2 text-xs"><?= htmlspecialchars($r['class_name'] ?? '-') ?></td><?php endif; ?>
                <td class="border border-gray-300 px-3 py-2 font-mono text-xs"><?= date('d/m/Y', strtotime($r['date'])) ?></td>
                <td class="border border-gray-300 px-3 py-2 text-center font-bold"><?= $r['status'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

</body>
</html>
