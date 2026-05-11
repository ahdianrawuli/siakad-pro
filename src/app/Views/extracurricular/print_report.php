<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rapor Ekskul - <?= htmlspecialchars($ekskul['name'] ?? '') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print { body { -webkit-print-color-adjust: exact; } .no-print { display: none; } }
    </style>
</head>
<body class="bg-white text-gray-800 p-8 text-sm">

    <div class="text-center mb-6 border-b-2 border-gray-800 pb-4">
        <h1 class="text-xl font-bold uppercase">Laporan Rekapitulasi Ekstrakurikuler</h1>
        <h2 class="text-lg font-semibold"><?= htmlspecialchars($ekskul['name'] ?? '') ?></h2>
        <p class="text-gray-500">
            Bulan: <?= date('F Y', strtotime($month . '-01')) ?> &nbsp;|&nbsp;
            Tahun Ajaran: <?= htmlspecialchars($year['name'] ?? '-') ?>
        </p>
        <p class="text-gray-400 text-xs mt-1">Dicetak: <?= date('d F Y, H:i') ?></p>
    </div>

    <div class="mb-4 no-print">
        <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded font-bold text-sm">
            <i class="fa-solid fa-print mr-1"></i> Cetak Dokumen
        </button>
        <a href="javascript:history.back()" class="ml-2 bg-gray-200 text-gray-700 px-4 py-2 rounded font-bold text-sm">Kembali</a>
    </div>

    <table class="w-full border-collapse border border-gray-300 text-left">
        <thead>
            <tr class="bg-gray-100 uppercase text-xs">
                <th class="border border-gray-300 px-3 py-2 text-center w-8">No</th>
                <th class="border border-gray-300 px-3 py-2">NIS</th>
                <th class="border border-gray-300 px-3 py-2">Nama Lengkap</th>
                <th class="border border-gray-300 px-3 py-2">Kelas</th>
                <th class="border border-gray-300 px-3 py-2 text-center">Hadir</th>
                <th class="border border-gray-300 px-3 py-2 text-center">Izin</th>
                <th class="border border-gray-300 px-3 py-2 text-center">Sakit</th>
                <th class="border border-gray-300 px-3 py-2 text-center">Alpa</th>
                <th class="border border-gray-300 px-3 py-2 text-center">Total</th>
                <th class="border border-gray-300 px-3 py-2 text-center">% Hadir</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($members)): ?>
                <tr><td colspan="10" class="border border-gray-300 px-3 py-4 text-center text-gray-400">Tidak ada data anggota.</td></tr>
            <?php endif; ?>
            <?php $no = 1; foreach ($members as $m):
                $s = $summary[$m['student_id']] ?? ['HADIR'=>0,'IZIN'=>0,'SAKIT'=>0,'ALPA'=>0,'total'=>0];
                $pct = $s['total'] > 0 ? round($s['HADIR'] / $s['total'] * 100) : 0;
            ?>
            <tr class="<?= $no % 2 === 0 ? 'bg-gray-50' : '' ?>">
                <td class="border border-gray-300 px-3 py-2 text-center"><?= $no++ ?></td>
                <td class="border border-gray-300 px-3 py-2 font-mono text-xs"><?= htmlspecialchars($m['nis']) ?></td>
                <td class="border border-gray-300 px-3 py-2 font-bold"><?= htmlspecialchars($m['full_name']) ?></td>
                <td class="border border-gray-300 px-3 py-2 text-xs"><?= htmlspecialchars($m['class_name'] ?? '-') ?></td>
                <td class="border border-gray-300 px-3 py-2 text-center text-green-700 font-bold"><?= $s['HADIR'] ?></td>
                <td class="border border-gray-300 px-3 py-2 text-center"><?= $s['IZIN'] ?></td>
                <td class="border border-gray-300 px-3 py-2 text-center"><?= $s['SAKIT'] ?></td>
                <td class="border border-gray-300 px-3 py-2 text-center text-red-600 font-bold"><?= $s['ALPA'] ?></td>
                <td class="border border-gray-300 px-3 py-2 text-center"><?= $s['total'] ?></td>
                <td class="border border-gray-300 px-3 py-2 text-center font-bold <?= $pct >= 75 ? 'text-green-600' : 'text-red-600' ?>"><?= $pct ?>%</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="mt-10 flex justify-end no-print">
        <div class="text-center">
            <p class="mb-1">Mengetahui,</p>
            <p class="mb-16">Pembina Ekstrakurikuler</p>
            <p class="font-bold border-b border-black inline-block px-8">___________________</p>
        </div>
    </div>

</body>
</html>
