<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/student_sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-4 md:p-8 pb-24">
    <h1 class="text-xl md:text-2xl font-bold text-gray-800 mb-2">Keuangan Saya</h1>

    <?php \App\Core\Session::flash(); ?>

    <?php if (isset($bills)): ?>
        <?php
            $totalUnpaid = array_sum(array_column(array_filter($bills, fn($b) => $b['status'] === 'UNPAID'), 'amount'));
            $totalPaid   = array_sum(array_column(array_filter($bills, fn($b) => $b['status'] === 'PAID'), 'amount'));
        ?>
        <p class="text-sm text-gray-500 mb-6">Kelas <?= htmlspecialchars($student['class_name'] ?? '-') ?></p>

        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="bg-red-50 border border-red-100 rounded-xl p-4">
                <p class="text-xs text-red-500 font-bold uppercase mb-1">Belum Dibayar</p>
                <p class="text-2xl font-extrabold text-red-700">Rp <?= number_format($totalUnpaid, 0, ',', '.') ?></p>
            </div>
            <div class="bg-green-50 border border-green-100 rounded-xl p-4">
                <p class="text-xs text-green-600 font-bold uppercase mb-1">Sudah Dibayar</p>
                <p class="text-2xl font-extrabold text-green-700">Rp <?= number_format($totalPaid, 0, ',', '.') ?></p>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 text-gray-500 text-xs uppercase">
                    <tr>
                        <th class="px-4 py-3 text-left">Keterangan</th>
                        <th class="px-4 py-3 text-right">Jumlah</th>
                        <th class="px-4 py-3 text-center">Jatuh Tempo</th>
                        <th class="px-4 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if (empty($bills)): ?>
                    <tr><td colspan="4" class="text-center py-10 text-gray-400">Belum ada tagihan.</td></tr>
                    <?php else: ?>
                    <?php foreach ($bills as $b): ?>
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 font-medium text-gray-800">
                            <?= htmlspecialchars($b['title'] ?: ($b['fee_type_name'] ?? '-')) ?>
                            <?php if ($b['description']): ?>
                                <span class="block text-xs text-gray-400"><?= htmlspecialchars($b['description']) ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="px-4 py-3 text-right font-bold text-gray-700">Rp <?= number_format($b['amount'], 0, ',', '.') ?></td>
                        <td class="px-4 py-3 text-center text-gray-500"><?= $b['due_date'] ? date('d/m/Y', strtotime($b['due_date'])) : '-' ?></td>
                        <td class="px-4 py-3 text-center">
                            <?php if ($b['status'] === 'PAID'): ?>
                                <span class="bg-green-100 text-green-700 text-xs font-bold px-2 py-1 rounded-full">Lunas</span>
                            <?php else: ?>
                                <span class="bg-red-100 text-red-700 text-xs font-bold px-2 py-1 rounded-full">Belum Bayar</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

    <?php else: ?>
        <!-- Calon Santri: form upload bukti bayar PPDB -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 md:gap-8">
            <div class="space-y-6">
                <div class="bg-white p-5 md:p-6 rounded-xl shadow-sm border border-gray-200">
                    <h3 class="font-bold text-gray-500 text-xs uppercase tracking-wider mb-2">Total Tagihan</h3>
                    <div class="flex items-end">
                        <span class="text-3xl md:text-4xl font-extrabold text-gray-800">Rp 250.000</span>
                        <span class="text-sm text-gray-500 ml-2 mb-2 font-medium">/ pendaftaran</span>
                    </div>
                    <div class="mt-6 bg-blue-50 rounded-xl p-4 border border-blue-100 relative overflow-hidden">
                        <div class="absolute right-0 top-0 opacity-10 transform translate-x-4 -translate-y-4">
                            <i class="fa-solid fa-building-columns text-8xl text-blue-800"></i>
                        </div>
                        <div class="relative z-10">
                            <p class="text-xs font-bold text-blue-800 uppercase opacity-70 mb-1">Transfer ke Bank BCA</p>
                            <div class="flex items-center justify-between mb-2">
                                <span class="font-mono text-xl md:text-2xl font-bold text-gray-800 tracking-wide" id="rekNum">1234567890</span>
                                <button onclick="copyRek()" class="bg-white text-blue-600 hover:bg-blue-50 px-3 py-1.5 rounded-lg text-xs font-bold border border-blue-200 shadow-sm transition active:scale-95">
                                    <i class="fa-regular fa-copy mr-1"></i> Salin
                                </button>
                            </div>
                            <p class="text-sm text-gray-600 font-medium">a.n Yayasan Thawalib Parabek</p>
                        </div>
                    </div>
                </div>
            </div>

            <div>
                <div class="bg-white p-5 md:p-6 rounded-xl shadow-sm border border-gray-200 h-full">
                    <h3 class="font-bold text-gray-800 mb-6 flex items-center">
                        <i class="fa-solid fa-receipt mr-2 text-green-600"></i>
                        Konfirmasi Pembayaran
                    </h3>

                    <?php if (isset($payment) && $payment): ?>
                        <div class="text-center py-4">
                            <?php if($payment['status'] == 'PENDING'): ?>
                                <div class="w-20 h-20 bg-yellow-50 text-yellow-600 rounded-full flex items-center justify-center mx-auto mb-4 animate-pulse">
                                    <i class="fa-solid fa-hourglass-half text-3xl"></i>
                                </div>
                                <h4 class="font-bold text-lg text-gray-800">Sedang Diverifikasi</h4>
                                <p class="text-gray-500 text-sm mt-1 max-w-xs mx-auto">Admin sedang mengecek bukti transfer Anda. Proses ini memakan waktu max 1x24 jam.</p>
                            <?php elseif($payment['status'] == 'VERIFIED'): ?>
                                <div class="w-20 h-20 bg-green-50 text-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fa-solid fa-check text-3xl"></i>
                                </div>
                                <h4 class="font-bold text-lg text-gray-800">Pembayaran Lunas</h4>
                                <p class="text-gray-500 text-sm mt-1">Terima kasih, pembayaran Anda telah diterima.</p>
                            <?php else: ?>
                                <div class="w-20 h-20 bg-red-50 text-red-600 rounded-full flex items-center justify-center mx-auto mb-4">
                                    <i class="fa-solid fa-xmark text-3xl"></i>
                                </div>
                                <h4 class="font-bold text-lg text-red-600">Bukti Ditolak</h4>
                                <p class="text-gray-500 text-sm mt-1">Catatan: <?= htmlspecialchars($payment['notes'] ?? '') ?></p>
                            <?php endif; ?>
                            <div class="mt-6">
                                <a href="/uploads/payments/<?= $payment['proof_file'] ?>" target="_blank" class="inline-flex items-center text-blue-600 bg-blue-50 px-4 py-2 rounded-lg text-sm font-bold hover:bg-blue-100 transition">
                                    <i class="fa-solid fa-image mr-2"></i> Lihat Bukti Terkirim
                                </a>
                            </div>
                        </div>
                    <?php else: ?>
                        <form action="/student/payment/store" method="POST" enctype="multipart/form-data" class="space-y-4">
                            <?= \App\Core\Csrf::input() ?>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Tanggal Transfer</label>
                                <input type="date" name="payment_date" class="w-full bg-gray-50 border border-gray-200 rounded-lg px-3 py-2.5 text-gray-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 transition" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">Nominal</label>
                                <div class="relative">
                                    <span class="absolute left-3 top-2.5 text-gray-500 text-sm font-bold">Rp</span>
                                    <input type="number" name="amount" value="250000" class="w-full bg-gray-100 border border-gray-200 rounded-lg pl-10 pr-3 py-2.5 text-gray-800 font-bold cursor-not-allowed focus:outline-none" readonly>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase mb-1">File Bukti</label>
                                <div class="border-2 border-dashed border-gray-300 rounded-xl p-4 text-center hover:bg-gray-50 transition cursor-pointer relative">
                                    <input type="file" name="proof_file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" accept=".jpg,.jpeg,.png,.pdf" required>
                                    <div class="pointer-events-none">
                                        <i class="fa-solid fa-cloud-arrow-up text-2xl text-gray-400 mb-2"></i>
                                        <p class="text-sm font-bold text-blue-600">Klik untuk pilih foto</p>
                                        <p class="text-xs text-gray-400 mt-1">JPG, PNG, atau PDF (Max 2MB)</p>
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="w-full bg-blue-600 text-white font-bold py-3 rounded-lg hover:bg-blue-700 shadow-lg shadow-blue-200 transition transform active:scale-95 flex justify-center items-center">
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
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
