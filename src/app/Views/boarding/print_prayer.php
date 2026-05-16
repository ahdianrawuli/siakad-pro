<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Absensi Sholat</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>@media print{.no-print{display:none}body{-webkit-print-color-adjust:exact}}</style>
</head>
<body class="bg-white p-6 text-gray-800" style="font-size:11px">

    <div class="text-center mb-5 border-b-2 border-gray-800 pb-3">
        <h1 class="text-lg font-bold uppercase">Rekap Absensi Sholat</h1>
        <?php if ($classroom): ?><h2 class="text-sm font-semibold">Kelas <?= htmlspecialchars($classroom['name']) ?></h2><?php endif; ?>
        <p class="text-gray-500 text-[10px] mt-1">
            Periode: <?= date('d F Y', strtotime($dateFrom)) ?> s/d <?= date('d F Y', strtotime($dateTo)) ?>
            &nbsp;|&nbsp; Dicetak: <?= date('d F Y, H:i') ?>
        </p>
    </div>

    <div class="no-print mb-4 flex gap-2">
        <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded font-bold text-xs">Cetak</button>
        <a href="javascript:history.back()" class="bg-gray-200 text-gray-700 px-4 py-2 rounded font-bold text-xs">Kembali</a>
    </div>

    <!-- Legend -->
    <div class="mb-4 flex items-center gap-4 text-[10px]">
        <span class="font-bold">Keterangan:</span>
        <span class="flex items-center gap-1"><span class="w-3 h-3 bg-green-100 border border-green-300 rounded inline-block"></span> H = Hadir</span>
        <span class="flex items-center gap-1"><span class="w-3 h-3 bg-yellow-100 border border-yellow-300 rounded inline-block"></span> T = Terlambat</span>
        <span class="flex items-center gap-1"><span class="w-3 h-3 bg-blue-100 border border-blue-300 rounded inline-block"></span> I = Izin</span>
        <span class="flex items-center gap-1"><span class="w-3 h-3 bg-purple-100 border border-purple-300 rounded inline-block"></span> S = Sakit</span>
        <span class="flex items-center gap-1"><span class="w-3 h-3 bg-red-100 border border-red-300 rounded inline-block"></span> X = Tidak Hadir</span>
    </div>

    <table class="w-full border-collapse border border-gray-300 text-left">
        <thead>
            <tr class="bg-gray-100">
                <th class="border border-gray-300 px-2 py-1.5 text-center w-6" rowspan="2">No</th>
                <th class="border border-gray-300 px-2 py-1.5" rowspan="2">NIS</th>
                <th class="border border-gray-300 px-2 py-1.5" rowspan="2">Nama</th>
                <th class="border border-gray-300 px-2 py-1.5" rowspan="2">Kelas</th>
                <?php foreach ($prayerTypes as $pt): ?>
                <th class="border border-gray-300 px-1 py-1.5 text-center" colspan="5">
                    <?= $pt['name'] ?>
                    <div class="text-[8px] font-normal text-gray-400"><?= $pt['category'] ?></div>
                </th>
                <?php endforeach; ?>
                <th class="border border-gray-300 px-2 py-1.5 text-center" colspan="5">TOTAL</th>
            </tr>
            <tr class="bg-gray-50 text-[9px] uppercase">
                <?php foreach ($prayerTypes as $pt): ?>
                <th class="border border-gray-300 px-1 py-1 text-center text-green-700">H</th>
                <th class="border border-gray-300 px-1 py-1 text-center text-yellow-700">T</th>
                <th class="border border-gray-300 px-1 py-1 text-center text-blue-700">I</th>
                <th class="border border-gray-300 px-1 py-1 text-center text-purple-700">S</th>
                <th class="border border-gray-300 px-1 py-1 text-center text-red-700">X</th>
                <?php endforeach; ?>
                <th class="border border-gray-300 px-1 py-1 text-center text-green-700">H</th>
                <th class="border border-gray-300 px-1 py-1 text-center text-yellow-700">T</th>
                <th class="border border-gray-300 px-1 py-1 text-center text-blue-700">I</th>
                <th class="border border-gray-300 px-1 py-1 text-center text-purple-700">S</th>
                <th class="border border-gray-300 px-1 py-1 text-center text-red-700">X</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($report)): ?>
                <tr><td colspan="<?= 4 + count($prayerTypes)*5 + 5 ?>" class="border border-gray-300 px-3 py-4 text-center text-gray-400">Tidak ada data.</td></tr>
            <?php endif; ?>
            <?php $no=1; foreach ($report as $r):
                $totH=0; $totT=0; $totI=0; $totS=0; $totX=0;
                foreach ($prayerTypes as $pt) {
                    $totH += $r['prayer_'.$pt['id'].'_hadir'];
                    $totT += $r['prayer_'.$pt['id'].'_terlambat'];
                    $totI += $r['prayer_'.$pt['id'].'_izin'];
                    $totS += $r['prayer_'.$pt['id'].'_sakit'];
                    $totX += $r['prayer_'.$pt['id'].'_tidak'];
                }
            ?>
            <tr class="<?= $no%2===0?'bg-gray-50':'' ?>">
                <td class="border border-gray-300 px-2 py-1 text-center"><?= $no++ ?></td>
                <td class="border border-gray-300 px-2 py-1 font-mono"><?= $r['nis'] ?></td>
                <td class="border border-gray-300 px-2 py-1 font-semibold"><?= htmlspecialchars($r['full_name']) ?></td>
                <td class="border border-gray-300 px-2 py-1"><?= htmlspecialchars($r['class_name'] ?? '-') ?></td>
                <?php foreach ($prayerTypes as $pt):
                    $h = $r['prayer_'.$pt['id'].'_hadir'];
                    $t = $r['prayer_'.$pt['id'].'_terlambat'];
                    $i2 = $r['prayer_'.$pt['id'].'_izin'];
                    $s2 = $r['prayer_'.$pt['id'].'_sakit'];
                    $x = $r['prayer_'.$pt['id'].'_tidak'];
                ?>
                <td class="border border-gray-300 px-1 py-1 text-center <?= $h>0?'text-green-700 font-bold':'text-gray-300' ?>"><?= $h ?: '-' ?></td>
                <td class="border border-gray-300 px-1 py-1 text-center <?= $t>0?'text-yellow-700 font-bold':'text-gray-300' ?>"><?= $t ?: '-' ?></td>
                <td class="border border-gray-300 px-1 py-1 text-center <?= $i2>0?'text-blue-700 font-bold':'text-gray-300' ?>"><?= $i2 ?: '-' ?></td>
                <td class="border border-gray-300 px-1 py-1 text-center <?= $s2>0?'text-purple-700 font-bold':'text-gray-300' ?>"><?= $s2 ?: '-' ?></td>
                <td class="border border-gray-300 px-1 py-1 text-center <?= $x>0?'text-red-700 font-bold':'text-gray-300' ?>"><?= $x ?: '-' ?></td>
                <?php endforeach; ?>
                <td class="border border-gray-300 px-1 py-1 text-center text-green-700 font-bold"><?= $totH ?></td>
                <td class="border border-gray-300 px-1 py-1 text-center text-yellow-700 font-bold"><?= $totT ?></td>
                <td class="border border-gray-300 px-1 py-1 text-center text-blue-700 font-bold"><?= $totI ?></td>
                <td class="border border-gray-300 px-1 py-1 text-center text-purple-700 font-bold"><?= $totS ?></td>
                <td class="border border-gray-300 px-1 py-1 text-center text-red-700 font-bold"><?= $totX ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="mt-8 flex justify-end">
        <div class="text-center">
            <p class="mb-14">Mengetahui, Pembina Asrama</p>
            <p class="font-bold border-b border-black inline-block px-8">___________________</p>
        </div>
    </div>

</body>
</html>
