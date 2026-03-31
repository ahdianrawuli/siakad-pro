<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<style>
    /* Styling Scrollbar & Modal seperti Mata Pelajaran */
    .custom-scrollbar::-webkit-scrollbar { height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    #addModal.hidden, #editModal.hidden { display: none; opacity: 0; }
    /* Agar teks tidak turun ke bawah */
    .whitespace-nowrap { white-space: nowrap; }
</style>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <div class="mb-6 flex justify-between items-end">
        <div>
            <h3 class="text-3xl font-medium text-gray-700">Data Siswa</h3>
            <p class="text-gray-500 text-sm">Total <?= $totalData ?> siswa aktif terdaftar.</p>
        </div>
        <button onclick="document.getElementById('addModal').classList.remove('hidden')" 
                class="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-blue-700 shadow-lg transition text-sm">
            <i class="fa-solid fa-plus-circle mr-2"></i> Tambah Siswa
        </button>
    </div>
    
    <?php \App\Core\Session::flash(); ?>

    <div class="grid grid-cols-1 gap-6">
        
        <div class="space-y-4">
            
            <div class="bg-white p-4 rounded shadow-sm border border-gray-200">
                <form method="GET" class="flex flex-wrap items-center gap-3">
                    <input type="hidden" name="limit" value="<?= $limit ?>">
                    
                    <div class="flex-1 min-w-[250px]">
                        <div class="relative">
                            <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
                            <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                                   placeholder="Cari Nama, NIS, atau Kelas..." 
                                   class="w-full pl-10 pr-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none border-gray-300">
                        </div>
                    </div>

                    <button type="submit" class="bg-gray-800 text-white px-6 py-2 rounded-lg text-sm font-bold hover:bg-black transition">
                        Filter
                    </button>
                    
                    <?php if(!empty($search)): ?>
                        <a href="/student-affairs/students" class="text-red-500 text-xs font-bold underline">Reset</a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="bg-white rounded shadow-sm border border-gray-200 overflow-hidden flex flex-col">
                <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                    <h4 class="font-bold text-gray-700 text-xs uppercase">Daftar Siswa</h4>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] text-gray-500 font-bold uppercase">Show:</span>
                        <select onchange="window.location.href=updateQueryStringParameter(window.location.href, 'limit', this.value)" class="border rounded p-1 text-xs outline-none cursor-pointer">
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
                                <th class="px-5 py-4 border-b">NIS</th>
                                <th class="px-5 py-4 border-b">Nama Lengkap</th>
                                <th class="px-5 py-4 border-b">Kelas</th>
                                <th class="px-5 py-4 border-b">Gender</th>
                                <th class="px-5 py-4 border-b">Orang Tua</th>
                                <th class="px-5 py-4 border-b text-center">Status</th>
                                <th class="px-5 py-4 border-b text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php if (empty($students)): ?>
                            <tr>
                                <td colspan="7" class="px-5 py-12 text-center text-gray-400 italic text-sm">Data tidak ditemukan.</td>
                            </tr>
                            <?php endif; ?>

                            <?php foreach ($students as $row): ?>
                            <tr class="hover:bg-blue-50/30 transition text-sm">
                                <td class="px-5 py-4 font-mono text-xs text-gray-500">
                                    <?= $row['nis'] ?>
                                </td>
                                <td class="px-5 py-4 font-bold text-gray-800">
                                    <?= $row['full_name'] ?>
                                </td>
                                <td class="px-5 py-4">
                                    <?php if($row['class_name']): ?>
                                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded font-bold border border-blue-200">
                                            <?= $row['class_name'] ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded border border-red-200">No Class</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-4 text-xs">
                                    <?= $row['gender'] == 'L' ? 'Laki-laki' : 'Perempuan' ?>
                                </td>
                                <td class="px-5 py-4 text-xs text-gray-600">
                                    <?= $row['parent_name'] ?? '-' ?> <br>
                                    <span class="text-[10px] text-gray-400"><?= $row['parent_phone'] ?? '' ?></span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <span class="text-green-600 font-extrabold text-[10px] px-2 py-0.5 rounded border border-green-200 bg-green-50">ACTIVE</span>
                                </td>
                                <td class="px-5 py-4 text-center flex justify-center items-center gap-2">
                                    <button onclick="openEditModal(this)" 
                                            data-id="<?= $row['id'] ?>" 
                                            data-nis="<?= $row['nis'] ?>" 
                                            data-name="<?= $row['full_name'] ?>" 
                                            data-class="<?= $row['classroom_id'] ?>"
                                            class="text-blue-500 hover:text-blue-700 transition" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    
                                    <a href="/report/print?student_id=<?= $row['id'] ?>" target="_blank" class="text-green-600 hover:text-green-800" title="Cetak Rapor">
                                        <i class="fa-solid fa-print"></i>
                                    </a>

                                    <a href="/student-affairs/students/delete?id=<?= $row['id'] ?>" 
                                       onclick="return confirm('Hapus data siswa ini? Data nilai dan tagihan juga akan terhapus!')"
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
                        
                        <?php 
                        $start = max(1, $currentPage - 2);
                        $end = min($totalPages, $currentPage + 2);
                        if($start > 1) echo '<span class="px-2 text-gray-400">...</span>';
                        for($i = $start; $i <= $end; $i++): 
                        ?>
                            <a href="?page=<?= $i . $queryString ?>" class="px-3 py-1 border rounded text-xs transition <?= $i == $currentPage ? 'bg-blue-600 text-white border-blue-600 font-bold' : 'bg-white hover:bg-gray-100' ?>">
                                <?= $i ?>
                            </a>
                        <?php endfor; ?>
                        <?php if($end < $totalPages) echo '<span class="px-2 text-gray-400">...</span>'; ?>

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

