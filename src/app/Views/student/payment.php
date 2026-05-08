<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/student_sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">
<?php
$pageTitle    = 'Keuangan Saya';
$pageSubtitle = isset($student) ? 'Kelas ' . htmlspecialchars($student['class_name'] ?? '-') : 'Biaya Pendaftaran PPDB';
$pageBadgeIcon = 'fa-file-invoice-dollar';
$infoItems    = [
    'Halaman ini menampilkan tagihan dan status pembayaran Anda.',
    'Status "Lunas" berarti pembayaran telah dikonfirmasi oleh admin.',
    'Untuk siswa aktif: tagihan SPP dan biaya lainnya ditampilkan di sini.',
    'Untuk calon santri: upload bukti transfer pembayaran pendaftaran.',
    'Hubungi bendahara jika ada tagihan yang tidak sesuai.',
];
require __DIR__ . '/../layouts/portal_header_card.php';
?>

    <?php \App\Core\Session::flash(); ?>

    <?php if (isset($bills)): ?>
        <?php
            $totalUnpaid = array_sum(array_column(array_filter($bills, fn($b) => $b['status'] === 'UNPAID'), 'amount'));
            $totalPaid   = array_sum(array_column(array_filter($bills, fn($b) => $b['status'] === 'PAID'), 'amount'));
        ?>

        <!-- Filter -->
        <div class="portal-filter-bar">
            <form method="GET" class="flex flex-wrap items-center gap-3">
                <div class="relative flex-1 min-w-[180px]">
                    <span class="absolute inset-y-0 left-3 flex items-center text-slate-400"><i class="fa-solid fa-magnifying-glass text-xs"></i></span>
                    <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>"
                        placeholder="Cari tagihan..."
                        class="w-full pl-9 pr-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:ring-2 focus:ring-green-500 outline-none">
                </div>
                <select name="status" class="select2-portal py-2 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none min-w-[150px]">
                    <option value="">Semua Status</option>
                    <option value="UNPAID" <?= ($_GET['status'] ?? '') === 'UNPAID' ? 'selected' : '' ?>>Belum Bayar</option>
                    <option value="PAID"   <?= ($_GET['status'] ?? '') === 'PAID'   ? 'selected' : '' ?>>Lunas</option>
                </select>
                <button type="submit" class="bg-green-700 text-white px-5 py-2 rounded-xl text-sm font-bold hover:bg-green-800 transition">Terapkan</button>
            </form>
        </div>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="bg-red-50 border border-red-100 rounded-2xl p-4">
                <p class="text-xs text-red-500 font-bold uppercase mb-1">Belum Dibayar</p>
                <p class="text-2xl font-extrabold text-red-700">Rp <?= number_format($totalUnpaid, 0, ',', '.') ?></p>
            </div>
            <div class="bg-green-50 border border-green-100 rounded-2xl p-4">
                <p class="text-xs text-green-600 font-bold uppercase mb-1">Sudah Dibayar</p>
                <p class="text-2xl font-extrabold text-green-700">Rp <?= number_format($totalPaid, 0, ',', '.') ?></p>
            </div>
        </div>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-slate-50 border-b border-slate-200">
                    <tr>
                        <th class="px-5 py-3 text-left text-xs font-bold text-slate-500 uppercase">Keterangan</th>
                        <th class="px-5 py-3 text-right text-xs font-bold text-slate-500 uppercase">Jumlah</th>
                        <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase">Jatuh Tempo</th>
                        <th class="px-5 py-3 text-center text-xs font-bold text-slate-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (empty($bills)): ?>
                    <tr><td colspan="4" class="text-center py-12 text-slate-400"><i class="fa-solid fa-file-invoice text-3xl mb-2 block opacity-30"></i>Belum ada tagihan.</td></tr>
                    <?php else: ?>
                    <?php foreach ($bills as $b): ?>
                    <tr class="hover:bg-slate-50 transition">
                        <td class="px-5 py-3 font-medium text-slate-800">
                            <?= htmlspecialchars($b['title'] ?: ($b['fee_type_name'] ?? '-')) ?>
                            <?php if ($b['description']): ?><span class="block text-xs text-slate-400"><?= htmlspecialchars($b['description']) ?></span><?php endif; ?>
                        </td>
                        <td class="px-5 py-3 text-right font-bold text-slate-700">Rp <?= number_format($b['amount'], 0, ',', '.') ?></td>
                        <td class="px-5 py-3 text-center text-slate-500"><?= $b['due_date'] ? date('d/m/Y', strtotime($b['due_date'])) : '-' ?></td>
                        <td class="px-5 py-3 text-center">
                            <?php if ($b['status'] === 'PAID'): ?>
                                <span class="bg-green-100 text-green-700 text-xs font-bold px-2.5 py-1 rounded-full">Lunas</span>
                            <?php else: ?>
                                <span class="bg-red-100 text-red-700 text-xs font-bold px-2.5 py-1 rounded-full">Belum Bayar</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
            </div>
        </div>

    <?php else: ?>
        <!-- Calon Santri: form upload bukti bayar PPDB -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
            <div class="space-y-6">
                <div class="bg-white p-5 md:p-6 rounded-2xl shadow-sm border border-slate-200">
                    <h3 class="font-bold text-slate-500 text-xs uppercase tracking-wider mb-2">Total Tagihan</h3>
                    <div class="flex items-end">
                        <span class="text-3xl md:text-4xl font-extrabold text-slate-800">Rp 250.000</span>
                        <span class="text-sm text-slate-500 ml-2 mb-2 font-medium">/ pendaftaran</span>
                    </div>
                    <div class="mt-6 bg-blue-50 rounded-2xl p-4 border border-blue-100 relative overflow-hidden">
                        <div class="absolute right-0 top-0 opacity-10 transform translate-x-4 -translate-y-4">
                            <i class="fa-solid fa-building-columns text-8xl text-blue-800"></i>
                        </div>
                        <div class="relative z-10">
                            <p class="text-xs font-bold text-blue-800 uppercase opacity-70 mb-1">Transfer ke Bank BCA</p>
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-mono text-xl md:text-2xl font-bold text-slate-800 tracking-wide" id="rekNum">1234567890</span>
                                <button onclick="copyRek()" class="bg-white text-blue-600 hover:bg-blue-50 px-3 py-1.5 rounded-lg text-xs font-bold border border-blue-200 shadow-sm transition">
                                    <i class="fa-regular fa-copy mr-1"></i> Salin
                                </button>
                            </div>
                            <p class="text-sm text-slate-600 font-medium">a.n Yayasan Thawalib Parabek</p>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <div class="bg-white p-5 md:p-6 rounded-2xl shadow-sm border border-slate-200 h-full">
                    <h3 class="font-bold text-slate-800 mb-6 flex items-center">
                        <i class="fa-solid fa-receipt mr-2 text-green-600"></i>
                        Konfirmasi Pembayaran
                    </h3>

                    <?php if (isset($payment) && $payment): ?>
                        <div class="text-center py-4">
                            <?php if($payment['status'] == 'PENDING'): ?>
                                <div class="w-20 h-20 bg-yellow-50 text-yellow-600 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse">
                                    <i class="fa-solid fa-hourglass-half text-3xl"></i>
                                </div>
                                <h4 class="font-bold text-lg text-slate-800">Sedang Diverifikasi</h4>
                                <p class="text-slate-500 text-sm mt-1 max-w-xs mx-auto">Admin sedang mengecek bukti transfer Anda. Proses ini memakan waktu max 1x24 jam.</p>
                            <?php elseif($payment['status'] == 'VERIFIED'): ?>
                                <div class="w-20 h-20 bg-green-50 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fa-solid fa-check text-3xl"></i>
                                </div>
                                <h4 class="font-bold text-lg text-slate-800">Pembayaran Lunas</h4>
                                <p class="text-slate-500 text-sm mt-1">Terima kasih, pembayaran Anda telah diterima.</p>
                            <?php else: ?>
                                <div class="w-20 h-20 bg-red-50 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fa-solid fa-xmark text-3xl"></i>
                                </div>
                                <h4 class="font-bold text-lg text-red-600">Bukti Ditolak</h4>
                                <p class="text-slate-500 text-sm mt-1">Catatan: <?= htmlspecialchars($payment['notes'] ?? '') ?></p>
                            <?php endif; ?>
                            <div class="mt-6">
                                <a href="/uploads/payments/<?= $payment['proof_file'] ?>" target="_blank" class="inline-flex items-center text-blue-600 bg-blue-50 px-4 py-2 rounded-xl text-sm font-bold hover:bg-blue-100 transition">
                                    <i class="fa-solid fa-image mr-2"></i> Lihat Bukti Terkirim
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <form action="/student/payment/store" method="POST" enctype="multipart/form-data" class="space-y-4">
                            <?= \App\Core\Csrf::input() ?>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Tanggal Transfer</label>
                                <input type="date" name="payment_date" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3 py-2.5 text-slate-800 focus:outline-none focus:ring-2 focus:ring-green-500/30 focus:border-green-500 transition" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Nominal</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2.5 text-slate-500 text-sm font-bold">Rp</span>
                                    <input type="number" name="amount" value="250000" class="w-full bg-slate-100 border border-slate-200 rounded-xl pl-10 pr-3 py-2.5 text-slate-800 font-bold cursor-not-allowed" readonly>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">File Bukti</label>
                                <div class="border-2 border-dashed border-slate-300 rounded-2xl p-4 text-center hover:bg-slate-50 transition cursor-pointer relative">
                                    <input type="file" name="proof_file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept=".jpg,.jpeg,.png,.pdf" required>
                                    <div class="pointer-events-none">
                                        <i class="fa-solid fa-cloud-arrow-up text-2xl text-slate-400 mb-2"></i>
                                        <p class="text-sm font-bold text-green-600">Klik untuk pilih foto</p>
                                        <p class="text-xs text-slate-400 mt-1">JPG, PNG, atau PDF (Max 2MB)</p>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="w-full bg-green-700 text-white font-bold py-3 rounded-xl hover:bg-green-800 shadow-lg shadow-green-200 transition flex justify-center items-center">
                                <i class="fa-regular fa-paper-plane mr-2"></i> Kirim Bukti Transfer
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
</main>

<script>
function copyRek() {
    navigator.clipboard.writeText(document.getElementById("rekNum").innerText).then(function() {
        alert("Nomor rekening disalin!");
    });
}
$(function(){ $('.select2-portal').select2({ width: 'resolve' }); });
</script>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
