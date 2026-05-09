<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <!-- Header: Info Siswa -->
    <div class="mb-6 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-blue-100 flex items-center justify-center text-blue-600 text-2xl font-extrabold shrink-0">
                    <?= mb_strtoupper(mb_substr($student['full_name'], 0, 1)) ?>
                </div>
                <div>
                    <h3 class="text-xl font-extrabold text-slate-800"><?= htmlspecialchars($student['full_name']) ?></h3>
                    <div class="flex flex-wrap items-center gap-2 mt-1">
                        <span class="text-xs font-mono bg-slate-100 text-slate-600 px-2 py-0.5 rounded border border-slate-200">NIS: <?= $student['nis'] ?></span>
                        <span class="text-xs bg-blue-50 text-blue-700 px-2 py-0.5 rounded border border-blue-100 font-semibold"><?= htmlspecialchars($student['class_name'] ?? 'Belum ada kelas') ?></span>
                        <span class="text-xs bg-<?= $student['status'] === 'ACTIVE' ? 'green' : 'slate' ?>-50 text-<?= $student['status'] === 'ACTIVE' ? 'green' : 'slate' ?>-700 px-2 py-0.5 rounded border border-<?= $student['status'] === 'ACTIVE' ? 'green' : 'slate' ?>-100 font-semibold"><?= $student['status'] ?></span>
                    </div>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <!-- Ringkasan Keuangan -->
                <div class="flex gap-3">
                    <div class="text-center px-4 py-2 bg-red-50 rounded-xl border border-red-100">
                        <div class="text-[10px] text-red-400 font-bold uppercase tracking-wider">Tunggakan</div>
                        <div class="text-sm font-extrabold text-red-700 font-mono">Rp <?= number_format($totalUnpaid, 0, ',', '.') ?></div>
                    </div>
                    <div class="text-center px-4 py-2 bg-green-50 rounded-xl border border-green-100">
                        <div class="text-[10px] text-green-400 font-bold uppercase tracking-wider">Terbayar</div>
                        <div class="text-sm font-extrabold text-green-700 font-mono">Rp <?= number_format($totalPaid, 0, ',', '.') ?></div>
                    </div>
                </div>
                <div class="flex gap-2">
                    <button onclick="document.getElementById('infoModal').classList.remove('hidden')"
                        class="w-9 h-9 rounded-xl bg-slate-100 text-slate-500 hover:bg-blue-100 hover:text-blue-600 flex items-center justify-center transition border border-slate-200" title="Panduan">
                        <i class="fa-solid fa-circle-info"></i>
                    </button>
                    <a href="/finance" class="px-4 py-2 bg-white text-slate-700 border border-slate-300 rounded-xl text-sm font-semibold hover:bg-slate-50 transition flex items-center gap-2">
                        <i class="fa-solid fa-arrow-left text-slate-400"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Form Tambah Tagihan -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 h-fit">
            <h4 class="font-bold text-slate-700 flex items-center gap-2 border-b border-slate-100 pb-4 mb-5">
                <i class="fa-solid fa-plus-circle text-blue-400"></i> Tambah Tagihan
            </h4>
            <form action="/finance/billing/create" method="POST" class="space-y-4">
                <?= \App\Core\Csrf::input() ?>
                <input type="hidden" name="student_nis" value="<?= htmlspecialchars($student['nis']) ?>">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Jenis Tagihan</label>
                    <select name="fee_type_id" id="feeTypeSelect" onchange="onFeeTypeChange(this)"
                        class="w-full select2-fee">
                        <option value="">-- Pilih atau Ketik Manual --</option>
                        <?php foreach ($feeTypes as $ft): ?>
                            <option value="<?= $ft['id'] ?>" data-amount="<?= $ft['amount'] ?>" data-name="<?= htmlspecialchars($ft['name']) ?>">
                                <?= htmlspecialchars($ft['name']) ?> — Rp <?= number_format($ft['amount'], 0, ',', '.') ?>
                            </option>
                        <?php endforeach; ?>
                        <option value="__manual__">+ Ketik Manual</option>
                    </select>
                </div>
                <div id="manualTitleWrap" class="hidden">
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nama Tagihan</label>
                    <input type="text" name="title" id="manualTitle" placeholder="cth: Biaya Ekskul"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nominal (Rp)</label>
                    <input type="number" name="amount" id="feeAmount" placeholder="cth: 350000"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Jatuh Tempo <span class="text-slate-400 font-normal">(Opsional)</span></label>
                    <input type="date" name="due_date"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Keterangan <span class="text-slate-400 font-normal">(Opsional)</span></label>
                    <textarea name="description" rows="2" placeholder="cth: SPP bulan Januari 2025"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none resize-none"></textarea>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2.5 rounded-xl font-bold hover:bg-blue-700 shadow-md shadow-blue-500/20 transition text-sm">
                    <i class="fa-solid fa-plus mr-2"></i> Buat Tagihan
                </button>
            </form>
        </div>

        <!-- Tabel Tagihan -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-5 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                    <h4 class="font-bold text-slate-700 flex items-center gap-2">
                        <i class="fa-solid fa-receipt text-slate-400"></i> Riwayat Tagihan
                    </h4>
                    <span class="text-xs text-slate-400"><?= count($bills) ?> tagihan</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-full whitespace-nowrap text-left">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal</th>
                                <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider">Keterangan</th>
                                <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Nominal</th>
                                <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Jatuh Tempo</th>
                                <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Status</th>
                                <th class="px-4 py-3 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            <?php if (empty($bills)): ?>
                                <tr><td colspan="6" class="px-5 py-16 text-center text-slate-400 text-sm">Belum ada tagihan untuk siswa ini.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($bills as $bill):
                                $billTitle  = $bill['fee_type_name'] ?? $bill['title'] ?? 'Tagihan #' . $bill['id'];
                                $billStatus = $bill['status'] ?? 'UNPAID';
                                $isOverdue  = $billStatus === 'UNPAID' && !empty($bill['due_date']) && $bill['due_date'] < date('Y-m-d');
                            ?>
                            <tr class="hover:bg-slate-50/80 transition-colors text-sm <?= $isOverdue ? 'bg-red-50/30' : '' ?>">
                                <td class="px-4 py-3 font-mono text-xs text-slate-500"><?= date('d/m/Y', strtotime($bill['created_at'])) ?></td>
                                <td class="px-4 py-3">
                                    <div class="font-bold text-slate-800"><?= htmlspecialchars($billTitle) ?></div>
                                    <?php if (!empty($bill['description'])): ?>
                                        <div class="text-[10px] text-slate-400"><?= htmlspecialchars($bill['description']) ?></div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-right font-mono font-bold text-slate-700">Rp <?= number_format($bill['amount'], 0, ',', '.') ?></td>
                                <td class="px-4 py-3 text-center text-xs <?= $isOverdue ? 'text-red-600 font-bold' : 'text-slate-500' ?>">
                                    <?= !empty($bill['due_date']) ? date('d M Y', strtotime($bill['due_date'])) : '-' ?>
                                    <?php if ($isOverdue): ?><div class="text-[9px] text-red-500 font-bold">JATUH TEMPO</div><?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <?php if ($billStatus === 'PAID'): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-green-50 text-green-700 rounded-full text-[10px] font-bold border border-green-200">
                                            <i class="fa-solid fa-circle-check"></i> LUNAS
                                        </span>
                                    <?php elseif (!empty($bill['payment_proof'])): ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-50 text-amber-700 rounded-full text-[10px] font-bold border border-amber-200">
                                            <i class="fa-solid fa-clock"></i> MENUNGGU
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-red-50 text-red-700 rounded-full text-[10px] font-bold border border-red-200">
                                            <i class="fa-solid fa-circle-xmark"></i> BELUM BAYAR
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <?php if ($billStatus === 'PAID'): ?>
                                            <a href="/finance/receipt?bill_id=<?= $bill['id'] ?>" target="_blank"
                                                class="w-8 h-8 rounded-lg bg-green-50 text-green-600 hover:bg-green-600 hover:text-white inline-flex items-center justify-center transition shadow-sm" title="Cetak Kuitansi">
                                                <i class="fa-solid fa-print text-xs"></i>
                                            </a>
                                        <?php elseif (!empty($bill['payment_proof'])): ?>
                                            <!-- Bukti sudah diupload, tunggu verifikasi admin -->
                                            <a href="/uploads/payments/<?= $bill['payment_proof'] ?>" target="_blank"
                                                class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white inline-flex items-center justify-center transition shadow-sm" title="Lihat Bukti Bayar">
                                                <i class="fa-solid fa-image text-xs"></i>
                                            </a>
                                            <form method="POST" action="/finance/billing/verify" class="inline">
                                                <?= \App\Core\Csrf::input() ?>
                                                <input type="hidden" name="bill_id" value="<?= $bill['id'] ?>">
                                                <input type="hidden" name="action" value="approve">
                                                <button type="submit" class="w-8 h-8 rounded-lg bg-green-50 text-green-600 hover:bg-green-600 hover:text-white inline-flex items-center justify-center transition shadow-sm" title="Setujui Pembayaran">
                                                    <i class="fa-solid fa-check text-xs"></i>
                                                </button>
                                            </form>
                                            <form method="POST" action="/finance/billing/verify" class="inline" onsubmit="return confirm('Tolak bukti bayar ini?')">
                                                <?= \App\Core\Csrf::input() ?>
                                                <input type="hidden" name="bill_id" value="<?= $bill['id'] ?>">
                                                <input type="hidden" name="action" value="reject">
                                                <button type="submit" class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white inline-flex items-center justify-center transition shadow-sm" title="Tolak Bukti Bayar">
                                                    <i class="fa-solid fa-xmark text-xs"></i>
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <button onclick="openCashModal(<?= $bill['id'] ?>, '<?= addslashes($billTitle) ?>', <?= $bill['amount'] ?>)"
                                                class="px-2.5 py-1.5 bg-blue-600 text-white rounded-lg text-xs font-bold hover:bg-blue-700 transition shadow-sm" title="Tandai Lunas">
                                                <i class="fa-solid fa-money-bill-wave mr-1"></i> Lunas
                                            </button>
                                            <a href="/finance/billing/delete?id=<?= $bill['id'] ?>"
                                                onclick="return confirm('Hapus tagihan ini?')"
                                                class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white inline-flex items-center justify-center transition shadow-sm" title="Hapus Tagihan">
                                                <i class="fa-solid fa-trash-can text-xs"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- Modal Tandai Lunas (Tunai/Transfer) -->
