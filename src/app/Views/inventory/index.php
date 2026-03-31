<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <div class="mb-6 flex justify-between items-end">
        <div>
            <h3 class="text-3xl font-medium text-gray-700">Inventaris Aset</h3>
            <p class="text-sm text-gray-500">Manajemen Sarana dan Prasarana Sekolah.</p>
        </div>
        
        <div class="text-right">
            <div class="text-xs text-gray-500 uppercase font-bold">Total Nilai Aset</div>
            <div class="text-2xl font-bold text-green-600">Rp <?= number_format($summary['total_asset'] ?? 0, 0, ',', '.') ?></div>
            <div class="text-xs text-gray-400"><?= $summary['total_item'] ?> Item Terdaftar</div>
        </div>
    </div>
    
    <?php \App\Core\Session::flash(); ?>

    <div class="bg-white p-4 rounded shadow-sm mb-4 border border-gray-200">
        <form class="flex flex-wrap gap-4 items-center">
            <input type="text" name="search" value="<?= $search ?>" placeholder="Cari Nama / Kode / Merk..." class="p-2 border rounded text-sm w-64">
            
            <select name="category_id" class="p-2 border rounded text-sm">
                <option value="">Semua Kategori</option>
                <?php foreach($categories as $c): ?>
                    <option value="<?= $c['id'] ?>" <?= $catId == $c['id'] ? 'selected' : '' ?>><?= $c['name'] ?></option>
                <?php endforeach; ?>
            </select>

            <select name="condition" class="p-2 border rounded text-sm">
                <option value="">Semua Kondisi</option>
                <option value="BAIK" <?= $cond == 'BAIK' ? 'selected' : '' ?>>Baik</option>
                <option value="RUSAK_RINGAN" <?= $cond == 'RUSAK_RINGAN' ? 'selected' : '' ?>>Rusak Ringan</option>
                <option value="RUSAK_BERAT" <?= $cond == 'RUSAK_BERAT' ? 'selected' : '' ?>>Rusak Berat</option>
                <option value="HILANG" <?= $cond == 'HILANG' ? 'selected' : '' ?>>Hilang</option>
            </select>

            <button class="bg-gray-800 text-white px-4 py-2 rounded text-sm">Filter</button>
            <a href="/finance/inventory" class="text-red-500 text-sm flex items-center">Reset</a>

            <div class="ml-auto">
                <button type="button" onclick="document.getElementById('addModal').classList.remove('hidden')" class="bg-blue-600 text-white px-4 py-2 rounded text-sm shadow hover:bg-blue-700">
                    <i class="fa fa-plus"></i> Tambah Aset
                </button>
            </div>
        </form>
    </div>

    <div class="bg-white rounded shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm">
                <thead class="bg-gray-100 text-gray-600 uppercase text-xs">
                    <tr>
                        <th class="px-4 py-3 text-left">Kode & Nama Barang</th>
                        <th class="px-4 py-3 text-left">Kategori & Merk</th>
                        <th class="px-4 py-3 text-left">Lokasi</th>
                        <th class="px-4 py-3 text-center">Jumlah</th>
                        <th class="px-4 py-3 text-right">Nilai Aset</th>
                        <th class="px-4 py-3 text-center">Kondisi</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <?php if(empty($items)): ?>
                        <tr><td colspan="7" class="p-6 text-center text-gray-400">Data tidak ditemukan.</td></tr>
                    <?php endif; ?>

                    <?php foreach($items as $i): ?>
                    <tr class="hover:bg-gray-50 transition-colors duration-200">
                        <td class="px-4 py-3">
                            <div class="font-bold text-gray-800"><?= $i['name'] ?></div>
                            <div class="text-xs font-mono text-blue-600 bg-blue-50 inline-block px-1 rounded"><?= $i['code'] ?></div>
                        </td>
                        <td class="px-4 py-3 text-xs">
                            <div class="font-bold text-gray-600"><?= $i['category_name'] ?></div>
                            <div class="text-gray-500"><?= $i['brand'] ?? '-' ?></div>
                        </td>
                        <td class="px-4 py-3 text-xs text-gray-600">
                            <?= $i['location'] ?? '-' ?>
                        </td>
                        <td class="px-4 py-3 text-center font-bold">
                            <?= $i['quantity'] ?>
                        </td>
                        <td class="px-4 py-3 text-right">
                            <div class="text-xs text-gray-500">@ <?= number_format($i['price'] ?? 0, 0, ',', '.') ?></div>
                            <div class="font-bold text-gray-800">Rp <?= number_format(($i['price'] ?? 0) * ($i['quantity'] ?? 0), 0, ',', '.') ?></div>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <?php 
                                $bgCond = 'bg-gray-100 text-gray-600';
                                if($i['condition_status'] == 'BAIK') $bgCond = 'bg-green-100 text-green-700';
                                if($i['condition_status'] == 'RUSAK_RINGAN') $bgCond = 'bg-yellow-100 text-yellow-700';
                                if($i['condition_status'] == 'RUSAK_BERAT') $bgCond = 'bg-red-100 text-red-700';
                            ?>
                            <span class="px-2 py-1 rounded text-[10px] font-bold <?= $bgCond ?>">
                                <?= str_replace('_', ' ', $i['condition_status']) ?>
                            </span>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-2">
                                <button onclick='editItem(<?= json_encode($i) ?>)' class="text-blue-500 hover:text-blue-700" title="Edit"><i class="fa fa-edit"></i></button>
                                <a href="/finance/inventory/delete?id=<?= $i['id'] ?>" class="text-red-500 hover:text-red-700" onclick="return confirm('Hapus barang ini?')" title="Hapus"><i class="fa fa-trash"></i></a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if($totalPages > 1): ?>
        <div class="p-4 bg-gray-50 border-t flex justify-between items-center text-xs">
            <span>Hal <?= $currentPage ?> dari <?= $totalPages ?></span>
            <div class="flex gap-1">
                <?php if($currentPage > 1): ?>
                    <a href="?page=<?= $currentPage - 1 ?>&search=<?= $search ?>" class="px-3 py-1 bg-white border rounded">Prev</a>
                <?php endif; ?>
                <?php if($currentPage < $totalPages): ?>
                    <a href="?page=<?= $currentPage + 1 ?>&search=<?= $search ?>" class="px-3 py-1 bg-white border rounded">Next</a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>
