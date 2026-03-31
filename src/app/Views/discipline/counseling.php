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
            <h3 class="text-3xl font-medium text-gray-700">Bimbingan Konseling</h3>
            <p class="text-gray-500 text-sm">Catatan konseling dan pembinaan siswa.</p>
        </div>
        
        <button onclick="document.getElementById('addModal').classList.remove('hidden')" 
           class="bg-pink-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-pink-700 shadow-lg transition text-sm flex items-center">
            <i class="fa-solid fa-heart-pulse mr-2"></i> Input Sesi Konseling
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
                           placeholder="Cari Nama Siswa atau Masalah..." 
                           class="w-full pl-10 pr-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-pink-400 outline-none">
                </div>

                <input type="date" name="date" value="<?= $dateFilter ?>" 
                       class="w-full p-2 border rounded-lg text-sm bg-white focus:ring-2 focus:ring-pink-400 outline-none">

                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-black transition">
                        Filter
                    </button>
                    <?php if(!empty($search) || !empty($dateFilter)): ?>
                        <a href="/student-affairs/counseling" class="px-3 py-2 bg-red-100 text-red-600 rounded-lg text-sm font-bold hover:bg-red-200 text-center">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="bg-white rounded shadow-sm border border-gray-200 overflow-hidden flex flex-col">
            <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                <h4 class="font-bold text-gray-700 text-xs uppercase">Riwayat Konseling</h4>
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
                            <th class="px-5 py-4 border-b">Tanggal</th>
                            <th class="px-5 py-4 border-b">Siswa</th>
                            <th class="px-5 py-4 border-b">Kelas</th>
                            <th class="px-5 py-4 border-b">Permasalahan / Isu</th>
                            <th class="px-5 py-4 border-b">Hasil / Tindak Lanjut</th>
                            <th class="px-5 py-4 border-b">Konselor</th>
                            <th class="px-5 py-4 border-b text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-gray-400 italic text-sm">Belum ada data konseling.</td>
                        </tr>
                        <?php endif; ?>

                        <?php foreach ($logs as $row): ?>
                        <tr class="hover:bg-pink-50/30 transition text-sm">
                            <td class="px-5 py-4 text-gray-600 font-mono text-xs">
                                <?= date('d/m/Y', strtotime($row['date'])) ?>
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-bold text-gray-800"><?= $row['full_name'] ?></div>
                                <div class="text-[10px] text-gray-500">NIS: <?= $row['nis'] ?></div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="bg-gray-100 text-gray-600 text-[10px] px-2 py-1 rounded border border-gray-200 font-bold">
                                    <?= $row['class_name'] ?? '-' ?>
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-bold text-pink-600 text-sm"><?= $row['issue'] ?></div>
                            </td>
                            <td class="px-5 py-4 text-xs text-gray-600 italic max-w-xs truncate">
                                <?= $row['result'] ?: '-' ?>
                            </td>
                            <td class="px-5 py-4 text-xs text-gray-500">
                                <?= $row['counselor_name'] ?>
                            </td>
                            <td class="px-5 py-4 text-center flex justify-center gap-2">
                                <button onclick="openEditModal(this)" 
                                        data-id="<?= $row['id'] ?>"
                                        data-student="<?= $row['student_id'] ?>"
                                        data-date="<?= $row['date'] ?>"
                                        data-issue="<?= htmlspecialchars($row['issue']) ?>"
                                        data-result="<?= htmlspecialchars($row['result']) ?>"
                                        class="text-blue-500 hover:text-blue-700 transition" title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <a href="/student-affairs/counseling/delete?id=<?= $row['id'] ?>" 
                                   onclick="return confirm('Hapus data konseling ini?')"
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
                    <?php $queryString = "&limit=$limit&search=" . urlencode($search) . "&date=$dateFilter"; ?>
                    <?php for($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?= $i . $queryString ?>" class="px-3 py-1 border rounded text-xs transition <?= $i == $currentPage ? 'bg-pink-600 text-white border-pink-600 font-bold' : 'bg-white hover:bg-gray-100' ?>">
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
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden animate__animated animate__zoomIn animate__faster">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-700 flex items-center">
                <i class="fa-solid fa-heart-pulse mr-2 text-pink-600"></i> Catat Sesi Konseling
            </h3>
            <button onclick="document.getElementById('addModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <form action="/student-affairs/counseling/store" method="POST" class="p-6">
            <?= \App\Core\Csrf::input() ?>
            
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Nama Siswa</label>
                <select name="student_id" class="w-full p-2.5 border rounded-lg text-sm bg-white focus:ring-2 focus:ring-pink-400 outline-none" required>
                    <option value="">-- Cari Siswa --</option>
                    <?php foreach($students as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= $s['full_name'] ?> (<?= $s['nis'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Tanggal Konseling</label>
                <input type="date" name="date" value="<?= date('Y-m-d') ?>" class="w-full p-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-pink-400 outline-none" required>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Pokok Permasalahan</label>
                <input type="text" name="issue" placeholder="Contoh: Sering melamun, Masalah keluarga, dll." class="w-full p-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-pink-400 outline-none" required>
            </div>

            <div class="mb-6">
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Hasil / Tindak Lanjut</label>
                <textarea name="result" rows="3" placeholder="Hasil diskusi atau saran yang diberikan..." class="w-full p-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-pink-400 outline-none"></textarea>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="flex-1 bg-gray-100 text-gray-600 py-3 rounded-lg font-bold hover:bg-gray-200 transition">Batal</button>
                <button type="submit" class="flex-1 bg-pink-600 text-white py-3 rounded-lg font-bold hover:bg-pink-700 shadow-lg transition">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div id="editModal" class="hidden fixed inset-0 z-50 overflow-auto bg-black bg-opacity-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden animate__animated animate__zoomIn animate__faster">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-700 flex items-center">
                <i class="fa-solid fa-pen-to-square mr-2 text-blue-500"></i> Edit Data Konseling
            </h3>
            <button onclick="document.getElementById('editModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <form action="/student-affairs/counseling/update" method="POST" class="p-6">
            <?= \App\Core\Csrf::input() ?>
            <input type="hidden" name="id" id="edit_id">
            
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Nama Siswa</label>
                <select name="student_id" id="edit_student" class="w-full p-2.5 border rounded-lg text-sm bg-gray-50 focus:ring-2 focus:ring-blue-400 outline-none" required>
                    <?php foreach($students as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= $s['full_name'] ?> (<?= $s['nis'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Tanggal Konseling</label>
                <input type="date" name="date" id="edit_date" class="w-full p-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-blue-400 outline-none" required>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Pokok Permasalahan</label>
                <input type="text" name="issue" id="edit_issue" class="w-full p-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-blue-400 outline-none" required>
            </div>

            <div class="mb-6">
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Hasil / Tindak Lanjut</label>
                <textarea name="result" id="edit_result" rows="3" class="w-full p-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-blue-400 outline-none"></textarea>
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
        document.getElementById('edit_student').value = btn.getAttribute('data-student');
        document.getElementById('edit_date').value = btn.getAttribute('data-date');
        document.getElementById('edit_issue').value = btn.getAttribute('data-issue');
        document.getElementById('edit_result').value = btn.getAttribute('data-result');
        
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