<div id="cashModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-700 flex items-center gap-2"><i class="fa-solid fa-money-bill-wave text-green-500"></i> Konfirmasi Pembayaran</h3>
            <button onclick="document.getElementById('cashModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-slate-200 text-slate-500 hover:bg-slate-300 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="/finance/billing/mark-paid" method="POST" class="p-6 space-y-4">
            <?= \App\Core\Csrf::input() ?>
            <input type="hidden" name="bill_id" id="cashBillId">
            <div class="bg-blue-50 p-4 rounded-xl border border-blue-100 text-sm text-blue-800">
                Tagihan: <strong id="cashBillTitle"></strong><br>
                Nominal: <strong id="cashBillAmount"></strong>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-2">Metode Pembayaran</label>
                <div class="grid grid-cols-2 gap-3">
                    <label class="flex items-center gap-3 p-3 border-2 border-blue-500 bg-blue-50 rounded-xl cursor-pointer">
                        <input type="radio" name="payment_method" value="CASH" checked class="accent-blue-600">
                        <span class="font-semibold text-blue-700 text-sm"><i class="fa-solid fa-money-bill mr-1"></i> Tunai</span>
                    </label>
                    <label class="flex items-center gap-3 p-3 border-2 border-slate-200 rounded-xl cursor-pointer hover:border-blue-300 transition">
                        <input type="radio" name="payment_method" value="TRANSFER" class="accent-blue-600">
                        <span class="font-semibold text-slate-700 text-sm"><i class="fa-solid fa-building-columns mr-1"></i> Transfer</span>
                    </label>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Catatan <span class="text-slate-400 font-normal">(Opsional)</span></label>
                <input type="text" name="notes" placeholder="cth: Bayar tunai di kasir"
                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('cashModal').classList.add('hidden')" class="flex-1 bg-slate-100 text-slate-600 py-2.5 rounded-xl font-bold hover:bg-slate-200 transition text-sm">Batal</button>
                <button type="submit" class="flex-1 bg-green-600 text-white py-2.5 rounded-xl font-bold hover:bg-green-700 shadow-md shadow-green-500/20 transition text-sm"><i class="fa-solid fa-check mr-1"></i> Tandai Lunas</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Info / Panduan -->
