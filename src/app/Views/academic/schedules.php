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
            <h3 class="text-3xl font-medium text-gray-700">Jadwal Pelajaran</h3>
            <p class="text-gray-500 text-sm">Kelola jadwal aktif tahun ajaran ini.</p>
        </div>
        
        <button onclick="document.getElementById('addModal').classList.remove('hidden')" 
           class="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-blue-700 shadow-lg transition text-sm flex items-center">
            <i class="fa-solid fa-calendar-plus mr-2"></i> Buat Jadwal Baru
        </button>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <div class="space-y-4">
        
        <div class="bg-white p-4 rounded shadow-sm border border-gray-200">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <input type="hidden" name="limit" value="<?= $limit ?>">
                
                <select name="class_id" class="w-full p-2 border rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">-- Semua Kelas --</option>
                    <?php foreach ($classrooms as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $filterClass == $c['id'] ? 'selected' : '' ?>>
                            <?= $c['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <select name="day" class="w-full p-2 border rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">-- Semua Hari --</option>
                    <?php foreach(['SENIN','SELASA','RABU','KAMIS','JUMAT','SABTU','AHAD'] as $d): ?>
                        <option value="<?= $d ?>" <?= $filterDay == $d ? 'selected' : '' ?>><?= $d ?></option>
                    <?php endforeach; ?>
                </select>

                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                           placeholder="Cari Mapel / Guru..." 
                           class="w-full pl-10 pr-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-black transition">
                        Filter
                    </button>
                    <?php if(!empty($search) || !empty($filterClass) || !empty($filterDay)): ?>
                        <a href="/academic/schedules" class="px-3 py-2 bg-red-100 text-red-600 rounded-lg text-sm font-bold hover:bg-red-200 text-center">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="bg-white rounded shadow-sm border border-gray-200 overflow-hidden flex flex-col">
            <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                <h4 class="font-bold text-gray-700 text-xs uppercase">Daftar Jadwal</h4>
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
                            <th class="px-5 py-4 border-b">Hari / Waktu</th>
                            <th class="px-5 py-4 border-b">Kelas</th>
                            <th class="px-5 py-4 border-b">Mata Pelajaran</th>
                            <th class="px-5 py-4 border-b">Guru Pengampu</th>
                            <th class="px-5 py-4 border-b text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if (empty($schedules)): ?>
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-gray-400 italic text-sm">Tidak ada jadwal yang ditemukan.</td>
                        </tr>
                        <?php endif; ?>

                        <?php foreach ($schedules as $row): ?>
                        <tr class="hover:bg-blue-50/30 transition text-sm">
                            <td class="px-5 py-4">
                                <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2 py-1 rounded">
                                    <?= $row['day'] ?>
                                </span>
                                <div class="text-xs text-gray-500 mt-1 font-mono">
                                    <?= date('H:i', strtotime($row['start_time'])) ?> - <?= date('H:i', strtotime($row['end_time'])) ?>
                                </div>
                            </td>
                            <td class="px-5 py-4 font-bold text-gray-700">
                                <?= $row['class_name'] ?>
                            </td>
                            <td class="px-5 py-4 font-bold text-blue-600">
                                <?= $row['subject_name'] ?>
                            </td>
                            <td class="px-5 py-4 text-sm text-gray-600">
                                <i class="fa-solid fa-user-tie mr-1 text-gray-400"></i> <?= $row['teacher_name'] ?>
                            </td>
                            <td class="px-5 py-4 text-center flex justify-center gap-2">
                                <button onclick="openEditModal(this)" 
                                        data-id="<?= $row['id'] ?>"
                                        data-class="<?= $row['classroom_id'] ?>"
                                        data-subject="<?= $row['subject_id'] ?>"
                                        data-teacher="<?= $row['teacher_id'] ?>"
                                        data-day="<?= $row['day'] ?>"
                                        data-start="<?= $row['start_time'] ?>"
                                        data-end="<?= $row['end_time'] ?>"
                                        class="text-blue-500 hover:text-blue-700 transition" title="Edit">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </button>
                                <a href="/academic/schedules/delete?id=<?= $row['id'] ?>" 
                                   onclick="return confirm('Hapus jadwal ini?')"
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
                    <?php 
                        $queryString = "&limit=$limit&search=" . urlencode($search) . "&class_id=$filterClass&day=$filterDay"; 
                    ?>
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

<div id="addModal" class="hidden fixed inset-0 z-50 overflow-auto bg-black bg-opacity-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden animate__animated animate__zoomIn animate__faster">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-700">Buat Jadwal Baru</h3>
            <button onclick="document.getElementById('addModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <form action="/academic/schedules/store" method="POST" class="p-6">
            <?= \App\Core\Csrf::input() ?>
            <input type="hidden" name="academic_year_id" value="<?= $academic_year_id ?>">

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Kelas</label>
                    <select name="classroom_id" class="w-full p-2.5 border rounded-lg text-sm bg-white select2" required style="width:100%">
                        <?php foreach($classrooms as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Hari</label>
                    <select name="day" class="w-full p-2.5 border rounded-lg text-sm bg-white" required>
                        <?php foreach(['SENIN','SELASA','RABU','KAMIS','JUMAT','SABTU'] as $d): ?>
                            <option value="<?= $d ?>"><?= $d ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Mata Pelajaran</label>
                <select name="subject_id" class="w-full p-2.5 border rounded-lg text-sm bg-white select2" required style="width:100%">
                    <?php foreach($subjects as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= $s['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Guru Pengampu</label>
                <select name="teacher_id" class="w-full p-2.5 border rounded-lg text-sm bg-white select2" required style="width:100%">
                    <?php foreach($teachers as $t): ?>
                        <option value="<?= $t['id'] ?>"><?= $t['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Jam Mulai</label>
                    <input type="time" name="start_time" class="w-full p-2.5 border rounded-lg text-sm" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Jam Selesai</label>
                    <input type="time" name="end_time" class="w-full p-2.5 border rounded-lg text-sm" required>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="flex-1 bg-gray-100 text-gray-600 py-3 rounded-lg font-bold hover:bg-gray-200 transition">Batal</button>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 shadow-lg transition">Simpan Jadwal</button>
            </div>
        </form>
    </div>
</div>

<div id="editModal" class="hidden fixed inset-0 z-50 overflow-auto bg-black bg-opacity-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden animate__animated animate__zoomIn animate__faster">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-700">Edit Jadwal</h3>
            <button onclick="document.getElementById('editModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <form action="/academic/schedules/update" method="POST" class="p-6">
            <?= \App\Core\Csrf::input() ?>
            <input type="hidden" name="id" id="edit_id">
            <input type="hidden" name="academic_year_id" value="<?= $academic_year_id ?>">

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Kelas</label>
                    <select name="classroom_id" id="edit_class" class="w-full p-2.5 border rounded-lg text-sm bg-white" required>
                        <?php foreach($classrooms as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Hari</label>
                    <select name="day" id="edit_day" class="w-full p-2.5 border rounded-lg text-sm bg-white" required>
                        <?php foreach(['SENIN','SELASA','RABU','KAMIS','JUMAT','SABTU'] as $d): ?>
                            <option value="<?= $d ?>"><?= $d ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Mata Pelajaran</label>
                <select name="subject_id" id="edit_subject" class="w-full p-2.5 border rounded-lg text-sm bg-white" required>
                    <?php foreach($subjects as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= $s['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Guru Pengampu</label>
                <select name="teacher_id" id="edit_teacher" class="w-full p-2.5 border rounded-lg text-sm bg-white" required>
                    <?php foreach($teachers as $t): ?>
                        <option value="<?= $t['id'] ?>"><?= $t['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-6">
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Jam Mulai</label>
                    <input type="time" name="start_time" id="edit_start" class="w-full p-2.5 border rounded-lg text-sm" required>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Jam Selesai</label>
                    <input type="time" name="end_time" id="edit_end" class="w-full p-2.5 border rounded-lg text-sm" required>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="flex-1 bg-gray-100 text-gray-600 py-3 rounded-lg font-bold hover:bg-gray-200 transition">Batal</button>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-3 rounded-lg font-bold hover:bg-blue-700 shadow-lg transition">Update Jadwal</button>
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
        document.getElementById('edit_class').value = btn.getAttribute('data-class');
        document.getElementById('edit_subject').value = btn.getAttribute('data-subject');
        document.getElementById('edit_teacher').value = btn.getAttribute('data-teacher');
        document.getElementById('edit_day').value = btn.getAttribute('data-day');
        document.getElementById('edit_start').value = btn.getAttribute('data-start');
        document.getElementById('edit_end').value = btn.getAttribute('data-end');
        
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
