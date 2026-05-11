<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Jadwal Pelajaran<?= $classroom ? ' - ' . htmlspecialchars($classroom['name']) : '' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print { .no-print { display: none; } body { -webkit-print-color-adjust: exact; } }
    </style>
</head>
<body class="bg-white text-gray-800 p-8 text-sm">

    <div class="text-center mb-6 border-b-2 border-gray-800 pb-4">
        <h1 class="text-xl font-bold uppercase">Jadwal Pelajaran</h1>
        <?php if ($classroom): ?>
            <h2 class="text-lg font-semibold">Kelas <?= htmlspecialchars($classroom['name']) ?></h2>
        <?php endif; ?>
        <?php if ($day): ?>
            <p class="text-gray-600">Hari: <?= htmlspecialchars($day) ?></p>
        <?php endif; ?>
        <p class="text-gray-500 text-xs mt-1">
            Tahun Ajaran: <?= htmlspecialchars($activeYear['name'] ?? '-') ?> &nbsp;|&nbsp;
            Dicetak: <?= date('d F Y, H:i') ?>
        </p>
    </div>

    <div class="no-print mb-4 flex gap-2">
        <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded font-bold text-sm">
            <i class="fa-solid fa-print mr-1"></i> Cetak
        </button>
        <a href="javascript:history.back()" class="bg-gray-200 text-gray-700 px-4 py-2 rounded font-bold text-sm">Kembali</a>
    </div>

    <?php
    // Kelompokkan per kelas lalu per hari
    $grouped = [];
    foreach ($schedules as $s) {
        $grouped[$s['class_name']][$s['day']][] = $s;
    }
    $dayOrder = ['SENIN','SELASA','RABU','KAMIS','JUMAT','SABTU','AHAD'];
    ?>

    <?php foreach ($grouped as $className => $days): ?>
    <div class="mb-8">
        <?php if (!$classroom): ?>
        <h3 class="font-bold text-base mb-2 bg-gray-100 px-3 py-1.5 rounded">Kelas <?= htmlspecialchars($className) ?></h3>
        <?php endif; ?>

        <table class="w-full border-collapse border border-gray-300 text-left">
            <thead>
                <tr class="bg-gray-100 text-xs uppercase">
                    <th class="border border-gray-300 px-3 py-2 w-24">Hari</th>
                    <th class="border border-gray-300 px-3 py-2 w-28">Waktu</th>
                    <th class="border border-gray-300 px-3 py-2">Mata Pelajaran</th>
                    <th class="border border-gray-300 px-3 py-2">Guru</th>
                    <th class="border border-gray-300 px-3 py-2 w-24">Ruangan</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sortedDays = array_intersect($dayOrder, array_keys($days));
                foreach ($sortedDays as $dayName):
                    $rows = $days[$dayName];
                    usort($rows, fn($a,$b) => strcmp($a['start_time'], $b['start_time']));
                    foreach ($rows as $i => $r):
                ?>
                <tr class="<?= ($i % 2 === 1) ? 'bg-gray-50' : '' ?>">
                    <td class="border border-gray-300 px-3 py-2 font-semibold"><?= $i === 0 ? htmlspecialchars($dayName) : '' ?></td>
                    <td class="border border-gray-300 px-3 py-2 font-mono text-xs">
                        <?= substr($r['start_time'],0,5) ?> – <?= substr($r['end_time'],0,5) ?>
                    </td>
                    <td class="border border-gray-300 px-3 py-2 font-bold"><?= htmlspecialchars($r['subject_name']) ?></td>
                    <td class="border border-gray-300 px-3 py-2"><?= htmlspecialchars($r['teacher_name']) ?></td>
                    <td class="border border-gray-300 px-3 py-2 text-xs"><?= htmlspecialchars($r['room'] ?? '-') ?></td>
                </tr>
                <?php endforeach; endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endforeach; ?>

    <?php if (empty($schedules)): ?>
        <p class="text-center text-gray-400 py-8">Tidak ada jadwal ditemukan.</p>
    <?php endif; ?>

</body>
</html>