<div id="infoModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-blue-50 flex justify-between items-center">
            <h3 class="font-bold text-blue-800 flex items-center gap-2"><i class="fa-solid fa-circle-info text-blue-500"></i> Panduan Info Keuangan & Tagihan</h3>
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-blue-100 text-blue-500 hover:bg-blue-200 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="p-6 space-y-5 text-sm text-slate-600 overflow-y-auto max-h-[70vh]">

            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">1</span>
                    Membuat Tagihan Baru
                </h4>
                <ol class="list-decimal list-inside space-y-1.5 text-slate-500">
                    <li>Pilih <strong class="text-slate-700">Jenis Tagihan</strong> dari dropdown (diambil dari master Jenis Tagihan). Nominal akan terisi otomatis.</li>
                    <li>Pilih <strong class="text-slate-700">"+ Ketik Manual"</strong> jika jenis tagihan belum ada di master.</li>
                    <li>Isi <strong class="text-slate-700">Jatuh Tempo</strong> agar tagihan yang melewati tanggal ini ditandai merah otomatis.</li>
                    <li>Klik <strong class="text-slate-700">Buat Tagihan</strong>. Notifikasi WhatsApp otomatis dikirim ke orang tua.</li>
                </ol>
            </div>

            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">2</span>
                    Proses Pembayaran
                </h4>
                <div class="space-y-2">
                    <div class="flex items-start gap-3 p-2.5 bg-blue-50 rounded-xl border border-blue-100">
                        <i class="fa-solid fa-money-bill-wave text-blue-500 mt-0.5 w-4 shrink-0"></i>
                        <div><span class="font-semibold text-slate-700">Bayar Tunai/Transfer (Admin)</span> — Klik tombol <strong>Lunas</strong> pada tagihan, pilih metode, lalu konfirmasi. Kuitansi langsung bisa dicetak.</div>
                    </div>
                    <div class="flex items-start gap-3 p-2.5 bg-amber-50 rounded-xl border border-amber-100">
                        <i class="fa-solid fa-upload text-amber-500 mt-0.5 w-4 shrink-0"></i>
                        <div><span class="font-semibold text-slate-700">Upload Bukti (Orang Tua/Siswa)</span> — Jika orang tua upload bukti transfer dari portal mereka, status berubah ke <strong>MENUNGGU</strong>. Admin perlu klik <strong>✓ Setujui</strong> atau <strong>✗ Tolak</strong>.</div>
                    </div>
                </div>
            </div>

            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">3</span>
                    Status Tagihan
                </h4>
                <div class="flex flex-wrap gap-2">
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-red-50 text-red-700 rounded-full text-[10px] font-bold border border-red-200"><i class="fa-solid fa-circle-xmark"></i> BELUM BAYAR</span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-amber-50 text-amber-700 rounded-full text-[10px] font-bold border border-amber-200"><i class="fa-solid fa-clock"></i> MENUNGGU</span>
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-green-50 text-green-700 rounded-full text-[10px] font-bold border border-green-200"><i class="fa-solid fa-circle-check"></i> LUNAS</span>
                </div>
                <p class="text-xs text-slate-400 mt-2">Tagihan yang melewati jatuh tempo akan ditandai <strong class="text-red-500">JATUH TEMPO</strong> berwarna merah.</p>
            </div>

            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">4</span>
                    Cetak Kuitansi & Hapus
                </h4>
                <ul class="list-disc list-inside space-y-1.5 text-slate-500">
                    <li>Tagihan yang sudah <strong class="text-slate-700">LUNAS</strong> dapat dicetak kuitansinya dengan klik ikon <i class="fa-solid fa-print text-green-600"></i>.</li>
                    <li>Tagihan yang <strong class="text-slate-700">BELUM BAYAR</strong> dapat dihapus jika salah input (klik ikon <i class="fa-solid fa-trash-can text-red-500"></i>).</li>
                    <li>Tagihan yang sudah <strong class="text-slate-700">LUNAS tidak dapat dihapus</strong> untuk menjaga integritas data keuangan.</li>
                </ul>
            </div>

            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">5</span>
                    Relasi ke Menu Lain
                </h4>
                <div class="space-y-2">
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-list-check text-blue-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Jenis Tagihan</div><div class="text-[11px] text-slate-400">Kelola master jenis tagihan di <strong>Keuangan → Jenis Tagihan</strong>. Jenis yang ditambahkan akan muncul di dropdown form ini.</div></div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-chart-bar text-green-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Laporan Keuangan</div><div class="text-[11px] text-slate-400">Rekap semua pembayaran tersedia di <strong>Keuangan → Laporan</strong> dengan fitur export Excel/PDF.</div></div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-brands fa-whatsapp text-green-500 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Notifikasi WhatsApp</div><div class="text-[11px] text-slate-400">Notif otomatis ke orang tua saat tagihan dibuat. Blast manual tersedia di <strong>Keuangan → Laporan Bendahara</strong>.</div></div>
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
$(document).ready(function() {
    $('#feeTypeSelect').select2({
        placeholder: '-- Pilih atau Ketik Manual --',
        allowClear: true,
        width: '100%',
    }).on('change', function() { onFeeTypeChange(this); });
});

