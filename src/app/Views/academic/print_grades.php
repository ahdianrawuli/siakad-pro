<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Nilai - <?= htmlspecialchars($schedule['subject_name']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>@media print{.no-print{display:none}body{-webkit-print-color-adjust:exact}}</style>
</head>
<body class="bg-white p-6 text-gray-800" style="font-size:11px">

    <div class="text-center mb-5 border-b-2 border-gray-800 pb-3">
        <h1 class="text-lg font-bold uppercase">Rekap Nilai Siswa</h1>
        <h2 class="text-sm font-semibold"><?= htmlspecialchars($schedule['subject_name']) ?> — Kelas <?= htmlspecialchars($schedule['class_name']) ?></h2>
        <p class="text-gray-500 text-[10px] mt-1">Guru: <?= htmlspecialchars($schedule['teacher_name']) ?> | Bobot: Harian <?= $weights['weight_daily'] ?>%, UTS <?= $weights['weight_uts'] ?>%, UAS <?= $weights['weight_uas'] ?>% | Dicetak: <?= date('d F Y') ?></p>
    </div>

    <div class="no-print mb-4 flex gap-2">
        <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded font-bold text-xs">Cetak</button>
        <a href="javascript:history.back()" class="bg-gray-200 text-gray-700 px-4 py-2 rounded font-bold text-xs">Kembali</a>
    </div>

    <?php $totalHarian = count($harianColumns); ?>
    <table class="w-full border-collapse border border-gray-300 text-left">
        <thead>
            <tr class="bg-gray-100">
                <th class="border border-gray-300 px-2 py-1.5 text-center w-6" rowspan="2">No</th>
                <th class="border border-gray-300 px-2 py-1.5" rowspan="2">NIS</th>
                <th class="border border-gray-300 px-2 py-1.5" rowspan="2">Nama</th>
                <?php foreach ($harianColumns as $key => $col): ?>
                <th class="border border-gray-300 px-1 py-1.5 text-center" title="<?= htmlspecialchars($col['description']??'') ?>">
                    <?= $col['category'] ?><?= $col['seq_num'] ?>
                    <?php if ($col['date']): ?><div class="text-[8px] text-gray-400"><?= date('d/m', strtotime($col['date'])) ?></div><?php endif; ?>
                </th>
                <?php endforeach; ?>
                <th class="border border-gray-300 px-2 py-1.5 text-center bg-amber-50">Rata²</th>
                <th class="border border-gray-300 px-2 py-1.5 text-center bg-blue-50">UTS</th>
                <th class="border border-gray-300 px-2 py-1.5 text-center bg-green-50">UAS</th>
                <th class="border border-gray-300 px-2 py-1.5 text-center bg-purple-50">NA</th>
            </tr>
        </thead>
        <tbody>
            <?php $no=1; foreach ($students as $s):
                $sum = 0;
                foreach ($harianColumns as $key => $col) { $sum += (float)($gradeMap[$s['id']][$key] ?? 0); }
                $avg = $totalHarian > 0 ? round($sum / $totalHarian, 1) : 0;
                $uts = (float)($gradeMap[$s['id']]['UTS'] ?? 0);
                $uas = (float)($gradeMap[$s['id']]['UAS'] ?? 0);
                $na = round(($avg * $weights['weight_daily'] + $uts * $weights['weight_uts'] + $uas * $weights['weight_uas']) / 100, 1);
            ?>
            <tr class="<?= $no%2===0?'bg-gray-50':'' ?>">
                <td class="border border-gray-300 px-2 py-1 text-center"><?= $no++ ?></td>
                <td class="border border-gray-300 px-2 py-1 font-mono"><?= $s['nis'] ?></td>
                <td class="border border-gray-300 px-2 py-1 font-semibold"><?= htmlspecialchars($s['full_name']) ?></td>
                <?php foreach ($harianColumns as $key => $col): ?>
                <td class="border border-gray-300 px-1 py-1 text-center"><?= $gradeMap[$s['id']][$key] ?? '-' ?></td>
                <?php endforeach; ?>
                <td class="border border-gray-300 px-2 py-1 text-center bg-amber-50 font-bold"><?= $avg ?></td>
                <td class="border border-gray-300 px-2 py-1 text-center bg-blue-50 font-bold"><?= $uts ?: '-' ?></td>
                <td class="border border-gray-300 px-2 py-1 text-center bg-green-50 font-bold"><?= $uas ?: '-' ?></td>
                <td class="border border-gray-300 px-2 py-1 text-center bg-purple-50 font-bold"><?= $na ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="mt-8 flex justify-between text-[10px]">
        <div class="text-center">
            <p class="mb-14">Mengetahui, Wali Kelas</p>
            <p class="font-bold border-b border-black inline-block px-6">___________________</p>
        </div>
        <div class="text-center">
            <p class="mb-14">Guru Mata Pelajaran</p>
            <p class="font-bold border-b border-black inline-block px-6"><?= htmlspecialchars($schedule['teacher_name']) ?></p>
        </div>
    </div>

</body>
</html>
