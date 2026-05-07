<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Info Keuangan & Tagihan</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">
                Data tagihan untuk: <strong class="text-blue-600"><?= htmlspecialchars($_GET['nis'] ?? '-') ?></strong>
            </p>
            <div class="mt-3 flex items-center gap-2">
                <?php
                $totalUnpaid = 0;
                if (!empty($bills)) { foreach ($bills as $b) { if (($b['status'] ?? 'UNPAID') == 'UNPAID') $totalUnpaid += $b['amount']; } }
                ?>
                <?php if ($totalUnpaid > 0): ?>
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-red-50 text-red-700 rounded-lg text-xs font-bold border border-red-100">
                        <i class="fa-solid fa-circle-exclamation"></i> Belum Bayar: Rp <?= number_format($totalUnpaid, 0, ',', '.') ?>
                    </div>
                <?php else: ?>
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-green-50 text-green-700 rounded-lg text-xs font-bold border border-green-100">
                        <i class="fa-solid fa-circle-check"></i> Lunas Semua
                    </div>
                <?php endif; ?>
                <button onclick="document.getElementById('infoModal').classList.remove('hidden')"
                    class="w-7 h-7 rounded-full bg-slate-100 text-slate-500 hover:bg-blue-100 hover:text-blue-600 flex items-center justify-center transition-colors border border-slate-200" title="Panduan Penggunaan">
                    <i class="fa-solid fa-circle-info text-sm"></i>
                </button>
            </div>
        </div>
        <a href="/finance" class="px-4 py-2.5 bg-white text-slate-700 border border-slate-300 rounded-xl text-sm font-semibold hover:bg-slate-50 transition-all flex items-center gap-2 w-fit">
            <i class="fa-solid fa-arrow-left text-slate-400"></i> Kembali
        </a>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <?php if (($_SESSION['user_role'] ?? '') != 'siswa'): ?>
        <!-- Form Tambah Tagihan -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 h-fit">
            <h4 class="font-bold text-slate-700 flex items-center gap-2 border-b border-slate-100 pb-4 mb-5">
                <i class="fa-solid fa-plus-circle text-slate-400"></i> Tambah Tagihan Manual
            </h4>
            <form action="/finance/billing/create" method="POST" class="space-y-4">
                <input type="hidden" name="student_nis" value="<?= htmlspecialchars($_GET['nis'] ?? '') ?>">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Jenis Tagihan</label>
                    <select name="title" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                        <option value="SPP Bulan Ini">SPP Bulan Ini</option>
                        <option value="Uang Gedung">Uang Gedung</option>
                        <option value="Daftar Ulang">Daftar Ulang</option>
                        <option value="Buku Paket">Buku Paket</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nominal (Rp)</label>
                    <input type="number" name="amount" placeholder="cth: 350000"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Keterangan <span class="text-slate-400 font-normal">(Opsional)</span></label>
                    <textarea name="description" rows="2" placeholder="cth: SPP bulan Januari 2025"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all resize-none"></textarea>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2.5 rounded-xl font-bold hover:bg-blue-700 shadow-md shadow-blue-500/20 transition-all text-sm">
                    <i class="fa-solid fa-plus mr-2"></i> Buat Tagihan
                </button>
            </form>
        </div>
        <?php endif; ?>

        <!-- Tabel Tagihan -->
        <div class="<?= (($_SESSION['user_role'] ?? '') == 'siswa') ? 'lg:col-span-3' : 'lg:col-span-2' ?>">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-5 border-b border-slate-100 bg-slate-50">
                    <h4 class="font-bold text-slate-700 flex items-center gap-2">
                        <i class="fa-solid fa-receipt text-slate-400"></i> Riwayat Tagihan
                    </h4>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full whitespace-nowrap text-left">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Keterangan</th>
                                <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Nominal</th>
                                <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Status</th>
                                <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            <?php if (!empty($bills)): ?>
                                <?php foreach ($bills as $bill):
                                    $billTitle  = $bill['title'] ?? $bill['name'] ?? 'Tagihan #' . $bill['id'];
                                    $billDesc   = $bill['description'] ?? '-';
                                    $billAmount = $bill['amount'] ?? 0;
                                    $billStatus = $bill['status'] ?? 'UNPAID';
                                ?>
                                <tr class="hover:bg-slate-50/80 transition-colors text-sm">
                                    <td class="px-5 py-4 font-mono text-xs text-slate-500"><?= date('d/m/Y', strtotime($bill['created_at'] ?? 'now')) ?></td>
                                    <td class="px-5 py-4">
                                        <div class="font-extrabold text-slate-800"><?= htmlspecialchars($billTitle) ?></div>
                                        <div class="text-[10px] text-slate-400 mt-0.5"><?= htmlspecialchars($billDesc) ?></div>
                                    </td>
                                    <td class="px-5 py-4 text-right font-mono font-bold text-slate-700">
                                        Rp <?= number_format($billAmount, 0, ',', '.') ?>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <?php if ($billStatus == 'PAID'): ?>
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-50 text-green-700 rounded-full text-[10px] font-bold border border-green-200">
                                                <i class="fa-solid fa-circle-check"></i> LUNAS
                                            </span>
                                        <?php else: ?>
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-red-50 text-red-700 rounded-full text-[10px] font-bold border border-red-200">
                                                <i class="fa-solid fa-clock"></i> BELUM BAYAR
                                            </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-5 py-4 text-center">
                                        <?php if ($billStatus == 'UNPAID'): ?>
                                            <button onclick="openPaymentModal(<?= $bill['id'] ?>, '<?= addslashes($billTitle) ?>', <?= $billAmount ?>)"
                                                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-bold hover:bg-blue-700 transition-colors shadow-sm">
                                                <i class="fa-solid fa-upload"></i> Bayar
                                            </button>
                                        <?php else: ?>
                                            <span class="text-slate-400 text-xs"><i class="fa-solid fa-check mr-1"></i>Selesai</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="5" class="px-5 py-16 text-center text-slate-400 text-sm font-medium">Belum ada tagihan untuk siswa ini.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal Info -->
