<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <!-- Header -->
    <div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Inventaris Aset</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Manajemen sarana dan prasarana sekolah.</p>
            <div class="mt-3 flex flex-wrap items-center gap-2">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-green-50 text-green-700 rounded-lg text-xs font-bold border border-green-100">
                    <i class="fa-solid fa-boxes-stacked"></i> <?= $summary['total_item'] ?? 0 ?> Item | Rp <?= number_format($summary['total_asset'] ?? 0, 0, ',', '.') ?>
                </div>
                <?php if ($activeLoan > 0): ?>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold border border-blue-100">
                    <i class="fa-solid fa-hand-holding-box"></i> <?= $activeLoan ?> Dipinjam
                </div>
                <?php endif; ?>
                <?php if ($overdue > 0): ?>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-red-50 text-red-700 rounded-lg text-xs font-bold border border-red-100">
                    <i class="fa-solid fa-triangle-exclamation"></i> <?= $overdue ?> Terlambat
                </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="flex flex-wrap gap-2">
            <a href="/finance/inventory/loans" class="px-4 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-semibold shadow-md shadow-indigo-500/20 hover:bg-indigo-700 transition-all flex items-center gap-2">
                <i class="fa-solid fa-hand-holding-box"></i> Peminjaman
            </a>
            <a href="/finance/inventory/mutations" class="px-4 py-2.5 bg-amber-500 text-white rounded-xl text-sm font-semibold shadow-md shadow-amber-500/20 hover:bg-amber-600 transition-all flex items-center gap-2">
                <i class="fa-solid fa-clock-rotate-left"></i> Riwayat Mutasi
            </a>
            <a href="/finance/inventory/export" target="_blank" class="px-4 py-2.5 bg-emerald-600 text-white rounded-xl text-sm font-semibold shadow-md shadow-emerald-500/20 hover:bg-emerald-700 transition-all flex items-center gap-2">
                <i class="fa-solid fa-print"></i> Cetak Laporan
            </a>
            <form method="POST" action="/finance/inventory/notify-damaged" class="inline" onsubmit="return confirm('Kirim notifikasi WA kondisi aset rusak/hilang ke admin?')">
                <?= \App\Core\Csrf::input() ?>
                <button type="submit" class="px-4 py-2.5 bg-orange-500 text-white rounded-xl text-sm font-semibold shadow-md shadow-orange-500/20 hover:bg-orange-600 transition-all flex items-center gap-2">
                    <i class="fa-brands fa-whatsapp"></i> Notif Rusak/Hilang
                </button>
            </form>
            <button type="button" onclick="document.getElementById('addModal').classList.remove('hidden')"
                class="px-4 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold shadow-md shadow-blue-500/20 hover:bg-blue-700 transition-all flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Tambah Aset
            </button>
        </div>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <div class="flex flex-col gap-6">
        <!-- Filter -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
            <form method="GET" class="flex flex-wrap items-center gap-3">
                <div class="flex-1 min-w-[200px] relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nama / kode / merk..."
                        class="w-full pl-10 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
                </div>
                <select name="category_id" class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($categories as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $catId == $c['id'] ? 'selected' : '' ?>><?= $c['name'] ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="condition" class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                    <option value="">Semua Kondisi</option>
                    <option value="BAIK"         <?= $cond == 'BAIK'         ? 'selected' : '' ?>>Baik</option>
                    <option value="RUSAK_RINGAN"  <?= $cond == 'RUSAK_RINGAN' ? 'selected' : '' ?>>Rusak Ringan</option>
                    <option value="RUSAK_BERAT"   <?= $cond == 'RUSAK_BERAT'  ? 'selected' : '' ?>>Rusak Berat</option>
                    <option value="HILANG"        <?= $cond == 'HILANG'       ? 'selected' : '' ?>>Hilang</option>
                </select>
                <button type="submit" class="bg-slate-800 text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-slate-900 transition-colors">Terapkan</button>
                <?php if (!empty($search) || !empty($catId) || !empty($cond)): ?>
                    <a href="/finance/inventory" class="flex items-center justify-center w-10 h-10 bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition" title="Reset">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                <?php endif; ?>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
            <div class="overflow-x-auto">
                <table class="min-w-full whitespace-nowrap text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Kode & Nama</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Kategori & Merk</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Lokasi</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Jml</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-right">Nilai Aset</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Kondisi</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <?php if (empty($items)): ?>
                            <tr><td colspan="7" class="px-5 py-16 text-center text-slate-400 text-sm font-medium">Data tidak ditemukan.</td></tr>
                        <?php endif; ?>
                        <?php
                        $condColors = [
                            'BAIK'        => 'bg-green-50 text-green-700 border-green-200',
                            'RUSAK_RINGAN'=> 'bg-yellow-50 text-yellow-700 border-yellow-200',
                            'RUSAK_BERAT' => 'bg-red-50 text-red-700 border-red-200',
                            'HILANG'      => 'bg-slate-100 text-slate-500 border-slate-200',
                        ];
                        ?>
                        <?php foreach ($items as $i): ?>
                        <tr class="hover:bg-slate-50/80 transition-colors text-sm">
                            <td class="px-5 py-4">
                                <div class="font-extrabold text-slate-800"><?= htmlspecialchars($i['name']) ?></div>
                                <span class="text-[10px] font-mono text-blue-600 bg-blue-50 px-1.5 py-0.5 rounded border border-blue-100"><?= $i['code'] ?></span>
                            </td>
                            <td class="px-5 py-4 text-xs">
                                <div class="font-semibold text-slate-700"><?= $i['category_name'] ?></div>
                                <div class="text-slate-400"><?= $i['brand'] ?? '-' ?></div>
                            </td>
                            <td class="px-5 py-4 text-xs text-slate-600"><?= $i['location'] ?? '-' ?></td>
                            <td class="px-5 py-4 text-center font-bold text-slate-700"><?= $i['quantity'] ?></td>
                            <td class="px-5 py-4 text-right">
                                <div class="text-[10px] text-slate-400">@ <?= number_format($i['price'] ?? 0, 0, ',', '.') ?></div>
                                <div class="font-bold text-slate-800 font-mono text-xs">Rp <?= number_format(($i['price'] ?? 0) * ($i['quantity'] ?? 0), 0, ',', '.') ?></div>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg border <?= $condColors[$i['condition_status']] ?? 'bg-slate-100 text-slate-500 border-slate-200' ?>">
                                    <?= str_replace('_', ' ', $i['condition_status']) ?>
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="/finance/inventory/mutations?item_id=<?= $i['id'] ?>"
                                        class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 hover:bg-amber-500 hover:text-white inline-flex items-center justify-center transition-colors shadow-sm" title="Riwayat Mutasi">
                                        <i class="fa-solid fa-clock-rotate-left text-xs"></i>
                                    </a>
                                    <button onclick='editItem(<?= json_encode($i) ?>)'
                                        class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white inline-flex items-center justify-center transition-colors shadow-sm" title="Edit">
                                        <i class="fa-solid fa-pen-to-square text-sm"></i>
                                    </button>
                                    <a href="/finance/inventory/delete?id=<?= $i['id'] ?>"
                                        onclick="return confirm('Hapus barang ini?')"
                                        class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white inline-flex items-center justify-center transition-colors shadow-sm" title="Hapus">
                                        <i class="fa-solid fa-trash-can text-sm"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <!-- Pagination -->
            <div class="p-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between gap-4">
                <span class="text-xs text-slate-500">Halaman <?= $currentPage ?> / <?= $totalPages ?></span>
                <?php if ($totalPages > 1): ?>
                <div class="flex items-center gap-1.5">
                    <?php $qs = "&search=" . urlencode($search) . "&category_id=$catId&condition=$cond"; ?>
                    <?php if ($currentPage > 1): ?>
                        <a href="?page=<?= $currentPage - 1 . $qs ?>" class="w-8 h-8 flex items-center justify-center bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors shadow-sm"><i class="fa-solid fa-chevron-left"></i></a>
                    <?php endif; ?>
                    <?php if ($currentPage < $totalPages): ?>
                        <a href="?page=<?= $currentPage + 1 . $qs ?>" class="w-8 h-8 flex items-center justify-center bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors shadow-sm"><i class="fa-solid fa-chevron-right"></i></a>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<!-- Modal Tambah/Edit -->
<div id="addModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden max-h-[90vh] flex flex-col">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-700" id="modalTitle">Tambah Aset Baru</h3>
            <button onclick="closeModal()" class="w-8 h-8 rounded-lg bg-slate-200 text-slate-500 hover:bg-slate-300 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="/finance/inventory/store" method="POST" id="inventoryForm" class="p-6 overflow-y-auto space-y-4">
            <?= \App\Core\Csrf::input() ?>
            <input type="hidden" name="id" id="inpId">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Kode Barang</label>
                    <input type="text" name="code" id="inpCode" placeholder="cth: INV-2025-001"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nama Barang</label>
                    <input type="text" name="name" id="inpName" placeholder="cth: Laptop Acer Aspire"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" required>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Kategori</label>
                    <select name="category_id" id="inpCat" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none" required>
                        <?php foreach ($categories as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Merk / Model</label>
                    <input type="text" name="brand" id="inpBrand" placeholder="cth: Acer, Samsung"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
                </div>
            </div>
            <div class="grid grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Jumlah</label>
                    <input type="number" name="quantity" id="inpQty" value="1" min="1"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm text-center focus:bg-white outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Harga Satuan (Rp)</label>
                    <input type="number" name="price" id="inpPrice" value="0"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Kondisi</label>
                    <select name="condition_status" id="inpCond" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                        <option value="BAIK">Baik</option>
                        <option value="RUSAK_RINGAN">Rusak Ringan</option>
                        <option value="RUSAK_BERAT">Rusak Berat</option>
                        <option value="HILANG">Hilang</option>
                    </select>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Sumber Dana</label>
                    <input type="text" name="source_fund" id="inpSource" placeholder="cth: BOS, Yayasan"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Tanggal Pengadaan</label>
                    <input type="date" name="acquisition_date" id="inpDate" value="<?= date('Y-m-d') ?>"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Lokasi Penyimpanan</label>
                <input type="text" name="location" id="inpLoc" placeholder="cth: Lab Komputer 1, Ruang Guru"
                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
            </div>
            <!-- Catatan mutasi (hanya tampil saat edit) -->
            <div id="mutationNotesWrap" class="hidden">
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Catatan Perubahan Kondisi <span class="text-slate-400 font-normal">(Opsional)</span></label>
                <input type="text" name="mutation_notes" id="inpMutNotes" placeholder="cth: Layar retak akibat terjatuh"
                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Deskripsi / Spesifikasi <span class="text-slate-400 font-normal">(Opsional)</span></label>
                <textarea name="description" id="inpDesc" rows="2" placeholder="cth: Core i5, RAM 8GB, SSD 256GB"
                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all resize-none"></textarea>
            </div>
            <div class="flex gap-3 pt-2 border-t border-slate-100">
                <button type="button" onclick="closeModal()" class="flex-1 bg-slate-100 text-slate-600 py-2.5 rounded-xl font-bold hover:bg-slate-200 transition text-sm">Batal</button>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2.5 rounded-xl font-bold hover:bg-blue-700 shadow-md shadow-blue-500/20 transition text-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function closeModal() {
    document.getElementById('addModal').classList.add('hidden');
    document.getElementById('inventoryForm').reset();
    document.getElementById('modalTitle').innerText = 'Tambah Aset Baru';
    document.getElementById('inventoryForm').action = '/finance/inventory/store';
    document.getElementById('inpCode').readOnly = false;
    document.getElementById('inpCode').classList.remove('bg-slate-200');
    document.getElementById('mutationNotesWrap').classList.add('hidden');
}
function editItem(item) {
    document.getElementById('addModal').classList.remove('hidden');
    document.getElementById('modalTitle').innerText = 'Edit Data Aset';
    document.getElementById('inventoryForm').action = '/finance/inventory/update';
    document.getElementById('inpId').value = item.id;
    document.getElementById('inpCode').value = item.code;
    document.getElementById('inpCode').readOnly = true;
    document.getElementById('inpCode').classList.add('bg-slate-200');
    document.getElementById('inpName').value = item.name;
    document.getElementById('inpCat').value = item.category_id;
    document.getElementById('inpBrand').value = item.brand;
    document.getElementById('inpQty').value = item.quantity;
    document.getElementById('inpPrice').value = item.price;
    document.getElementById('inpCond').value = item.condition_status;
    document.getElementById('inpSource').value = item.source_fund;
    document.getElementById('inpDate').value = item.acquisition_date;
    document.getElementById('inpLoc').value = item.location;
    document.getElementById('inpDesc').value = item.description;
    document.getElementById('mutationNotesWrap').classList.remove('hidden');
}
window.onclick = function(e) {
    if (e.target == document.getElementById('addModal')) closeModal();
}
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
