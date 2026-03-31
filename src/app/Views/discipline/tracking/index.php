<?php require __DIR__ . '/../../layouts/header.php'; ?>
<?php require __DIR__ . '/../../layouts/sidebar.php'; ?>

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
            <h3 class="text-3xl font-medium text-gray-700">Pelacakan Santri</h3>
            <p class="text-gray-500 text-sm">Monitoring lokasi dan aktivitas harian santri.</p>
        </div>
        
        <button onclick="document.getElementById('addModal').classList.remove('hidden')" 
           class="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-blue-700 shadow-lg transition text-sm flex items-center">
            <i class="fa-solid fa-map-location-dot mr-2"></i> Catat Aktivitas
        </button>
    </div>
    
    <?php \App\Core\Session::flash(); ?>

    <div class="space-y-4">
        
        <div class="bg-white p-4 rounded shadow-sm border border-gray-200">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <input type="hidden" name="limit" value="<?= $limit ?>">
                
                <input type="date" name="date" value="<?= $date ?>" 
                       class="w-full p-2 border rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500 outline-none">

                <div class="md:col-span-2 relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                           placeholder="Cari Nama Santri atau Lokasi..." 
                           class="w-full pl-10 pr-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-black transition">
                        Filter
                    </button>
                    <?php if(!empty($search) || $date != date('Y-m-d')): ?>
                        <a href="/discipline/tracking" class="px-3 py-2 bg-red-100 text-red-600 rounded-lg text-sm font-bold hover:bg-red-200 text-center">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="bg-white rounded shadow-sm border border-gray-200 overflow-hidden flex flex-col">
            <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                <h4 class="font-bold text-gray-700 text-xs uppercase">Log Aktivitas (<?= date('d M Y', strtotime($date)) ?>)</h4>
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
                            <th class="px-5 py-4 border-b">Jam</th>
                            <th class="px-5 py-4 border-b">Santri</th>
                            <th class="px-5 py-4 border-b">Jenis</th>
                            <th class="px-5 py-4 border-b">Lokasi</th>
                            <th class="px-5 py-4 border-b">Keterangan</th>
                            <th class="px-5 py-4 border-b">Pelapor</th>
                            <th class="px-5 py-4 border-b text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if(empty($logs)): ?>
                            <tr><td colspan="7" class="px-5 py-12 text-center text-gray-400 italic text-sm">Tidak ada data aktivitas.</td></tr>
                        <?php endif; ?>

                        <?php foreach($logs as $log): ?>
                        <tr class="hover:bg-blue-50/30 transition text-sm">
                            <td class="px-5 py-4 font-mono text-blue-600 font-bold text-xs">
                                <?= date('H:i', strtotime($log['logged_at'])) ?>
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-bold text-gray-800"><?= $log['full_name'] ?></div>
                                <div class="text-[10px] text-gray-500">NIS: <?= $log['nis'] ?></div>
                            </td>
                            <td class="px-5 py-4">
                                 <span class="px-2 py-1 rounded text-[10px] font-extrabold border 
                                    <?= $log['activity_type'] == 'LOCATION' ? 'bg-green-50 text-green-700 border-green-200' : 
                                       ($log['activity_type'] == 'INCIDENT' ? 'bg-red-50 text-red-700 border-red-200' : 'bg-blue-50 text-blue-700 border-blue-200') ?>">
                                    <?= $log['activity_type'] ?>
                                </span>
                            </td>
                            <td class="px-5 py-4 font-medium text-gray-700"><?= $log['location'] ?></td>
                            <td class="px-5 py-4 text-xs text-gray-600 italic max-w-xs truncate"><?= $log['description'] ?: '-' ?></td>
                            <td class="px-5 py-4 text-xs text-gray-500"><?= $log['reporter_name'] ?></td>
                            <td class="px-5 py-4 text-center flex justify-center gap-2">
                                <button onclick="openEditModal(this)" 
                                        data-id="<?= $log['id'] ?>"
                                        data-student="<?= $log['student_id'] ?>"
                                        data-type="<?= $log['activity_type'] ?>"
                                        data-location="<?= htmlspecialchars($log['location']) ?>"
                                        data-desc="<?= htmlspecialchars($log['description']) ?>"
                                        data-date="<?= date('Y-m-d', strtotime($log['logged_at'])) ?>"
                                        data-time="<?= date('H:i', strtotime($log['logged_at'])) ?>"
                                        class="text-blue-500 hover:text-blue-700 transition" title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <a href="/discipline/tracking/delete?id=<?= $log['id'] ?>" class="text-red-400 hover:text-red-600 transition" onclick="return confirm('Hapus log ini?')">
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
                    <?php $queryString = "&limit=$limit&search=" . urlencode($search) . "&date=$date"; ?>
                    <?php for($i = 1; $i <= $totalPages; $i++): ?>
                        <a href="?page=<?= $i . $queryString ?>" class="px-3 py-1 border rounded text-xs transition <?= $i == $currentPage ? 'bg-blue-600 text-white border-blue-600 font-bold' : 'bg-white hover:bg-gray-100' ?>">
                            <?= $i ?>
                        </a>
                    <?php endfor; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<div id="addModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl w-full max-w-lg shadow-2xl overflow-hidden animate__animated animate__zoomIn animate__faster">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-700">Catat Aktivitas Baru</h3>
            <button onclick="document.getElementById('addModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <form action="/discipline/tracking/store" method="POST" class="p-6">
            <?= \App\Core\Csrf::input() ?>
            
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Pilih Santri</label>
                <select name="student_id" class="w-full p-2.5 border rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500 outline-none select2" required style="width: 100%">
                    <?php foreach($students as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= $s['full_name'] ?> (<?= $s['nis'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Tanggal</label>
                    <input type="date" name="date" value="<?= date('Y-m-d') ?>" class="w-full p-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Jam</label>
                    <input type="time" name="time" value="<?= date('H:i') ?>" class="w-full p-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Jenis Log</label>
                    <select name="activity_type" class="w-full p-2.5 border rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="LOCATION">Cek Lokasi</option>
                        <option value="ACTIVITY">Kegiatan</option>
                        <option value="INCIDENT">Insiden/Kejadian</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Lokasi</label>
                    <input type="text" name="location" class="w-full p-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Kantin/Masjid..." required>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Keterangan Detil</label>
                <textarea name="description" class="w-full p-2.5 border rounded-lg text-sm h-20 focus:ring-2 focus:ring-blue-500 outline-none" placeholder="Sedang makan siang..."></textarea>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="flex-1 bg-gray-100 text-gray-600 py-3 rounded-lg font-bold hover:bg-gray-200 transition">Batal</button>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 shadow-lg transition">Simpan</button>
            </div>
        </form>
    </div>
</div>

<div id="editModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-xl w-full max-w-lg shadow-2xl overflow-hidden animate__animated animate__zoomIn animate__faster">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-700">Edit Log Aktivitas</h3>
            <button onclick="document.getElementById('editModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <form action="/discipline/tracking/update" method="POST" class="p-6">
            <?= \App\Core\Csrf::input() ?>
            <input type="hidden" name="id" id="edit_id">
            
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Pilih Santri</label>
                <select name="student_id" id="edit_student" class="w-full p-2.5 border rounded-lg text-sm bg-gray-50 focus:ring-2 focus:ring-blue-500 outline-none" required>
                    <?php foreach($students as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= $s['full_name'] ?> (<?= $s['nis'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Tanggal</label>
                    <input type="date" name="date" id="edit_date" class="w-full p-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Jam</label>
                    <input type="time" name="time" id="edit_time" class="w-full p-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Jenis Log</label>
                    <select name="activity_type" id="edit_type" class="w-full p-2.5 border rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500 outline-none">
                        <option value="LOCATION">Cek Lokasi</option>
                        <option value="ACTIVITY">Kegiatan</option>
                        <option value="INCIDENT">Insiden/Kejadian</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Lokasi</label>
                    <input type="text" name="location" id="edit_location" class="w-full p-2.5 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none" required>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Keterangan Detil</label>
                <textarea name="description" id="edit_desc" class="w-full p-2.5 border rounded-lg text-sm h-20 focus:ring-2 focus:ring-blue-500 outline-none"></textarea>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="flex-1 bg-gray-100 text-gray-600 py-3 rounded-lg font-bold hover:bg-gray-200 transition">Batal</button>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 shadow-lg transition">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() { 
        $('.select2').select2({ dropdownParent: $('#addModal') }); 
    });

    function openEditModal(btn) {
        document.getElementById('edit_id').value = btn.getAttribute('data-id');
        document.getElementById('edit_student').value = btn.getAttribute('data-student');
        document.getElementById('edit_date').value = btn.getAttribute('data-date');
        document.getElementById('edit_time').value = btn.getAttribute('data-time');
        document.getElementById('edit_type').value = btn.getAttribute('data-type');
        document.getElementById('edit_location').value = btn.getAttribute('data-location');
        document.getElementById('edit_desc').value = btn.getAttribute('data-desc');
        
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

<?php require __DIR__ . '/../../layouts/footer.php'; ?>
