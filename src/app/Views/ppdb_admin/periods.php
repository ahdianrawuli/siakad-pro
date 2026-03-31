<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<style>
    .custom-scrollbar::-webkit-scrollbar { height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
</style>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <div class="mb-6 flex justify-between items-end">
        <div>
            <h3 class="text-3xl font-medium text-gray-700">Periode PPDB</h3>
            <p class="text-gray-500 text-sm">Total <?= $totalData ?> gelombang pendaftaran.</p>
        </div>
    </div>
    
    <?php \App\Core\Session::flash(); ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded shadow-sm h-fit border border-gray-200">
            <h4 class="font-bold mb-4 text-gray-700 border-b pb-2 text-[10px] uppercase tracking-wider flex items-center">
                <i class="fa-solid fa-plus-circle mr-2 text-blue-600"></i> Buat Periode
            </h4>
            <form action="/ppdb/periods/store" method="POST" class="space-y-4">
                <?= \App\Core\Csrf::input() ?>
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Nama Gelombang</label>
                    <input type="text" name="name" placeholder="Gelombang 1 2025" class="w-full p-2.5 border border-gray-300 rounded text-sm outline-none focus:ring-1 focus:ring-blue-500" required>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Tgl Buka</label>
                        <input type="date" name="start_date" class="w-full p-2.5 border border-gray-300 rounded text-sm" required>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Tgl Tutup</label>
                        <input type="date" name="end_date" class="w-full p-2.5 border border-gray-300 rounded text-sm" required>
                    </div>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 shadow-lg transition">Simpan</button>
            </form>
        </div>

        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white p-4 rounded shadow-sm border border-gray-200">
                <form method="GET" class="flex flex-wrap items-center gap-3">
                    <input type="hidden" name="limit" value="<?= $limit ?>">
                    <div class="flex-1 min-w-[250px]">
                        <div class="relative">
                            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
                            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari periode..." class="w-full pl-10 pr-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none border-gray-300">
                        </div>
                    </div>
                    <button type="submit" class="bg-gray-800 text-white px-6 py-2 rounded-lg text-sm font-bold hover:bg-black transition">Filter</button>
                </form>
            </div>

            <div class="bg-white rounded shadow-sm border border-gray-200 overflow-hidden flex flex-col">
                <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                    <h4 class="font-bold text-gray-700 text-[10px] uppercase">Daftar Gelombang</h4>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] text-gray-500 font-bold uppercase">Show:</span>
                        <select onchange="window.location.href=updateQueryStringParameter(window.location.href, 'limit', this.value)" class="border rounded p-1 text-xs outline-none bg-white">
                            <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10</option>
                            <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50</option>
                        </select>
                    </div>
                </div>
                
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="min-w-full whitespace-nowrap">
                        <thead>
                            <tr class="bg-gray-100 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                <th class="px-5 py-4 border-b">Nama Periode</th>
                                <th class="px-5 py-4 border-b text-center">Buka</th>
                                <th class="px-5 py-4 border-b text-center">Tutup</th>
                                <th class="px-5 py-4 border-b text-center">Status</th>
                                <th class="px-5 py-4 border-b text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php foreach ($batches as $row): ?>
                            <tr class="hover:bg-blue-50/30 transition text-sm">
                                <td class="px-5 py-4 font-bold text-blue-900"><?= $row['name'] ?></td>
                                <td class="px-5 py-4 text-center"><?= date('d/m/Y', strtotime($row['start_date'])) ?></td>
                                <td class="px-5 py-4 text-center"><?= date('d/m/Y', strtotime($row['end_date'])) ?></td>
                                <td class="px-5 py-4 text-center">
                                    <?php if($row['is_active']): ?>
                                        <span class="bg-green-100 text-green-700 px-2.5 py-1 rounded-full text-[10px] font-extrabold border border-green-200 uppercase">Aktif</span>
                                    <?php else: ?>
                                        <a href="/ppdb/periods/activate?id=<?= $row['id'] ?>" class="text-[10px] text-gray-400 font-bold hover:text-blue-600">Set Aktif</a>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-4 text-center flex justify-center gap-3">
                                    <button onclick="openEditModal(this)" data-id="<?= $row['id'] ?>" data-name="<?= $row['name'] ?>" data-start="<?= $row['start_date'] ?>" data-end="<?= $row['end_date'] ?>" class="text-blue-500 hover:text-blue-700"><i class="fa-solid fa-pen-to-square"></i></button>
                                    <form action="/ppdb/periods/delete" method="POST" onsubmit="return confirm('Hapus?')">
                                        <?= \App\Core\Csrf::input() ?>
                                        <input type="hidden" name="id" value="<?= $row['id'] ?>">
                                        <button type="submit" class="text-red-400 hover:text-red-600"><i class="fa-solid fa-trash-can"></i></button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if($totalPages > 1): ?>
                <div class="p-4 bg-gray-50 border-t flex justify-between items-center">
                    <span class="text-[10px] text-gray-500 font-bold uppercase">Hal. <?= $currentPage ?> / <?= $totalPages ?></span>
                    <div class="flex gap-1">
                        <?php $qs = "&limit=$limit&search=".urlencode($search); ?>
                        <?php for($i=1; $i<=$totalPages; $i++): ?>
                            <a href="?page=<?= $i . $qs ?>" class="px-2.5 py-1 border rounded text-[10px] <?= $i==$currentPage?'bg-blue-600 text-white border-blue-600 shadow':'bg-white hover:bg-gray-100' ?>"><?= $i ?></a>
                        <?php endfor; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<div id="editModal" class="hidden fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
            <h3 class="font-bold text-gray-700 text-sm uppercase">Edit Periode</h3>
            <button onclick="closeEditModal()" class="text-gray-400"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="/ppdb/periods/update" method="POST" class="p-6 space-y-4">
            <?= \App\Core\Csrf::input() ?>
            <input type="hidden" name="id" id="edit_id">
            <input type="text" name="name" id="edit_name" class="w-full p-2.5 border rounded text-sm" required>
            <div class="grid grid-cols-2 gap-3">
                <input type="date" name="start_date" id="edit_start" class="p-2.5 border rounded text-sm" required>
                <input type="date" name="end_date" id="edit_end" class="p-2.5 border rounded text-sm" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold shadow-lg">Perbarui</button>
        </form>
    </div>
</div>

<script>
    function openEditModal(btn) {
        document.getElementById('edit_id').value = btn.getAttribute('data-id');
        document.getElementById('edit_name').value = btn.getAttribute('data-name');
        document.getElementById('edit_start').value = btn.getAttribute('data-start');
        document.getElementById('edit_end').value = btn.getAttribute('data-end');
        document.getElementById('editModal').classList.remove('hidden');
    }
    function closeEditModal() { document.getElementById('editModal').classList.add('hidden'); }
    function updateQueryStringParameter(uri, key, value) {
        var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        var separator = uri.indexOf('?') !== -1 ? "&" : "?";
        return uri.match(re) ? uri.replace(re, '$1' + key + "=" + value + '$2') : uri + separator + key + "=" + value;
    }
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
