<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">
    <div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-center gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <div class="flex items-center gap-2 text-sm text-slate-400 mb-1">
                <a href="/finance/inventory" class="hover:text-blue-600 transition">Inventaris Aset</a>
                <i class="fa-solid fa-chevron-right text-xs"></i>
                <span class="text-slate-600 font-semibold">Peminjaman Barang</span>
            </div>
            <h3 class="text-2xl font-extrabold text-slate-800">Peminjaman Barang</h3>
            <p class="text-slate-500 text-sm mt-1">Catat dan pantau peminjaman aset oleh guru, staf, atau santri.</p>
        </div>
        <div class="flex gap-2">
            <a href="/finance/inventory" class="px-4 py-2.5 bg-slate-100 text-slate-700 rounded-xl text-sm font-semibold hover:bg-slate-200 transition flex items-center gap-2">
                <i class="fa-solid fa-arrow-left"></i> Kembali
            </a>
            <button onclick="document.getElementById('loanModal').classList.remove('hidden')"
                class="px-4 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-semibold shadow-md shadow-indigo-500/20 hover:bg-indigo-700 transition flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Catat Peminjaman
            </button>
        </div>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <!-- Filter Status -->
    <div class="mb-4 flex flex-wrap gap-2">
        <?php
        $statuses = ['' => 'Semua', 'DIPINJAM' => 'Dipinjam', 'TERLAMBAT' => 'Terlambat', 'DIKEMBALIKAN' => 'Dikembalikan'];
        $statusColors = ['' => 'bg-slate-800 text-white', 'DIPINJAM' => 'bg-blue-600 text-white', 'TERLAMBAT' => 'bg-red-600 text-white', 'DIKEMBALIKAN' => 'bg-green-600 text-white'];
        $statusInactive = 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50';
        foreach ($statuses as $val => $label):
        ?>
        <a href="?status=<?= $val ?>" class="px-4 py-2 rounded-xl text-sm font-semibold transition <?= $status === $val ? ($statusColors[$val] ?? 'bg-slate-800 text-white') : $statusInactive ?>">
            <?= $label ?>
        </a>
        <?php endforeach; ?>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full whitespace-nowrap text-left">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Barang</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Peminjam</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Jml</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Tgl Pinjam</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Batas Kembali</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Tgl Kembali</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Status</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php if (empty($loans)): ?>
                        <tr><td colspan="8" class="px-5 py-16 text-center text-slate-400 text-sm">Belum ada data peminjaman.</td></tr>
                    <?php endif; ?>
                    <?php
                    $loanColors = [
                        'DIPINJAM'     => 'bg-blue-50 text-blue-700 border-blue-200',
                        'TERLAMBAT'    => 'bg-red-50 text-red-700 border-red-200',
                        'DIKEMBALIKAN' => 'bg-green-50 text-green-700 border-green-200',
                    ];
                    ?>
                    <?php foreach ($loans as $l): ?>
                    <tr class="hover:bg-slate-50/80 transition-colors text-sm">
                        <td class="px-5 py-4">
                            <div class="font-bold text-slate-800"><?= htmlspecialchars($l['item_name']) ?></div>
                            <span class="text-[10px] font-mono text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-100"><?= $l['item_code'] ?></span>
                        </td>
                        <td class="px-5 py-4">
                            <div class="font-semibold text-slate-700"><?= htmlspecialchars($l['borrower_name']) ?></div>
                            <div class="text-xs text-slate-400"><?= $l['borrower_role'] ?></div>
                        </td>
                        <td class="px-5 py-4 text-center font-bold text-slate-700"><?= $l['quantity'] ?></td>
                        <td class="px-5 py-4 text-xs text-slate-600"><?= date('d M Y', strtotime($l['loan_date'])) ?></td>
                        <td class="px-5 py-4 text-xs <?= $l['status'] === 'TERLAMBAT' ? 'text-red-600 font-bold' : 'text-slate-600' ?>"><?= date('d M Y', strtotime($l['due_date'])) ?></td>
                        <td class="px-5 py-4 text-xs text-slate-500"><?= $l['return_date'] ? date('d M Y', strtotime($l['return_date'])) : '-' ?></td>
                        <td class="px-5 py-4 text-center">
                            <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg border <?= $loanColors[$l['status']] ?? '' ?>">
                                <?= $l['status'] ?>
                            </span>
                        </td>
                        <td class="px-5 py-4 text-center">
                            <?php if (in_array($l['status'], ['DIPINJAM', 'TERLAMBAT'])): ?>
                            <form method="POST" action="/finance/inventory/loans/return" onsubmit="return confirm('Tandai barang ini sudah dikembalikan?')">
                                <?= \App\Core\Csrf::input() ?>
                                <input type="hidden" name="id" value="<?= $l['id'] ?>">
                                <button type="submit" class="px-3 py-1.5 bg-green-50 text-green-700 hover:bg-green-600 hover:text-white rounded-lg text-xs font-bold transition border border-green-200">
                                    <i class="fa-solid fa-check mr-1"></i> Kembalikan
                                </button>
                            </form>
                            <?php else: ?>
                                <span class="text-xs text-slate-400">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php if ($totalPages > 1): ?>
        <div class="p-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
            <span class="text-xs text-slate-500">Halaman <?= $currentPage ?> / <?= $totalPages ?></span>
            <div class="flex gap-1.5">
                <?php if ($currentPage > 1): ?>
                    <a href="?page=<?= $currentPage - 1 ?>&status=<?= $status ?>" class="w-8 h-8 flex items-center justify-center bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors shadow-sm"><i class="fa-solid fa-chevron-left"></i></a>
                <?php endif; ?>
                <?php if ($currentPage < $totalPages): ?>
                    <a href="?page=<?= $currentPage + 1 ?>&status=<?= $status ?>" class="w-8 h-8 flex items-center justify-center bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors shadow-sm"><i class="fa-solid fa-chevron-right"></i></a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</main>

