<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Rekap Peminjaman Perpustakaan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>@media print{.no-print{display:none}body{-webkit-print-color-adjust:exact}}</style>
</head>
<body class="bg-white p-8 text-sm text-gray-800">

    <div class="text-center mb-6 border-b-2 border-gray-800 pb-4">
        <h1 class="text-xl font-bold uppercase">Rekap Peminjaman Perpustakaan</h1>
        <?php if ($status): ?><p class="text-gray-600 font-semibold">Status: <?= $status ?></p><?php endif; ?>
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
                <th class="border border-gray-300 px-3 py-2">Nama</th>
                <th class="border border-gray-300 px-3 py-2">Kelas</th>
                <th class="border border-gray-300 px-3 py-2">Judul Buku</th>
                <th class="border border-gray-300 px-3 py-2">Tgl Pinjam</th>
                <th class="border border-gray-300 px-3 py-2">Batas</th>
                <th class="border border-gray-300 px-3 py-2">Tgl Kembali</th>
                <th class="border border-gray-300 px-3 py-2 text-center">Status</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($loans)): ?>
                <tr><td colspan="9" class="border border-gray-300 px-3 py-4 text-center text-gray-400">Tidak ada data.</td></tr>
            <?php endif; ?>
            <?php $no=1; foreach ($loans as $l): ?>
            <tr class="<?= $no%2===0?'bg-gray-50':'' ?>">
                <td class="border border-gray-300 px-3 py-2 text-center"><?= $no++ ?></td>
                <td class="border border-gray-300 px-3 py-2 font-mono text-xs"><?= $l['nis'] ?></td>
                <td class="border border-gray-300 px-3 py-2 font-bold"><?= htmlspecialchars($l['full_name']) ?></td>
                <td class="border border-gray-300 px-3 py-2 text-xs"><?= htmlspecialchars($l['class_name'] ?? '-') ?></td>
                <td class="border border-gray-300 px-3 py-2"><?= htmlspecialchars($l['book_title']) ?></td>
                <td class="border border-gray-300 px-3 py-2 font-mono text-xs"><?= date('d/m/Y', strtotime($l['loan_date'])) ?></td>
                <td class="border border-gray-300 px-3 py-2 font-mono text-xs"><?= date('d/m/Y', strtotime($l['due_date'])) ?></td>
                <td class="border border-gray-300 px-3 py-2 font-mono text-xs"><?= $l['return_date'] ? date('d/m/Y', strtotime($l['return_date'])) : '-' ?></td>
                <td class="border border-gray-300 px-3 py-2 text-center text-xs font-bold <?= $l['status']==='TERLAMBAT'?'text-red-700':($l['status']==='DIPINJAM'?'text-blue-700':'text-green-700') ?>"><?= $l['status'] ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="mt-10 flex justify-end">
        <div class="text-center">
            <p class="mb-16">Mengetahui, Petugas Perpustakaan</p>
            <p class="font-bold border-b border-black inline-block px-8">___________________</p>
        </div>
    </div>

</body>
</html>