</main>

<div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
    <div class="bg-white p-6 rounded-lg w-full max-w-2xl shadow-xl overflow-y-auto max-h-[90vh]">
        <h3 class="text-xl font-bold mb-4" id="modalTitle">Tambah Aset Baru</h3>
        <form action="/finance/inventory/store" method="POST" id="inventoryForm">
            <?= \App\Core\Csrf::input() ?>
            <input type="hidden" name="id" id="inpId">

            <div class="grid grid-cols-2 gap-4 mb-3">
                <div>
                    <label class="block text-xs font-bold mb-1">Kode Barang / Barcode</label>
                    <input type="text" name="code" id="inpCode" class="w-full p-2 border rounded bg-gray-50" required placeholder="Contoh: INV-2025-001">
                </div>
                <div>
                    <label class="block text-xs font-bold mb-1">Nama Barang</label>
                    <input type="text" name="name" id="inpName" class="w-full p-2 border rounded" required placeholder="Contoh: Laptop Acer">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-3">
                <div>
                    <label class="block text-xs font-bold mb-1">Kategori</label>
                    <select name="category_id" id="inpCat" class="w-full p-2 border rounded" required>
                        <?php foreach($categories as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold mb-1">Merk / Model</label>
                    <input type="text" name="brand" id="inpBrand" class="w-full p-2 border rounded">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4 mb-3">
                <div>
                    <label class="block text-xs font-bold mb-1">Jumlah</label>
                    <input type="number" name="quantity" id="inpQty" value="1" min="1" class="w-full p-2 border rounded text-center" required>
                </div>
                <div>
                    <label class="block text-xs font-bold mb-1">Harga Satuan (Rp)</label>
                    <input type="number" name="price" id="inpPrice" value="0" class="w-full p-2 border rounded">
                </div>
                <div>
                    <label class="block text-xs font-bold mb-1">Kondisi</label>
                    <select name="condition_status" id="inpCond" class="w-full p-2 border rounded">
                        <option value="BAIK">Baik</option>
                        <option value="RUSAK_RINGAN">Rusak Ringan</option>
                        <option value="RUSAK_BERAT">Rusak Berat</option>
                        <option value="HILANG">Hilang</option>
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-3">
                <div>
                    <label class="block text-xs font-bold mb-1">Sumber Dana</label>
                    <input type="text" name="source_fund" id="inpSource" class="w-full p-2 border rounded" placeholder="BOS / Yayasan">
                </div>
                <div>
                    <label class="block text-xs font-bold mb-1">Tanggal Pengadaan</label>
                    <input type="date" name="acquisition_date" id="inpDate" value="<?= date('Y-m-d') ?>" class="w-full p-2 border rounded">
                </div>
            </div>

            <div class="mb-3">
                <label class="block text-xs font-bold mb-1">Lokasi Penyimpanan</label>
                <input type="text" name="location" id="inpLoc" class="w-full p-2 border rounded" placeholder="Contoh: Lab Komputer 1">
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold mb-1">Deskripsi / Spesifikasi</label>
                <textarea name="description" id="inpDesc" class="w-full p-2 border rounded h-20"></textarea>
            </div>

            <div class="flex justify-end gap-2 pt-4 border-t">
                <button type="button" onclick="closeModal()" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700 font-bold">Simpan</button>
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
        document.getElementById('inpCode').classList.remove('bg-gray-200');
    }

    function editItem(item) {
        document.getElementById('addModal').classList.remove('hidden');
        document.getElementById('modalTitle').innerText = 'Edit Data Aset';
        document.getElementById('inventoryForm').action = '/finance/inventory/update';
        
        document.getElementById('inpId').value = item.id;
        document.getElementById('inpCode').value = item.code;
        document.getElementById('inpCode').readOnly = true; 
        document.getElementById('inpCode').classList.add('bg-gray-200');
        
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
    }
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
