<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Absensi Ekskul - <?= htmlspecialchars($ekskul['name'] ?? '') ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>@media print{.no-print{display:none}body{-webkit-print-color-adjust:exact}}</style>
</head>
<body class="bg-white p-8 text-sm text-gray-800">

    <div class="text-center mb-6 border-b-2 border-gray-800 pb-4">
        <h1 class="text-xl font-bold uppercase">Daftar Hadir Ekstrakurikuler</h1>
        <h2 class="text-base font-semibold"><?= htmlspecialchars($ekskul['name'] ?? '') ?></h2>
        <p class="text-gray-500 text-xs mt-1">Tanggal: <?= date('d F Y', strtotime($date)) ?> &nbsp;|&nbsp; Dicetak: <?= date('d F Y, H:i') ?></p>
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
                <th class="border border-gray-300 px-3 py-2">Nama Lengkap</th>
                <th class="border border-gray-300 px-3 py-2">Kelas</th>
                <th class="border border-gray-300 px-3 py-2 text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($members)): ?>
                <tr><td colspan="5" class="border border-gray-300 px-3 py-4 text-center text-gray-400">Tidak ada data.</td></tr>
            <?php endif; ?>
            <?php $no=1; foreach ($members as $m):
                $st = $attendance[$m['student_id']] ?? '-';
                $label = match($st) { 'HADIR'=>'Hadir','IZIN'=>'Izin','SAKIT'=>'Sakit','ALPA'=>'Alpa', default=>'-' };
                $color = match($st) { 'HADIR'=>'text-green-700 font-bold','ALPA'=>'text-red-700 font-bold','IZIN'=>'text-blue-700','SAKIT'=>'text-purple-700', default=>'text-gray-400' };
            ?>
            <tr class="<?= $no%2===0?'bg-gray-50':'' ?>">
                <td class="border border-gray-300 px-3 py-2 text-center"><?= $no++ ?></td>
                <td class="border border-gray-300 px-3 py-2 font-mono text-xs"><?= htmlspecialchars($m['nis']) ?></td>
                <td class="border border-gray-300 px-3 py-2 font-bold"><?= htmlspecialchars($m['full_name']) ?></td>
                <td class="border border-gray-300 px-3 py-2 text-xs"><?= htmlspecialchars($m['class_name'] ?? '-') ?></td>
                <td class="border border-gray-300 px-3 py-2 text-center <?= $color ?>"><?= $label ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="mt-10 flex justify-end">
        <div class="text-center">
            <p class="mb-16">Pembina Ekstrakurikuler</p>
            <p class="font-bold border-b border-black inline-block px-8">___________________</p>
        </div>
    </div>

</body>
</html>
