<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap <?= htmlspecialchars($type['name']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>@media print{.no-print{display:none}body{-webkit-print-color-adjust:exact}}td,th{vertical-align:middle}</style>
</head>
<body class="bg-white p-8 text-sm text-gray-800">

    <div class="text-center mb-6 border-b-2 border-gray-800 pb-4">
        <h1 class="text-xl font-bold uppercase">Rekap Absensi: <?= htmlspecialchars($type['name']) ?></h1>
        <?php if ($classroom): ?><h2 class="text-base font-semibold">Kelas <?= htmlspecialchars($classroom['name']) ?></h2><?php endif; ?>
        <p class="text-gray-500 text-xs mt-1">Tanggal: <?= date('d F Y', strtotime($date)) ?> | Dicetak: <?= date('d F Y, H:i') ?></p>
        <?php
        $sessions = max(1, (int)$type['sessions']);
        $sessionLabels = $type['session_labels'] ? explode(',', $type['session_labels']) : [];
        $hasTime = (int)$type['has_time'];
        ?>
    </div>

    <div class="no-print mb-4 flex gap-2">
        <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded font-bold text-sm">Cetak</button>
        <a href="javascript:history.back()" class="bg-gray-200 text-gray-700 px-4 py-2 rounded font-bold text-sm">Kembali</a>
    </div>

    <?php
    // Group report by person+date for rowspan
    $grouped = [];
    foreach ($report as $r) {
        $key = $r['name'] . '|' . $r['date'];
        $grouped[$key]['info'] = $r;
        $grouped[$key]['sessions'][] = $r;
    }
    ?>

    <table class="w-full border-collapse border border-gray-300 text-left">
        <thead>
            <tr class="bg-gray-100 text-xs uppercase">
                <th class="border border-gray-300 px-3 py-2 w-8 text-center">No</th>
                <th class="border border-gray-300 px-3 py-2">Nama</th>
                <?php if ($type['target'] !== 'GURU'): ?><th class="border border-gray-300 px-3 py-2">Kelas</th><?php endif; ?>
                <?php if ($type['target'] === 'GURU'): ?><th class="border border-gray-300 px-3 py-2">Jabatan</th><?php endif; ?>
                <?php if ($sessions > 1): ?><th class="border border-gray-300 px-3 py-2 text-center">Sesi</th><?php endif; ?>
                <?php if ($hasTime): ?><th class="border border-gray-300 px-3 py-2 text-center">Jam</th><?php endif; ?>
                <th class="border border-gray-300 px-3 py-2 text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($grouped)): ?>
                <tr><td colspan="7" class="border border-gray-300 px-3 py-4 text-center text-gray-400">Tidak ada data.</td></tr>
            <?php endif; ?>
            <?php $no=1; foreach ($grouped as $g):
                $info = $g['info'];
                $rows = $g['sessions'];
                $rowspan = count($rows);
                foreach ($rows as $idx => $r):
            ?>
            <tr class="<?= $no%2===0?'bg-gray-50':'' ?>">
                <?php if ($idx === 0): ?>
                <td class="border border-gray-300 px-3 py-1.5 text-center" rowspan="<?= $rowspan ?>"><?= $no ?></td>
                <td class="border border-gray-300 px-3 py-1.5 font-bold" rowspan="<?= $rowspan ?>"><?= htmlspecialchars($info['name']) ?></td>
                <?php if ($type['target'] !== 'GURU'): ?><td class="border border-gray-300 px-3 py-1.5 text-xs" rowspan="<?= $rowspan ?>"><?= htmlspecialchars($info['class_name'] ?? '-') ?></td><?php endif; ?>
                <?php if ($type['target'] === 'GURU'): ?><td class="border border-gray-300 px-3 py-1.5 text-xs" rowspan="<?= $rowspan ?>"><?= htmlspecialchars($info['position_name'] ?? '-') ?></td><?php endif; ?>
                <?php endif; ?>
                <?php if ($sessions > 1): ?>
                <td class="border border-gray-300 px-3 py-1.5 text-center text-xs"><?= $sessionLabels[($r['session_num'] ?? 1) - 1] ?? 'Sesi '.$r['session_num'] ?></td>
                <?php endif; ?>
                <?php if ($hasTime): ?>
                <td class="border border-gray-300 px-3 py-1.5 text-center font-mono text-xs"><?= $r['time_in'] ? substr($r['time_in'], 0, 5) : '-' ?></td>
                <?php endif; ?>
                <td class="border border-gray-300 px-3 py-1.5 text-center font-bold"><?= $r['status'] ?></td>
            </tr>
            <?php endforeach; $no++; endforeach; ?>
        </tbody>
    </table>

</body>
</html>
