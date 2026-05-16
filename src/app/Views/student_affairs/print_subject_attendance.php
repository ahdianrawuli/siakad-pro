<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Absensi - <?= htmlspecialchars($schedule['subject_name']) ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>@media print{.no-print{display:none}body{-webkit-print-color-adjust:exact}}</style>
</head>
<body class="bg-white p-8 text-sm text-gray-800">

    <div class="text-center mb-6 border-b-2 border-gray-800 pb-4">
        <h1 class="text-xl font-bold uppercase">Rekap Absensi Per Mata Pelajaran</h1>
        <h2 class="text-base font-semibold mt-1"><?= htmlspecialchars($schedule['subject_name']) ?> — Kelas <?= htmlspecialchars($schedule['class_name']) ?></h2>
        <p class="text-gray-600 text-xs mt-1">Guru: <?= htmlspecialchars($schedule['teacher_name']) ?> | <?= $schedule['day'] ?> <?= substr($schedule['start_time'],0,5) ?>–<?= substr($schedule['end_time'],0,5) ?></p>
        <p class="text-gray-500 text-xs mt-1">
            Periode: <?= date('d F Y', strtotime($dateFrom)) ?> s/d <?= date('d F Y', strtotime($dateTo)) ?>
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
                <th class="border border-gray-300 px-3 py-2">NIS</th>
                <th class="border border-gray-300 px-3 py-2">Nama Siswa</th>
                <th class="border border-gray-300 px-3 py-2 text-center">Hadir</th>
                <th class="border border-gray-300 px-3 py-2 text-center">Sakit</th>
                <th class="border border-gray-300 px-3 py-2 text-center">Izin</th>
                <th class="border border-gray-300 px-3 py-2 text-center">Alpa</th>
                <th class="border border-gray-300 px-3 py-2 text-center">Total</th>
                <th class="border border-gray-300 px-3 py-2 text-center">% Hadir</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($report)): ?>
                <tr><td colspan="9" class="border border-gray-300 px-3 py-4 text-center text-gray-400">Tidak ada data.</td></tr>
            <?php endif; ?>
            <?php $no=1; foreach ($report as $r):
                $pct = $r['total'] > 0 ? round($r['hadir'] / $r['total'] * 100) : 0;
            ?>
            <tr class="<?= $no%2===0?'bg-gray-50':'' ?>">
                <td class="border border-gray-300 px-3 py-2 text-center"><?= $no++ ?></td>
                <td class="border border-gray-300 px-3 py-2 font-mono text-xs"><?= $r['nis'] ?></td>
                <td class="border border-gray-300 px-3 py-2 font-bold"><?= htmlspecialchars($r['full_name']) ?></td>
                <td class="border border-gray-300 px-3 py-2 text-center text-green-700 font-bold"><?= $r['hadir'] ?></td>
                <td class="border border-gray-300 px-3 py-2 text-center"><?= $r['sakit'] ?></td>
                <td class="border border-gray-300 px-3 py-2 text-center"><?= $r['izin'] ?></td>
                <td class="border border-gray-300 px-3 py-2 text-center text-red-600 font-bold"><?= $r['alpa'] ?></td>
                <td class="border border-gray-300 px-3 py-2 text-center"><?= $r['total'] ?></td>
                <td class="border border-gray-300 px-3 py-2 text-center font-bold <?= $pct>=75?'text-green-600':'text-red-600' ?>"><?= $pct ?>%</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="mt-10 flex justify-between">
        <div class="text-center">
            <p class="mb-16">Mengetahui,<br>Wali Kelas</p>
            <p class="font-bold border-b border-black inline-block px-8">___________________</p>
        </div>
        <div class="text-center">
            <p class="mb-16">Guru Mata Pelajaran</p>
            <p class="font-bold border-b border-black inline-block px-8"><?= htmlspecialchars($schedule['teacher_name']) ?></p>
        </div>
    </div>

</body>
</html>
