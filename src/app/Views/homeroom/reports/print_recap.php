<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Rekapitulasi Kelas <?= $classroom['name'] ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body { -webkit-print-color-adjust: exact; }
            .no-print { display: none; }
        }
    </style>
</head>
<body class="bg-white text-gray-800 p-8 text-sm">

    <div class="text-center mb-8 border-b-2 border-gray-800 pb-4">
        <h1 class="text-2xl font-bold uppercase">Laporan Rekapitulasi Kelas</h1>
        <h2 class="text-xl"><?= $classroom['name'] ?> - Wali Kelas: <?= $classroom['teacher_name'] ?></h2>
        <p class="text-gray-500">Tanggal Cetak: <?= date('d F Y') ?></p>
    </div>

    <div class="mb-4">
        <button onclick="window.print()" class="no-print bg-blue-600 text-white px-4 py-2 rounded font-bold">Cetak Dokumen</button>
    </div>

    <table class="w-full border-collapse border border-gray-300 text-left">
        <thead>
            <tr class="bg-gray-100 uppercase text-xs">
                <th class="border border-gray-300 px-3 py-2 text-center w-10">No</th>
                <th class="border border-gray-300 px-3 py-2">NIS</th>
                <th class="border border-gray-300 px-3 py-2">Nama Lengkap</th>
                <th class="border border-gray-300 px-3 py-2 text-center">Sakit</th>
                <th class="border border-gray-300 px-3 py-2 text-center">Izin</th>
                <th class="border border-gray-300 px-3 py-2 text-center">Alpa</th>
                <th class="border border-gray-300 px-3 py-2 text-center">Poin Pelanggaran</th>
            </tr>
        </thead>
        <tbody>
            <?php $no=1; foreach($students as $s): ?>
            <tr>
                <td class="border border-gray-300 px-3 py-2 text-center"><?= $no++ ?></td>
                <td class="border border-gray-300 px-3 py-2"><?= $s['nis'] ?></td>
                <td class="border border-gray-300 px-3 py-2 font-bold"><?= $s['full_name'] ?></td>
                
                <td class="border border-gray-300 px-3 py-2 text-center"><?= $attendance[$s['id']]['sakit'] ?? 0 ?></td>
                <td class="border border-gray-300 px-3 py-2 text-center"><?= $attendance[$s['id']]['izin'] ?? 0 ?></td>
                <td class="border border-gray-300 px-3 py-2 text-center text-red-600 font-bold"><?= $attendance[$s['id']]['alpa'] ?? 0 ?></td>
                
                <td class="border border-gray-300 px-3 py-2 text-center font-bold">
                    <?= $violations[$s['id']] > 0 ? $violations[$s['id']] : '-' ?>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="mt-8 flex justify-end">
        <div class="text-center">
            <p>Mengetahui,</p>
            <p class="mb-16">Wali Kelas</p>
            <p class="font-bold border-b border-black inline-block"><?= $classroom['teacher_name'] ?></p>
        </div>
    </div>

</body>
</html>