<!-- Modal Catat Peminjaman -->
<div id="loanModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-700">Catat Peminjaman Baru</h3>
            <button onclick="document.getElementById('loanModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-slate-200 text-slate-500 hover:bg-slate-300 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="/finance/inventory/loans/store" method="POST" class="p-6 space-y-4">
            <?= \App\Core\Csrf::input() ?>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Barang</label>
                <select name="item_id" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none" required>
                    <option value="">-- Pilih Barang --</option>
                    <?php foreach ($items as $it): ?>
                        <option value="<?= $it['id'] ?>"><?= htmlspecialchars($it['name']) ?> (<?= $it['code'] ?>) — Stok: <?= $it['quantity'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nama Peminjam</label>
                    <input type="text" name="borrower_name" placeholder="Nama lengkap" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Jabatan / Peran</label>
                    <select name="borrower_role" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none" required>
                        <option value="Guru">Guru</option>
                        <option value="Staf">Staf</option>
                        <option value="Santri">Santri</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Jumlah</label>
                    <input type="number" name="quantity" value="1" min="1" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-center focus:bg-white outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Tgl Pinjam</label>
                    <input type="date" name="loan_date" value="<?= date('Y-m-d') ?>" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Batas Kembali</label>
                    <input type="date" name="due_date" value="<?= date('Y-m-d', strtotime('+7 days')) ?>" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none" required>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Catatan <span class="text-slate-400 font-normal">(Opsional)</span></label>
                <input type="text" name="notes" placeholder="cth: Untuk kegiatan ujian" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none">
            </div>
            <div class="flex gap-3 pt-2 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('loanModal').classList.add('hidden')" class="flex-1 bg-slate-100 text-slate-600 py-2.5 rounded-xl font-bold hover:bg-slate-200 transition text-sm">Batal</button>
                <button type="submit" class="flex-1 bg-indigo-600 text-white py-2.5 rounded-xl font-bold hover:bg-indigo-700 shadow-md shadow-indigo-500/20 transition text-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
window.onclick = function(e) {
    if (e.target == document.getElementById('loanModal')) document.getElementById('loanModal').classList.add('hidden');
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