<div id="addModal" class="hidden fixed inset-0 z-50 overflow-auto bg-black bg-opacity-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden animate__animated animate__zoomIn animate__faster">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-700">Tambah Siswa Baru</h3>
            <button onclick="document.getElementById('addModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <form action="/student-affairs/students/store" method="POST" class="p-6">
            <?= \App\Core\Csrf::input() ?>
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">NIS</label>
                <input type="text" name="nis" placeholder="Nomor Induk Siswa" class="w-full p-2.5 border rounded-lg text-sm" required>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Nama Lengkap</label>
                <input type="text" name="full_name" placeholder="Nama Siswa" class="w-full p-2.5 border rounded-lg text-sm" required>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Jenis Kelamin</label>
                <select name="gender" class="w-full p-2.5 border rounded-lg text-sm bg-white" required>
                    <option value="L">Laki-laki</option>
                    <option value="P">Perempuan</option>
                </select>
            </div>
            <div class="mb-6">
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Kelas</label>
                <select name="classroom_id" class="w-full p-2.5 border rounded-lg text-sm bg-white" required>
                    <option value="">Pilih Kelas</option>
                    <?php foreach ($classrooms as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 shadow-lg transition">Simpan</button>
        </form>
    </div>
</div>

<div id="editModal" class="hidden fixed inset-0 z-50 overflow-auto bg-black bg-opacity-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden animate__animated animate__zoomIn animate__faster">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-700">Edit Data Siswa</h3>
            <button onclick="document.getElementById('editModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <form action="/student-affairs/students/update" method="POST" class="p-6">
            <?= \App\Core\Csrf::input() ?>
            <input type="hidden" name="id" id="edit_id">
            
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">NIS</label>
                <input type="text" name="nis" id="edit_nis" class="w-full p-2.5 border rounded-lg text-sm bg-gray-50" required>
            </div>
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Nama Lengkap</label>
                <input type="text" name="full_name" id="edit_name" class="w-full p-2.5 border rounded-lg text-sm" required>
            </div>
            <div class="mb-6">
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Kelas</label>
                <select name="classroom_id" id="edit_class" class="w-full p-2.5 border rounded-lg text-sm bg-white" required>
                    <?php foreach ($classrooms as $c): ?>
                        <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 shadow-lg transition">Update Data</button>
        </form>
    </div>
</div>

<script>
    // Handle Open Edit Modal & Populate Data
    function openEditModal(btn) {
        document.getElementById('edit_id').value = btn.getAttribute('data-id');
        document.getElementById('edit_nis').value = btn.getAttribute('data-nis');
        document.getElementById('edit_name').value = btn.getAttribute('data-name');
        document.getElementById('edit_class').value = btn.getAttribute('data-class');
        
        document.getElementById('editModal').classList.remove('hidden');
    }

    // Helper untuk Update URL Parameter (Limit)
    function updateQueryStringParameter(uri, key, value) {
        var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        var separator = uri.indexOf('?') !== -1 ? "&" : "?";
        if (uri.match(re)) {
            return uri.replace(re, '$1' + key + "=" + value + '$2');
        } else {
            return uri + separator + key + "=" + value;
        }
    }

    // Tutup modal jika klik di luar area
    window.onclick = function(event) {
        if (event.target == document.getElementById('addModal')) {
            document.getElementById('addModal').classList.add('hidden');
        }
        if (event.target == document.getElementById('editModal')) {
            document.getElementById('editModal').classList.add('hidden');
        }
    }
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
