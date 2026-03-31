<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<style>
    .custom-scrollbar::-webkit-scrollbar { height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    
    /* Modal Style */
    #editModal { transition: opacity 0.2s ease-in-out; }
    #editModal.hidden { display: none; opacity: 0; }
</style>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <div class="mb-6 flex justify-between items-end">
        <div>
            <h3 class="text-3xl font-medium text-gray-700">Mata Pelajaran</h3>
            <p class="text-gray-500 text-sm">Ditemukan <?= $totalData ?> mata pelajaran dalam sistem.</p>
        </div>
    </div>
    
    <?php \App\Core\Session::flash(); ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="bg-white p-6 rounded shadow-sm h-fit border border-gray-200">
            <h4 class="font-bold mb-4 text-gray-700 border-b pb-2 flex items-center">
                <i class="fa-solid fa-plus-circle mr-2 text-blue-600"></i> Tambah Mata Pelajaran
            </h4>
            <form action="/academic/subjects/store" method="POST">
                <?= \App\Core\Csrf::input() ?>
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Kode Mapel</label>
                    <input type="text" name="code" placeholder="Contoh: MP-001" class="w-full p-2.5 border border-gray-300 rounded-lg text-sm" required>
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Nama Mata Pelajaran</label>
                    <input type="text" name="name" placeholder="Contoh: Matematika" class="w-full p-2.5 border border-gray-300 rounded-lg text-sm" required>
                </div>
                <div class="mb-4">
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Kategori / Tipe</label>
                    <select name="type" class="w-full p-2.5 border border-gray-300 rounded-lg text-sm bg-white" required>
                        <option value="Umum">Umum</option>
                        <option value="Diniyah">Diniyah</option>
                        <option value="Tahfidz">Tahfidz</option>
                        <option value="Ekstrakurikuler">Ekstrakurikuler</option>
                    </select>
                </div>
                <div class="mb-6">
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">KKM</label>
                    <input type="number" name="kkm" placeholder="75" class="w-full p-2.5 border border-gray-300 rounded-lg text-sm" required>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 shadow-lg transition">
                    Simpan Mata Pelajaran
                </button>
            </form>
        </div>

        <div class="lg:col-span-2 space-y-4">
            
            <div class="bg-white p-4 rounded shadow-sm border border-gray-200">
                <form method="GET" class="flex flex-wrap items-center gap-3">
                    <input type="hidden" name="limit" value="<?= $limit ?>">
                    
                    <div class="flex-1 min-w-[250px]">
                        <div class="relative">
                            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
                            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                                   placeholder="Cari kode atau nama mata pelajaran..." 
                                   class="w-full pl-10 pr-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none border-gray-300">
                        </div>
                    </div>

                    <button type="submit" class="bg-gray-800 text-white px-6 py-2 rounded-lg text-sm font-bold hover:bg-black transition">
                        Filter
                    </button>
                    
                    <?php if(!empty($search)): ?>
                        <a href="/academic/subjects" class="text-red-500 text-xs font-bold underline">Reset</a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="bg-white rounded shadow-sm border border-gray-200 overflow-hidden flex flex-col">
                <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                    <h4 class="font-bold text-gray-700 text-xs uppercase">Daftar Mata Pelajaran</h4>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] text-gray-500 font-bold uppercase">Show:</span>
                        <select onchange="window.location.href=updateQueryStringParameter(window.location.href, 'limit', this.value)" class="border rounded p-1 text-xs outline-none">
                            <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10</option>
                            <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50</option>
                            <option value="100" <?= $limit == 100 ? 'selected' : '' ?>>100</option>
                        </select>
                    </div>
                </div>
                
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="min-w-full whitespace-nowrap">
                        <thead>
                            <tr class="bg-gray-100 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                <th class="px-5 py-4 border-b">Kode</th>
                                <th class="px-5 py-4 border-b">Mata Pelajaran</th>
                                <th class="px-5 py-4 border-b">Tipe</th>
                                <th class="px-5 py-4 border-b text-center">KKM</th>
                                <th class="px-5 py-4 border-b text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php if (empty($subjects)): ?>
                            <tr>
                                <td colspan="5" class="px-5 py-12 text-center text-gray-400 italic text-sm">Data tidak ditemukan.</td>
                            </tr>
                            <?php endif; ?>

                            <?php foreach ($subjects as $row): ?>
                            <tr class="hover:bg-blue-50/30 transition text-sm">
                                <td class="px-5 py-4 font-mono text-xs text-gray-500">
                                    <?= $row['code'] ?>
                                </td>
                                <td class="px-5 py-4 font-bold text-blue-800">
                                    <?= $row['name'] ?>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="px-2 py-0.5 text-[10px] font-extrabold rounded border 
                                        <?= $row['type'] == 'Umum' ? 'bg-blue-50 text-blue-700 border-blue-200' : 
                                           ($row['type'] == 'Diniyah' ? 'bg-green-50 text-green-700 border-green-200' : 
                                           ($row['type'] == 'Tahfidz' ? 'bg-purple-50 text-purple-700 border-purple-200' : 'bg-gray-50 text-gray-700 border-gray-200')) ?>">
                                        <?= strtoupper($row['type']) ?>
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="font-bold text-gray-700"><?= $row['kkm'] ?></span>
                                </td>
                                <td class="px-5 py-4 text-center flex justify-center gap-3">
                                    <button onclick="openEditModal(this)" 
                                            data-id="<?= $row['id'] ?>" 
                                            data-code="<?= $row['code'] ?>" 
                                            data-name="<?= $row['name'] ?>" 
                                            data-type="<?= $row['type'] ?>" 
                                            data-kkm="<?= $row['kkm'] ?>"
                                            class="text-blue-500 hover:text-blue-700 transition" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <a href="/academic/subjects/delete?id=<?= $row['id'] ?>" 
                                       onclick="return confirm('Hapus mata pelajaran ini?')"
                                       class="text-red-400 hover:text-red-600 transition" title="Hapus">
                                        <i class="fa-solid fa-trash-can"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if($totalPages > 1): ?>
                <div class="p-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                    <div class="text-xs text-gray-500 font-medium">
                        Hal. <?= $currentPage ?> / <?= $totalPages ?> (Total <?= $totalData ?> data)
                    </div>
                    <div class="flex gap-1">
                        <?php $queryString = "&limit=$limit&search=" . urlencode($search); ?>
                        <?php if($currentPage > 1): ?>
                            <a href="?page=<?= $currentPage - 1 . $queryString ?>" class="px-3 py-1 bg-white border rounded text-xs hover:bg-gray-100 transition">Prev</a>
                        <?php endif; ?>
                        <?php for($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?= $i . $queryString ?>" class="px-3 py-1 border rounded text-xs transition <?= $i == $currentPage ? 'bg-blue-600 text-white border-blue-600 font-bold' : 'bg-white hover:bg-gray-100' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                        <?php if($currentPage < $totalPages): ?>
                            <a href="?page=<?= $currentPage + 1 . $queryString ?>" class="px-3 py-1 bg-white border rounded text-xs hover:bg-gray-100 transition">Next</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<div id="editModal" class="hidden fixed inset-0 z-50 overflow-auto bg-black bg-opacity-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden animate__animated animate__zoomIn animate__faster">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-700 flex items-center">
                <i class="fa-solid fa-pen-to-square mr-2 text-blue-600"></i> Edit Mata Pelajaran
            </h3>
            <button onclick="closeEditModal()" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <form action="/academic/subjects/update" method="POST" class="p-6">
            <?= \App\Core\Csrf::input() ?>
            <input type="hidden" name="id" id="edit_id">
            
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Kode Mapel</label>
                <input type="text" name="code" id="edit_code" class="w-full p-2.5 border border-gray-300 rounded-lg text-sm bg-gray-50 focus:ring-2 focus:ring-blue-500 outline-none" required>
            </div>
            
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Nama Mata Pelajaran</label>
                <input type="text" name="name" id="edit_name" class="w-full p-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none" required>
            </div>
            
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Tipe</label>
                <select name="type" id="edit_type" class="w-full p-2.5 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500 outline-none" required>
                    <option value="Umum">Umum</option>
                    <option value="Diniyah">Diniyah</option>
                    <option value="Tahfidz">Tahfidz</option>
                    <option value="Ekstrakurikuler">Ekstrakurikuler</option>
                </select>
            </div>
            
            <div class="mb-6">
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">KKM</label>
                <input type="number" name="kkm" id="edit_kkm" class="w-full p-2.5 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none" required>
            </div>
            
            <div class="flex gap-3">
                <button type="button" onclick="closeEditModal()" class="flex-1 bg-gray-100 text-gray-600 py-3 rounded-lg font-bold hover:bg-gray-200 transition">Batal</button>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 shadow-lg transition">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditModal(btn) {
        const id = btn.getAttribute('data-id');
        const code = btn.getAttribute('data-code');
        const name = btn.getAttribute('data-name');
        const type = btn.getAttribute('data-type');
        const kkm = btn.getAttribute('data-kkm');

        document.getElementById('edit_id').value = id;
        document.getElementById('edit_code').value = code;
        document.getElementById('edit_name').value = name;
        document.getElementById('edit_type').value = type;
        document.getElementById('edit_kkm').value = kkm;

        document.getElementById('editModal').classList.remove('hidden');
    }

    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }

    // Tutup modal jika klik di luar box
    window.onclick = function(event) {
        const modal = document.getElementById('editModal');
        if (event.target == modal) {
            closeEditModal();
        }
    }

    function updateQueryStringParameter(uri, key, value) {
        var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        var separator = uri.indexOf('?') !== -1 ? "&" : "?";
        if (uri.match(re)) {
            return uri.replace(re, '$1' + key + "=" + value + '$2');
        } else {
            return uri + separator + key + "=" + value;
        }
    }
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
