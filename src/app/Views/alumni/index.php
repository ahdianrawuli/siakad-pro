<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<style>
    .custom-scrollbar::-webkit-scrollbar { height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .whitespace-nowrap { white-space: nowrap; }
    #addModal.hidden, #editModal.hidden { display: none; opacity: 0; }
</style>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <div class="mb-6 flex justify-between items-end">
        <div>
            <h3 class="text-3xl font-medium text-gray-700">Data Alumni</h3>
            <p class="text-gray-500 text-sm">Database lulusan dan penelusuran karir alumni.</p>
        </div>
        
        <button onclick="document.getElementById('addModal').classList.remove('hidden')" 
           class="bg-blue-800 text-white px-4 py-2 rounded-lg font-bold hover:bg-blue-900 shadow-lg transition text-sm flex items-center">
            <i class="fa-solid fa-graduation-cap mr-2"></i> Tambah Alumni
        </button>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <div class="space-y-4">
        
        <div class="bg-white p-4 rounded shadow-sm border border-gray-200">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <input type="hidden" name="limit" value="<?= $limit ?>">
                
                <div class="md:col-span-2 relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                           placeholder="Cari Nama Alumni atau NIS..." 
                           class="w-full pl-10 pr-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <input type="number" name="year" value="<?= $yearFilter ?>" placeholder="Tahun Lulus (Cth: 2024)"
                       class="w-full p-2 border rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500 outline-none">

                <select name="activity" class="w-full p-2 border rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">Semua Aktivitas</option>
                    <option value="KULIAH" <?= $activityFilter == 'KULIAH' ? 'selected' : '' ?>>Kuliah</option>
                    <option value="KERJA" <?= $activityFilter == 'KERJA' ? 'selected' : '' ?>>Bekerja</option>
                    <option value="USAHA" <?= $activityFilter == 'USAHA' ? 'selected' : '' ?>>Wirausaha</option>
                    <option value="LAINNYA" <?= $activityFilter == 'LAINNYA' ? 'selected' : '' ?>>Lainnya</option>
                </select>

                <div class="md:col-span-4 flex justify-end gap-2">
                    <button type="submit" class="bg-gray-800 text-white px-6 py-2 rounded-lg text-sm font-bold hover:bg-black transition">
                        Terapkan Filter
                    </button>
                    <?php if(!empty($search) || !empty($yearFilter) || !empty($activityFilter)): ?>
                        <a href="/student-affairs/alumni" class="px-4 py-2 bg-red-100 text-red-600 rounded-lg text-sm font-bold hover:bg-red-200 text-center">
                            Reset
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="bg-white rounded shadow-sm border border-gray-200 overflow-hidden flex flex-col">
            <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                <h4 class="font-bold text-gray-700 text-xs uppercase">Daftar Alumni</h4>
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
                            <th class="px-5 py-4 border-b">Nama Alumni</th>
                            <th class="px-5 py-4 border-b text-center">Lulusan</th>
                            <th class="px-5 py-4 border-b">Aktivitas Saat Ini</th>
                            <th class="px-5 py-4 border-b">Kontak</th>
                            <th class="px-5 py-4 border-b text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if (empty($alumni)): ?>
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-gray-400 italic text-sm">Data alumni tidak ditemukan.</td>
                        </tr>
                        <?php endif; ?>

                        <?php foreach ($alumni as $row): ?>
                        <tr class="hover:bg-blue-50/30 transition text-sm">
                            <td class="px-5 py-4">
                                <div class="font-bold text-gray-800"><?= $row['full_name'] ?></div>
                                <div class="text-[10px] text-gray-500 font-mono">NIS: <?= $row['nis'] ?></div>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded font-bold text-xs border border-gray-300">
                                    <?= $row['graduation_year'] ?>
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <?php
                                    $colors = [
                                        'KULIAH' => 'bg-blue-100 text-blue-700 border-blue-200',
                                        'KERJA' => 'bg-green-100 text-green-700 border-green-200',
                                        'USAHA' => 'bg-orange-100 text-orange-700 border-orange-200',
                                        'LAINNYA' => 'bg-gray-100 text-gray-600 border-gray-200'
                                    ];
                                ?>
                                <span class="px-2 py-0.5 text-[10px] font-extrabold rounded border <?= $colors[$row['activity']] ?? 'bg-gray-100' ?>">
                                    <?= $row['activity'] ?>
                                </span>
                                <div class="text-xs text-gray-500 mt-1 italic"><?= $row['detail_activity'] ?: '-' ?></div>
                            </td>
                            <td class="px-5 py-4 text-xs text-gray-600">
                                <?php if($row['phone']): ?>
                                    <div><i class="fa-brands fa-whatsapp text-green-500 mr-1"></i> <?= $row['phone'] ?></div>
                                <?php endif; ?>
                                <?php if($row['email']): ?>
                                    <div class="text-gray-400"><i class="fa-regular fa-envelope mr-1"></i> <?= $row['email'] ?></div>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-4 text-center flex justify-center gap-2">
                                <button onclick="openEditModal(this)" 
                                        data-id="<?= $row['id'] ?>"
                                        data-nis="<?= $row['nis'] ?>"
                                        data-name="<?= $row['full_name'] ?>"
                                        data-year="<?= $row['graduation_year'] ?>"
                                        data-activity="<?= $row['activity'] ?>"
                                        data-detail="<?= $row['detail_activity'] ?>"
                                        data-phone="<?= $row['phone'] ?>"
                                        data-email="<?= $row['email'] ?>"
                                        class="text-blue-500 hover:text-blue-700 transition" title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <a href="/student-affairs/alumni/delete?id=<?= $row['id'] ?>" 
                                   onclick="return confirm('Hapus data alumni ini?')"
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
                    Hal. <?= $currentPage ?> / <?= $totalPages ?>
                </div>
                <div class="flex gap-1">
                    <?php 
                        $queryString = "&limit=$limit&search=" . urlencode($search) . "&year=$yearFilter&activity=$activityFilter"; 
                    ?>
                    <?php for($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?= $i . $queryString ?>" class="px-3 py-1 border rounded text-xs transition <?= $i == $currentPage ? 'bg-blue-800 text-white border-blue-800 font-bold' : 'bg-white hover:bg-gray-100' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<div id="addModal" class="hidden fixed inset-0 z-50 overflow-auto bg-black bg-opacity-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden animate__animated animate__zoomIn animate__faster">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-700 flex items-center">
                <i class="fa-solid fa-user-graduate mr-2 text-blue-800"></i> Tambah Data Alumni
            </h3>
            <button onclick="document.getElementById('addModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <form action="/student-affairs/alumni/store" method="POST" class="p-6">
            <?= \App\Core\Csrf::input() ?>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">NIS (Nomor Induk Siswa)</label>
                    <input type="text" name="nis" placeholder="Cth: 2021001" class="w-full p-2.5 border rounded-lg text-sm outline-none focus:border-blue-500" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Nama Lengkap</label>
                    <input type="text" name="full_name" placeholder="Nama Alumni" class="w-full p-2.5 border rounded-lg text-sm outline-none focus:border-blue-500" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Tahun Lulus</label>
                    <input type="number" name="graduation_year" value="<?= date('Y') ?>" class="w-full p-2.5 border rounded-lg text-sm outline-none focus:border-blue-500" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Aktivitas Saat Ini</label>
                    <select name="activity" class="w-full p-2.5 border rounded-lg text-sm bg-white outline-none focus:border-blue-500" required>
                        <option value="KULIAH">Kuliah</option>
                        <option value="KERJA">Bekerja</option>
                        <option value="USAHA">Wirausaha</option>
                        <option value="LAINNYA">Lainnya / Belum Bekerja</option>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Detail Aktivitas (Nama Kampus / Kantor)</label>
                <input type="text" name="detail_activity" placeholder="Cth: Universitas Indonesia / PT. Maju Mundur" class="w-full p-2.5 border rounded-lg text-sm outline-none focus:border-blue-500">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">No. WhatsApp</label>
                    <input type="text" name="phone" placeholder="08..." class="w-full p-2.5 border rounded-lg text-sm outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Email (Opsional)</label>
                    <input type="email" name="email" placeholder="email@example.com" class="w-full p-2.5 border rounded-lg text-sm outline-none focus:border-blue-500">
                </div>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="flex-1 bg-gray-100 text-gray-600 py-3 rounded-lg font-bold hover:bg-gray-200 transition">Batal</button>
                <button type="submit" class="flex-1 bg-blue-800 text-white py-3 rounded-lg font-bold hover:bg-blue-900 shadow-lg transition">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<div id="editModal" class="hidden fixed inset-0 z-50 overflow-auto bg-black bg-opacity-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-2xl overflow-hidden animate__animated animate__zoomIn animate__faster">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-700 flex items-center">
                <i class="fa-solid fa-pen-to-square mr-2 text-blue-600"></i> Edit Data Alumni
            </h3>
            <button onclick="document.getElementById('editModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <form action="/student-affairs/alumni/update" method="POST" class="p-6">
            <?= \App\Core\Csrf::input() ?>
            <input type="hidden" name="id" id="edit_id">
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">NIS</label>
                    <input type="text" name="nis" id="edit_nis" class="w-full p-2.5 border rounded-lg text-sm bg-gray-50 outline-none focus:border-blue-500" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Nama Lengkap</label>
                    <input type="text" name="full_name" id="edit_name" class="w-full p-2.5 border rounded-lg text-sm outline-none focus:border-blue-500" required>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Tahun Lulus</label>
                    <input type="number" name="graduation_year" id="edit_year" class="w-full p-2.5 border rounded-lg text-sm outline-none focus:border-blue-500" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Aktivitas</label>
                    <select name="activity" id="edit_activity" class="w-full p-2.5 border rounded-lg text-sm bg-white outline-none focus:border-blue-500" required>
                        <option value="KULIAH">Kuliah</option>
                        <option value="KERJA">Bekerja</option>
                        <option value="USAHA">Wirausaha</option>
                        <option value="LAINNYA">Lainnya / Belum Bekerja</option>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Detail Aktivitas</label>
                <input type="text" name="detail_activity" id="edit_detail" class="w-full p-2.5 border rounded-lg text-sm outline-none focus:border-blue-500">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">No. WhatsApp</label>
                    <input type="text" name="phone" id="edit_phone" class="w-full p-2.5 border rounded-lg text-sm outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Email</label>
                    <input type="email" name="email" id="edit_email" class="w-full p-2.5 border rounded-lg text-sm outline-none focus:border-blue-500">
                </div>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="flex-1 bg-gray-100 text-gray-600 py-3 rounded-lg font-bold hover:bg-gray-200 transition">Batal</button>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 shadow-lg transition">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditModal(btn) {
        document.getElementById('edit_id').value = btn.getAttribute('data-id');
        document.getElementById('edit_nis').value = btn.getAttribute('data-nis');
        document.getElementById('edit_name').value = btn.getAttribute('data-name');
        document.getElementById('edit_year').value = btn.getAttribute('data-year');
        document.getElementById('edit_activity').value = btn.getAttribute('data-activity');
        document.getElementById('edit_detail').value = btn.getAttribute('data-detail');
        document.getElementById('edit_phone').value = btn.getAttribute('data-phone');
        document.getElementById('edit_email').value = btn.getAttribute('data-email');
        
        document.getElementById('editModal').classList.remove('hidden');
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