function openCashModal(id, title, amount) {
    document.getElementById('cashBillId').value = id;
    document.getElementById('cashBillTitle').innerText = title;
    document.getElementById('cashBillAmount').innerText = 'Rp ' + new Intl.NumberFormat('id-ID').format(amount);
    document.getElementById('cashModal').classList.remove('hidden');
}
function onFeeTypeChange(sel) {
    const opt = sel.options[sel.selectedIndex];
    const isManual = sel.value === '__manual__';
    document.getElementById('manualTitleWrap').classList.toggle('hidden', !isManual);
    document.getElementById('manualTitle').required = isManual;
    if (!isManual && opt.dataset.amount) {
        document.getElementById('feeAmount').value = opt.dataset.amount;
    }
    if (isManual) {
        document.getElementById('feeAmount').value = '';
        sel.value = ''; // reset ke kosong agar fee_type_id tidak terkirim
    }
}
// Radio button styling
document.querySelectorAll('input[name="payment_method"]').forEach(r => {
    r.addEventListener('change', () => {
        document.querySelectorAll('input[name="payment_method"]').forEach(x => {
            x.closest('label').className = x.checked
                ? 'flex items-center gap-3 p-3 border-2 border-blue-500 bg-blue-50 rounded-xl cursor-pointer'
                : 'flex items-center gap-3 p-3 border-2 border-slate-200 rounded-xl cursor-pointer hover:border-blue-300 transition';
        });
    });
});
window.onclick = function(e) {
    ['cashModal','infoModal'].forEach(id => {
        if (e.target == document.getElementById(id)) document.getElementById(id).classList.add('hidden');
    });
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
