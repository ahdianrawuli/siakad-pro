<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-x-hidden overflow-y-auto bg-slate-50 p-4 md:p-6">

    <!-- Header -->
    <div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Laporan Bendahara</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Rekapitulasi pemasukan dan tunggakan pesantren.</p>
            <div class="mt-3 flex items-center gap-2">
                <button onclick="document.getElementById('infoModal').classList.remove('hidden')"
                    class="w-7 h-7 rounded-full bg-slate-100 text-slate-500 hover:bg-blue-100 hover:text-blue-600 flex items-center justify-center transition border border-slate-200" title="Panduan">
                    <i class="fa-solid fa-circle-info text-sm"></i>
                </button>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <form method="GET" class="flex items-center gap-2" id="monthForm">
                <input type="hidden" name="month" id="monthInput" value="<?= htmlspecialchars($month) ?>">
                <input type="text" id="monthPicker" readonly
                    value="<?= date('F Y', strtotime($month . '-01')) ?>"
                    class="border border-slate-200 rounded-xl px-3 py-2.5 text-sm bg-slate-50 focus:bg-white outline-none cursor-pointer w-40 text-center font-semibold text-slate-700">
                <button type="submit" class="bg-slate-800 text-white px-4 py-2.5 rounded-xl text-sm font-bold hover:bg-slate-900 transition">Tampilkan</button>
            </form>
            <button onclick="window.print()" class="px-4 py-2.5 bg-white text-slate-700 border border-slate-300 rounded-xl text-sm font-semibold hover:bg-slate-50 transition flex items-center gap-2">
                <i class="fa-solid fa-print text-slate-400"></i> Cetak
            </button>
        </div>

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
        <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
        <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/index.js"></script>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/plugins/monthSelect/style.css">
        <script>
        flatpickr('#monthPicker', {
            plugins: [new monthSelectPlugin({ shorthand: false, dateFormat: 'Y-m', altFormat: 'F Y' })],
            defaultDate: '<?= $month ?>',
            onChange: function(dates, dateStr) {
                document.getElementById('monthInput').value = dateStr;
            }
        });
        </script>
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

<!-- Modal Info -->
<div id="infoModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-blue-50 flex justify-between items-center">
            <h3 class="font-bold text-blue-800 flex items-center gap-2"><i class="fa-solid fa-circle-info text-blue-500"></i> Panduan Laporan Bendahara</h3>
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-blue-100 text-blue-500 hover:bg-blue-200 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="p-6 space-y-5 text-sm text-slate-600 max-h-[70vh] overflow-y-auto">
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">1</span> Filter Bulan</h4>
                <p class="text-slate-500 text-xs">Gunakan input bulan di header untuk menampilkan laporan periode tertentu. Kartu ringkasan, tabel pemasukan, dan transaksi terbaru akan menyesuaikan bulan yang dipilih.</p>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">2</span> Notifikasi WA Tagihan</h4>
                <p class="text-slate-500 text-xs">Kirim pengingat otomatis ke orang tua siswa yang masih memiliki tunggakan. Bisa difilter per kelas atau kirim ke semua kelas sekaligus.</p>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">3</span> Isi Laporan</h4>
                <ul class="list-disc list-inside space-y-1.5 text-slate-500 text-xs">
                    <li><strong class="text-slate-700">Pemasukan per Jenis</strong> — Rekap tagihan lunas bulan ini, dikelompokkan per jenis tagihan.</li>
                    <li><strong class="text-slate-700">Tunggakan</strong> — Total tagihan yang belum dibayar, dikelompokkan per jenis.</li>
                    <li><strong class="text-slate-700">Rekap per Kelas</strong> — Perbandingan sudah bayar vs belum bayar per kelas untuk tahun berjalan.</li>
                    <li><strong class="text-slate-700">Transaksi Terbaru</strong> — 20 pembayaran terakhir bulan ini.</li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">4</span> Relasi ke Menu Lain</h4>
                <div class="space-y-2">
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-cash-register text-blue-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Kasir / Tagihan</div><div class="text-[11px] text-slate-400">Data berasal dari pembayaran di <strong>Keuangan → Kasir</strong>.</div></div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-chart-bar text-green-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Laporan Keuangan</div><div class="text-[11px] text-slate-400">Untuk filter lebih detail (per siswa, export Excel/PDF) gunakan <strong>Keuangan → Laporan Keuangan</strong>.</div></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end">
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="px-5 py-2 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700 transition">Mengerti</button>
        </div>
    </div>
</div>

<script>
window.onclick = function(e) {
    if (e.target == document.getElementById('infoModal')) document.getElementById('infoModal').classList.add('hidden');
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