<div id="infoModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-blue-50 flex justify-between items-center">
            <h3 class="font-bold text-blue-800 flex items-center gap-2"><i class="fa-solid fa-circle-info text-blue-500"></i> Panduan Tagihan Siswa</h3>
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-blue-100 text-blue-500 hover:bg-blue-200 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="p-6 space-y-5 text-sm text-slate-600">
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">1</span> Cara Penggunaan</h4>
                <ol class="list-decimal list-inside space-y-1.5 text-slate-500">
                    <li>Tambah tagihan manual menggunakan form di kiri.</li>
                    <li>Klik <strong class="text-slate-700">Bayar</strong> pada tagihan yang belum lunas.</li>
                    <li>Upload bukti transfer lalu kirim untuk konfirmasi.</li>
                </ol>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">2</span> Relasi ke Menu Lain</h4>
                <div class="space-y-2">
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-file-invoice-dollar text-blue-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Jenis Tagihan (SPP)</div><div class="text-[11px] text-slate-400">Jenis tagihan dikelola di menu <strong>Keuangan → Jenis Tagihan</strong>.</div></div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-chart-bar text-green-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Laporan Keuangan</div><div class="text-[11px] text-slate-400">Rekap pembayaran tersedia di menu <strong>Keuangan → Laporan</strong>.</div></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end">
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="px-5 py-2 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700 transition">Mengerti</button>
        </div>
    </div>
</div>

<!-- Modal Pembayaran -->
<div id="paymentModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-700 flex items-center gap-2"><i class="fa-solid fa-upload text-slate-400"></i> Konfirmasi Pembayaran</h3>
            <button onclick="document.getElementById('paymentModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-slate-200 text-slate-500 hover:bg-slate-300 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="/finance/pay" method="POST" enctype="multipart/form-data" class="p-6 space-y-4">
            <input type="hidden" name="bill_id" id="modalBillId">
            <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 text-sm text-blue-800">
                Tagihan: <strong id="modalBillTitle"></strong><br>
                Total: <strong id="modalBillAmount"></strong>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Bukti Transfer</label>
                <input type="file" name="payment_proof" required
                    class="w-full text-sm text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-all">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('paymentModal').classList.add('hidden')" class="flex-1 bg-slate-100 text-slate-600 py-2.5 rounded-xl font-bold hover:bg-slate-200 transition text-sm">Batal</button>
                <button type="submit" class="flex-1 bg-green-600 text-white py-2.5 rounded-xl font-bold hover:bg-green-700 shadow-md shadow-green-500/20 transition text-sm">Kirim Bukti</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openPaymentModal(id, title, amount) {
        document.getElementById('modalBillId').value = id;
        document.getElementById('modalBillTitle').innerText = title;
        document.getElementById('modalBillAmount').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
        document.getElementById('paymentModal').classList.remove('hidden');
    }
    window.onclick = function(e) {
        ['paymentModal','infoModal'].forEach(function(id) {
            if (e.target == document.getElementById(id)) document.getElementById(id).classList.add('hidden');
        });
    }
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
