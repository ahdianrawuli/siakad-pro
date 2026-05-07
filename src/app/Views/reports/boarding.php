<?php require_once __DIR__ . '/../layouts/header.php'; ?>
<?php require_once __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-slate-800">Rapor Asrama</h1>
        <p class="text-slate-500 text-sm mt-1">Data santri berdasarkan asrama untuk keperluan rapor kepesantrenan.</p>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <!-- Filter -->
    <form method="GET" class="bg-white p-4 rounded-2xl shadow-sm border border-slate-200 mb-6 flex gap-3">
        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nama / NIS..."
            class="flex-1 px-4 py-2 border border-slate-200 rounded-xl text-sm outline-none focus:ring-2 focus:ring-blue-500/50 bg-slate-50">
        <button type="submit" class="px-4 py-2 bg-slate-800 text-white rounded-xl text-sm font-semibold">Cari</button>
        <?php if($search): ?><a href="/reports/boarding" class="px-4 py-2 bg-red-50 text-red-500 rounded-xl text-sm font-semibold">Reset</a><?php endif; ?>
    </form>

    <!-- Per Asrama -->
    <?php
    $grouped = [];
    foreach ($students as $s) {
        $grouped[$s['dorm_name'] ?? 'Tanpa Asrama'][] = $s;
    }
    ?>

    <?php if (empty($grouped)): ?>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-12 text-center text-slate-400">
            Belum ada santri yang terdaftar di asrama.
        </div>
    <?php endif; ?>

    <?php foreach ($grouped as $dormName => $dormStudents): ?>
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-6">
        <div class="px-5 py-4 border-b border-slate-100 flex justify-between items-center bg-slate-50">
            <h3 class="font-bold text-slate-800 flex items-center gap-2">
                <i class="fa-solid fa-house text-blue-500"></i> <?= htmlspecialchars($dormName) ?>
            </h3>
            <div class="flex items-center gap-3">
                <span class="text-xs font-semibold text-slate-500"><?= count($dormStudents) ?> santri</span>
                <a href="/report/boarding?dorm=<?= urlencode($dormName) ?>" target="_blank"
                    class="px-3 py-1 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg text-xs font-semibold transition flex items-center gap-1">
                    <i class="fa-solid fa-print"></i> Cetak
                </a>
            </div>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm text-left">
                <thead class="border-b border-slate-100">
                    <tr>
                        <th class="px-4 py-2 text-xs font-bold text-slate-400 uppercase">No</th>
                        <th class="px-4 py-2 text-xs font-bold text-slate-400 uppercase">NIS</th>
                        <th class="px-4 py-2 text-xs font-bold text-slate-400 uppercase">Nama Santri</th>
                        <th class="px-4 py-2 text-xs font-bold text-slate-400 uppercase">Jenis Kelamin</th>
                        <th class="px-4 py-2 text-xs font-bold text-slate-400 uppercase text-center">Rapor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    <?php foreach ($dormStudents as $i => $s): ?>
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-4 py-2 text-slate-400 text-xs"><?= $i + 1 ?></td>
                        <td class="px-4 py-2 font-mono text-xs text-slate-500"><?= htmlspecialchars($s['nis']) ?></td>
                        <td class="px-4 py-2 font-semibold text-slate-800"><?= htmlspecialchars($s['full_name']) ?></td>
                        <td class="px-4 py-2 text-slate-600"><?= $s['gender'] == 'L' ? 'Laki-laki' : 'Perempuan' ?></td>
                        <td class="px-4 py-2 text-center">
                            <a href="/report/boarding/print?student_id=<?= $s['id'] ?>" target="_blank"
                                class="inline-flex items-center gap-1 px-3 py-1 bg-green-50 text-green-600 hover:bg-green-600 hover:text-white rounded-lg text-xs font-semibold transition">
                                <i class="fa-solid fa-file-lines"></i> Lihat
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endforeach; ?>
</main>

<?php require_once __DIR__ . '/../layouts/footer.php'; ?>
