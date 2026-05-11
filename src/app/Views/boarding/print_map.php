<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Denah Kamar Asrama</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            .no-print { display: none; }
            body { -webkit-print-color-adjust: exact; }
            .page-break { page-break-after: always; }
        }
    </style>
</head>
<body class="bg-white p-6 text-sm text-gray-800">

    <div class="text-center mb-6 border-b-2 border-gray-800 pb-4">
        <h1 class="text-xl font-bold uppercase">Denah Kamar Asrama</h1>
        <p class="text-gray-400 text-xs mt-1">Dicetak: <?= date('d F Y, H:i') ?></p>
    </div>

    <div class="no-print mb-4 flex gap-2">
        <button onclick="window.print()" class="bg-blue-600 text-white px-4 py-2 rounded font-bold text-sm">
            <i class="fa-solid fa-print mr-1"></i> Cetak
        </button>
        <a href="javascript:history.back()" class="bg-gray-200 text-gray-700 px-4 py-2 rounded font-bold text-sm">Kembali</a>
    </div>

    <?php
    $grouped = [];
    foreach ($dorms as $d) {
        $grouped[$d['unit']][$d['gender'] === 'L' ? 'Putra' : 'Putri'][] = $d;
    }
    ?>

    <?php foreach ($grouped as $unit => $genders): ?>
    <div class="mb-8">
        <h2 class="text-base font-bold bg-gray-100 px-3 py-1.5 rounded mb-4 uppercase">Unit <?= $unit ?></h2>
        <?php foreach ($genders as $gender => $rooms): ?>
        <h3 class="text-sm font-semibold text-gray-600 mb-3"><?= $gender ?></h3>
        <div style="column-count:3;column-gap:1rem" class="mb-6">
            <?php foreach ($rooms as $dorm):
                $students = $dormMap[$dorm['id']] ?? [];
            ?>
            <div class="border border-gray-300 rounded-lg overflow-hidden mb-4" style="break-inside:avoid">
                <div class="bg-gray-100 px-3 py-2 flex justify-between items-center border-b border-gray-300">
                    <span class="font-bold text-xs"><?= htmlspecialchars($dorm['name']) ?></span>
                    <span class="text-xs text-gray-500"><?= count($students) ?>/<?= $dorm['capacity'] ?></span>
                </div>
                <table class="w-full text-xs">
                    <?php if (empty($students)): ?>
                        <tr><td class="px-3 py-2 text-gray-400 italic">Kosong</td></tr>
                    <?php endif; ?>
                    <?php foreach ($students as $i => $s): ?>
                    <tr class="<?= $i % 2 === 1 ? 'bg-gray-50' : '' ?> border-b border-gray-100">
                        <td class="px-2 py-1 text-gray-400 w-6 text-center"><?= $i+1 ?></td>
                        <td class="px-2 py-1 font-semibold"><?= htmlspecialchars($s['full_name']) ?></td>
                        <td class="px-2 py-1 text-gray-400"><?= htmlspecialchars($s['class_name'] ?? '-') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endforeach; ?>

</body>
</html>
