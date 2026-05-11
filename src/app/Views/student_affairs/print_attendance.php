<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Absensi Siswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>@media print{.no-print{display:none}body{-webkit-print-color-adjust:exact}}</style>
</head>
<body class="bg-white p-8 text-sm text-gray-800">

    <div class="text-center mb-6 border-b-2 border-gray-800 pb-4">
        <h1 class="text-xl font-bold uppercase">Rekap Absensi Siswa</h1>
        <?php if ($classroom): ?><h2 class="text-base font-semibold">Kelas <?= htmlspecialchars($classroom['name']) ?></h2><?php endif; ?>
        <p class="text-gray-500 text-xs mt-1">
            Periode: <?= date('d F Y', strtotime($dateFrom)) ?> s/d <?= date('d F Y', strtotime($dateTo)) ?>
            &nbsp;|&nbsp; Dicetak: <?= date('d F Y, H:i') ?>
        </p>
    </div>

    <div class="no-print mb-4 flex gap-2">
        <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded font-bold text-sm">Cetak</button>
        <a href="javascript:history.back()" class="bg-gray-200 text-gray-700 px-4 py-2 rounded font-bold text-sm">Kembali</a>
    </div>

    <?php
    // Kelompokkan per kelas
    $grouped = [];
    foreach ($logs as $l) {
        $grouped[$l['class_name'] ?? '-'][] = $l;
    }
    $statusLabel = ['H'=>'Hadir','S'=>'Sakit','I'=>'Izin','A'=>'Alpa'];
    ?>

    <?php foreach ($grouped as $className => $rows): ?>
    <?php if (!$classroom): ?><h3 class="font-bold text-base mb-2 bg-gray-100 px-3 py-1.5 rounded">Kelas <?= htmlspecialchars($className) ?></h3><?php endif; ?>

    <?php
    // Rekap per siswa
    $byStudent = [];
    foreach ($rows as $r) {
        $byStudent[$r['nis']]['name'] = $r['full_name'];
        $byStudent[$r['nis']]['nis']  = $r['nis'];
        $byStudent[$r['nis']][$r['status']] = ($byStudent[$r['nis']][$r['status']] ?? 0) + 1;
    }
    ?>
    <table class="w-full border-collapse border border-gray-300 text-left mb-6">
        <thead>
            <tr class="bg-gray-100 text-xs uppercase">
                <th class="border border-gray-300 px-3 py-2 w-8 text-center">No</th>
                <th class="border border-gray-300 px-3 py-2">NIS</th>
                <th class="border border-gray-300 px-3 py-2">Nama</th>
                <th class="border border-gray-300 px-3 py-2 text-center">Hadir</th>
                <th class="border border-gray-300 px-3 py-2 text-center">Sakit</th>
                <th class="border border-gray-300 px-3 py-2 text-center">Izin</th>
                <th class="border border-gray-300 px-3 py-2 text-center">Alpa</th>
                <th class="border border-gray-300 px-3 py-2 text-center">Total</th>
            </tr>
        </thead>
        <tbody>
            <?php $no=1; foreach ($byStudent as $s):
                $total = ($s['H']??0)+($s['S']??0)+($s['I']??0)+($s['A']??0);
            ?>
            <tr class="<?= $no%2===0?'bg-gray-50':'' ?>">
                <td class="border border-gray-300 px-3 py-2 text-center"><?= $no++ ?></td>
                <td class="border border-gray-300 px-3 py-2 font-mono text-xs"><?= $s['nis'] ?></td>
                <td class="border border-gray-300 px-3 py-2 font-bold"><?= htmlspecialchars($s['name']) ?></td>
                <td class="border border-gray-300 px-3 py-2 text-center text-green-700 font-bold"><?= $s['H']??0 ?></td>
                <td class="border border-gray-300 px-3 py-2 text-center"><?= $s['S']??0 ?></td>
                <td class="border border-gray-300 px-3 py-2 text-center"><?= $s['I']??0 ?></td>
                <td class="border border-gray-300 px-3 py-2 text-center text-red-600 font-bold"><?= $s['A']??0 ?></td>
                <td class="border border-gray-300 px-3 py-2 text-center"><?= $total ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <?php endforeach; ?>

    <?php if (empty($logs)): ?>
        <p class="text-center text-gray-400 py-8">Tidak ada data absensi.</p>
    <?php endif; ?>

</body>
</html>
