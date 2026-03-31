<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-50 p-6">
    
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-800">Info Keuangan & Tagihan</h1>
            <p class="text-gray-600">
                Data Tagihan untuk: <span class="font-bold text-blue-600"><?= $_GET['nis'] ?? '-' ?></span>
            </p>
        </div>
        <a href="/student/dashboard" class="text-gray-500 hover:text-gray-700">
            <i class="fa-solid fa-arrow-left"></i> Kembali
        </a>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <?php if ($_SESSION['user_role'] != 'siswa'): ?>
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="font-bold text-gray-700 mb-4 border-b pb-2">
                    <i class="fa-solid fa-plus-circle mr-2"></i> Tambah Tagihan Manual
                </h3>
                
                <form action="/finance/billing/create" method="POST">
                    <input type="hidden" name="student_nis" value="<?= $_GET['nis'] ?? '' ?>">
                    
                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-600 mb-2">Jenis Tagihan</label>
                        <select name="title" class="w-full border rounded px-3 py-2 text-sm">
                            <option value="SPP Bulan Ini">SPP Bulan Ini</option>
                            <option value="Uang Gedung">Uang Gedung</option>
                            <option value="Daftar Ulang">Daftar Ulang</option>
                            <option value="Buku Paket">Buku Paket</option>
                            <option value="Lainnya">Lainnya</option>
                        </select>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-600 mb-2">Nominal (Rp)</label>
                        <input type="number" name="amount" class="w-full border rounded px-3 py-2" placeholder="Contoh: 350000" required>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-bold text-gray-600 mb-2">Keterangan (Opsional)</label>
                        <textarea name="description" class="w-full border rounded px-3 py-2 text-sm" rows="2"></textarea>
                    </div>

                    <button type="submit" class="w-full bg-blue-600 text-white font-bold py-2 rounded hover:bg-blue-700 transition">
                        Buat Tagihan
                    </button>
                </form>
            </div>
        </div>
        <?php endif; ?>

        <div class="<?= ($_SESSION['user_role'] == 'siswa') ? 'lg:col-span-3' : 'lg:col-span-2' ?>">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="px-6 py-4 border-b bg-gray-50 flex justify-between items-center">
                    <h3 class="font-bold text-gray-700">Riwayat Tagihan</h3>
                    
                    <?php 
                        $totalUnpaid = 0;
                        if(isset($bills)) {
                            foreach($bills as $b) {
                                if($b['status'] == 'UNPAID') $totalUnpaid += $b['amount'];
                            }
                        }
                    ?>
                    <?php if($totalUnpaid > 0): ?>
                        <span class="bg-red-100 text-red-600 px-3 py-1 rounded-full text-xs font-bold">
                            Total Belum Bayar: Rp <?= number_format($totalUnpaid, 0, ',', '.') ?>
                        </span>
                    <?php else: ?>
                        <span class="bg-green-100 text-green-600 px-3 py-1 rounded-full text-xs font-bold">
                            Lunas Semua
                        </span>
                    <?php endif; ?>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                            <tr>
                                <th class="px-6 py-3">Tanggal</th>
                                <th class="px-6 py-3">Keterangan</th>
                                <th class="px-6 py-3 text-right">Nominal</th>
                                <th class="px-6 py-3 text-center">Status</th>
                                <th class="px-6 py-3 text-center">Aksi</th>
                            </tr>
                        </thead>
<tbody class="divide-y divide-gray-200">
                            <?php if (!empty($bills)): ?>
                                <?php foreach ($bills as $bill): ?>
                                
                                <?php 
                                    // PERBAIKAN: Definisikan variabel dengan fallback agar tidak error
                                    $billTitle = $bill['title'] ?? $bill['name'] ?? 'Tagihan #' . $bill['id'];
                                    $billDesc  = $bill['description'] ?? '-';
                                    $billAmount = $bill['amount'] ?? 0;
                                    $billId = $bill['id'];
                                ?>

                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-gray-500">
                                        <?= date('d/m/Y', strtotime($bill['created_at'] ?? 'now')) ?>
                                    </td>
                                    <td class="px-6 py-4 font-medium text-gray-800">
                                        <?= $billTitle ?>
                                        <div class="text-xs text-gray-400"><?= $billDesc ?></div>
                                    </td>
                                    <td class="px-6 py-4 text-right font-mono text-gray-700">
                                        Rp <?= number_format($billAmount, 0, ',', '.') ?>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <?php if (($bill['status'] ?? 'UNPAID') == 'PAID'): ?>
                                            <span class="bg-green-100 text-green-700 px-2 py-1 rounded text-xs font-bold">LUNAS</span>
                                        <?php else: ?>
                                            <span class="bg-red-100 text-red-700 px-2 py-1 rounded text-xs font-bold">BELUM BAYAR</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-center">
                                        <?php if (($bill['status'] ?? 'UNPAID') == 'UNPAID'): ?>
                                            <button onclick="openPaymentModal(<?= $billId ?>, '<?= addslashes($billTitle) ?>', <?= $billAmount ?>)" 
                                                class="bg-blue-600 text-white px-3 py-1 rounded text-xs hover:bg-blue-700 transition">
                                                <i class="fa-solid fa-upload mr-1"></i> Bayar
                                            </button>
                                        <?php else: ?>
                                            <button class="text-gray-400 cursor-not-allowed text-xs">
                                                <i class="fa-solid fa-check"></i> Selesai
                                            </button>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-400">
                                        Belum ada tagihan untuk siswa ini.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div id="paymentModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-md">
            <div class="px-6 py-4 border-b flex justify-between items-center">
                <h3 class="font-bold text-gray-800">Konfirmasi Pembayaran</h3>
                <button onclick="document.getElementById('paymentModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
            
            <form action="/finance/pay" method="POST" enctype="multipart/form-data" class="p-6">
                <input type="hidden" name="bill_id" id="modalBillId">
                
                <div class="bg-blue-50 p-3 rounded mb-4 text-sm text-blue-800">
                    Bayar tagihan: <b id="modalBillTitle"></b><br>
                    Total: <b id="modalBillAmount"></b>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-bold text-gray-600 mb-2">Bukti Transfer</label>
                    <input type="file" name="payment_proof" required class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                </div>

                <button type="submit" class="w-full bg-green-600 text-white font-bold py-2 rounded hover:bg-green-700">
                    Kirim Bukti Bayar
                </button>
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
    </script>

</main>
<?php require __DIR__ . '/../layouts/footer.php'; ?>
