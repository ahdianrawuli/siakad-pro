<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jadwal Kegiatan Asrama</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>@media print{.no-print{display:none}body{-webkit-print-color-adjust:exact}}</style>
</head>
<body class="bg-white p-8 text-sm text-gray-800">

    <div class="text-center mb-6 border-b-2 border-gray-800 pb-4">
        <h1 class="text-xl font-bold uppercase">Jadwal Kegiatan Asrama</h1>
        <?php if ($day): ?><p class="text-gray-600 font-semibold">Hari: <?= htmlspecialchars($day) ?></p><?php endif; ?>
        <p class="text-gray-400 text-xs mt-1">Dicetak: <?= date('d F Y, H:i') ?></p>
    </div>

    <div class="no-print mb-4 flex gap-2">
        <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded font-bold text-sm">Cetak</button>
        <a href="javascript:history.back()" class="bg-gray-200 text-gray-700 px-4 py-2 rounded font-bold text-sm">Kembali</a>
    </div>

    <?php
    $grouped = [];
    foreach ($activities as $a) { $grouped[$a['day']][] = $a; }
    ?>

    <?php foreach ($grouped as $dayName => $rows): ?>
    <div class="mb-6">
        <?php if (!$day): ?><h3 class="font-bold text-base mb-2 bg-gray-100 px-3 py-1.5 rounded"><?= htmlspecialchars($dayName) ?></h3><?php endif; ?>
        <table class="w-full border-collapse border border-gray-300 text-left">
            <thead>
                <tr class="bg-gray-100 text-xs uppercase">
                    <th class="border border-gray-300 px-3 py-2 w-8 text-center">No</th>
                    <th class="border border-gray-300 px-3 py-2 w-28">Waktu</th>
                    <th class="border border-gray-300 px-3 py-2">Nama Kegiatan</th>
                    <th class="border border-gray-300 px-3 py-2">Keterangan</th>
                </tr>
            </thead>
            <tbody>
                <?php $no=1; foreach ($rows as $r): ?>
                <tr class="<?= $no%2===0?'bg-gray-50':'' ?>">
                    <td class="border border-gray-300 px-3 py-2 text-center"><?= $no++ ?></td>
                    <td class="border border-gray-300 px-3 py-2 font-mono text-xs"><?= substr($r['start_time'],0,5) ?> – <?= substr($r['end_time'],0,5) ?></td>
                    <td class="border border-gray-300 px-3 py-2 font-bold"><?= htmlspecialchars($r['name']) ?></td>
                    <td class="border border-gray-300 px-3 py-2 text-gray-500"><?= htmlspecialchars($r['description'] ?? '-') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endforeach; ?>

    <?php if (empty($activities)): ?>
        <p class="text-center text-gray-400 py-8">Tidak ada data kegiatan.</p>
    <?php endif; ?>

</body>
</html>
