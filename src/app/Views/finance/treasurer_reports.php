<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50 p-4 md:p-6">
    <div class="mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800">Laporan Bendahara</h1>
            <p class="text-sm text-gray-500">Rekapitulasi pemasukan dan tunggakan pesantren.</p>
        </div>
        <div class="flex items-center gap-2">
            <form method="GET" class="flex items-center gap-2">
                <input type="month" name="month" value="<?= htmlspecialchars($month) ?>"
                       class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-green-500">
                <button class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700">Tampilkan</button>
            </form>
            <button onclick="window.print()" class="bg-gray-700 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-gray-800 flex items-center gap-2">
                <i class="fa-solid fa-print"></i> Cetak
            </button>
        </div>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <!-- Notifikasi WA Tagihan -->
    <div class="bg-green-50 border border-green-200 rounded-xl p-4 mb-6 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <p class="font-semibold text-green-800"><i class="fa-brands fa-whatsapp mr-2"></i>Kirim Notifikasi Tagihan via WhatsApp</p>
            <p class="text-sm text-green-600">Kirim pengingat otomatis ke orang tua siswa yang belum membayar.</p>
        </div>
        <form method="POST" action="/finance/notify-bills" class="flex items-center gap-2">
            <select name="class_id" class="border border-green-300 rounded-lg px-3 py-2 text-sm bg-white">
                <option value="">Semua Kelas</option>
                <?php
                $db = \App\Core\Database::getInstance();
                $classes = $db->query("SELECT id, name FROM classrooms ORDER BY name")->fetchAll();
                foreach ($classes as $c): ?>
                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                <?php endforeach; ?>
            </select>
            <button class="bg-green-600 text-white px-4 py-2 rounded-lg text-sm font-medium hover:bg-green-700 whitespace-nowrap">
                <i class="fa-solid fa-paper-plane mr-1"></i> Kirim
            </button>
        </form>
    </div>

    <!-- Kartu Ringkasan -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Pemasukan Bulan Ini</p>
            <p class="text-2xl font-bold text-green-600">Rp <?= number_format($totalIncome ?? 0, 0, ',', '.') ?></p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Total Tunggakan</p>
            <p class="text-2xl font-bold text-red-600">Rp <?= number_format($totalUnpaid ?? 0, 0, ',', '.') ?></p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-5">
            <p class="text-xs text-gray-500 uppercase font-semibold mb-1">Transaksi Bulan Ini</p>
            <p class="text-2xl font-bold text-blue-600"><?= count($recent) ?></p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

        <!-- Pemasukan per Jenis -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800"><i class="fa-solid fa-arrow-trend-up text-green-500 mr-2"></i>Pemasukan per Jenis (<?= $month ?>)</h2>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-2 text-left">Jenis Tagihan</th>
                        <th class="px-4 py-2 text-center">Jumlah</th>
                        <th class="px-4 py-2 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php if (empty($income)): ?>
                    <tr><td colspan="3" class="text-center py-6 text-gray-400">Belum ada pemasukan bulan ini.</td></tr>
                    <?php else: ?>
                    <?php foreach ($income as $i): ?>
                    <tr>
                        <td class="px-4 py-2 text-gray-800"><?= htmlspecialchars($i['title']) ?></td>
                        <td class="px-4 py-2 text-center text-gray-500"><?= $i['count'] ?> siswa</td>
                        <td class="px-4 py-2 text-right font-semibold text-green-700">Rp <?= number_format($i['total'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="bg-green-50 font-bold">
                        <td class="px-4 py-2 text-green-800" colspan="2">Total</td>
                        <td class="px-4 py-2 text-right text-green-800">Rp <?= number_format($totalIncome ?? 0, 0, ',', '.') ?></td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Tunggakan per Jenis -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-gray-100">
                <h2 class="font-bold text-gray-800"><i class="fa-solid fa-triangle-exclamation text-red-500 mr-2"></i>Tunggakan Belum Dibayar</h2>
            </div>
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-2 text-left">Jenis Tagihan</th>
                        <th class="px-4 py-2 text-center">Jumlah</th>
                        <th class="px-4 py-2 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php if (empty($unpaid)): ?>
                    <tr><td colspan="3" class="text-center py-6 text-gray-400">Tidak ada tunggakan.</td></tr>
                    <?php else: ?>
                    <?php foreach ($unpaid as $u): ?>
                    <tr>
                        <td class="px-4 py-2 text-gray-800"><?= htmlspecialchars($u['title']) ?></td>
                        <td class="px-4 py-2 text-center text-gray-500"><?= $u['count'] ?> siswa</td>
                        <td class="px-4 py-2 text-right font-semibold text-red-600">Rp <?= number_format($u['total'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="bg-red-50 font-bold">
                        <td class="px-4 py-2 text-red-800" colspan="2">Total Tunggakan</td>
                        <td class="px-4 py-2 text-right text-red-800">Rp <?= number_format($totalUnpaid ?? 0, 0, ',', '.') ?></td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Rekap per Kelas -->
    <?php if (!empty($byClass)): ?>
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden mb-6">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="font-bold text-gray-800"><i class="fa-solid fa-school text-blue-500 mr-2"></i>Rekap per Kelas (Tahun <?= substr($month,0,4) ?>)</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-2 text-left">Kelas</th>
                        <th class="px-4 py-2 text-right">Sudah Bayar</th>
                        <th class="px-4 py-2 text-right">Belum Bayar</th>
                        <th class="px-4 py-2 text-right">Total Tagihan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php foreach ($byClass as $bc): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2 font-medium text-gray-800"><?= htmlspecialchars($bc['class_name'] ?? 'Tanpa Kelas') ?></td>
                        <td class="px-4 py-2 text-right text-green-700">Rp <?= number_format($bc['paid'], 0, ',', '.') ?></td>
                        <td class="px-4 py-2 text-right text-red-600">Rp <?= number_format($bc['unpaid'], 0, ',', '.') ?></td>
                        <td class="px-4 py-2 text-right font-semibold text-gray-800">Rp <?= number_format($bc['paid'] + $bc['unpaid'], 0, ',', '.') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php endif; ?>

    <!-- Transaksi Terbaru -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-gray-100">
            <h2 class="font-bold text-gray-800"><i class="fa-solid fa-clock-rotate-left text-gray-500 mr-2"></i>Transaksi Terbaru (<?= $month ?>)</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-xs text-gray-500 uppercase">
                    <tr>
                        <th class="px-4 py-2 text-left">Siswa</th>
                        <th class="px-4 py-2 text-left">Kelas</th>
                        <th class="px-4 py-2 text-left">Jenis</th>
                        <th class="px-4 py-2 text-right">Jumlah</th>
                        <th class="px-4 py-2 text-center">Tanggal</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    <?php if (empty($recent)): ?>
                    <tr><td colspan="5" class="text-center py-8 text-gray-400">Belum ada transaksi bulan ini.</td></tr>
                    <?php else: ?>
                    <?php foreach ($recent as $r): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-2">
                            <p class="font-medium text-gray-800"><?= htmlspecialchars($r['full_name']) ?></p>
                            <p class="text-xs text-gray-400"><?= $r['nis'] ?></p>
                        </td>
                        <td class="px-4 py-2 text-gray-600"><?= htmlspecialchars($r['class_name'] ?? '-') ?></td>
                        <td class="px-4 py-2 text-gray-600"><?= htmlspecialchars($r['title']) ?></td>
                        <td class="px-4 py-2 text-right font-semibold text-green-700">Rp <?= number_format($r['amount'], 0, ',', '.') ?></td>
                        <td class="px-4 py-2 text-center text-gray-500 text-xs"><?= date('d M Y', strtotime($r['updated_at'])) ?></td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
