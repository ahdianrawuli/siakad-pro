<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<style>
    .custom-scrollbar::-webkit-scrollbar { height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .whitespace-nowrap { white-space: nowrap; }
    #addModal.hidden { display: none; opacity: 0; }
</style>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <div class="mb-6 flex justify-between items-end">
        <div>
            <h3 class="text-3xl font-medium text-gray-700">Kedisiplinan Siswa</h3>
            <p class="text-gray-500 text-sm">Catatan pelanggaran dan poin sanksi.</p>
        </div>
        
        <button onclick="document.getElementById('addModal').classList.remove('hidden')" 
           class="bg-red-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-red-700 shadow-lg transition text-sm flex items-center">
            <i class="fa-solid fa-triangle-exclamation mr-2"></i> Catat Pelanggaran
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
                           placeholder="Cari Nama Siswa atau NIS..." 
                           class="w-full pl-10 pr-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <input type="date" name="date" value="<?= $dateFilter ?>" 
                       class="w-full p-2 border rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500 outline-none">

                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-black transition">
                        Filter Data
                    </button>
                    <?php if(!empty($search) || !empty($dateFilter)): ?>
                        <a href="/student-affairs/discipline" class="px-3 py-2 bg-red-100 text-red-600 rounded-lg text-sm font-bold hover:bg-red-200 text-center">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="bg-white rounded shadow-sm border border-gray-200 overflow-hidden flex flex-col">
            <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                <h4 class="font-bold text-gray-700 text-xs uppercase">Riwayat Pelanggaran</h4>
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
                            <th class="px-5 py-4 border-b">Jenis Pelanggaran</th>
                            <th class="px-5 py-4 border-b text-center">Poin</th>
                            <th class="px-5 py-4 border-b text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if (empty($violations)): ?>
                        <tr>
                            <td colspan="6" class="px-5 py-12 text-center text-gray-400 italic text-sm">Belum ada data pelanggaran yang tercatat.</td>
                        </tr>
                        <?php endif; ?>

                        <?php foreach ($violations as $row): ?>
                        <tr class="hover:bg-red-50/30 transition text-sm">
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
                                <div class="font-bold text-red-600 text-xs mb-1"><?= $row['violation_name'] ?></div>
                                <div class="text-[10px] text-gray-500 italic max-w-xs truncate"><?= $row['note'] ?: 'Tidak ada catatan khusus.' ?></div>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="px-2 py-1 text-xs font-extrabold rounded bg-red-100 text-red-700 border border-red-200">
                                    +<?= $row['points'] ?>
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <a href="/student-affairs/discipline/delete?id=<?= $row['id'] ?>" 
                                   onclick="return confirm('Hapus data pelanggaran ini? Poin siswa akan dikembalikan.')"
                                   class="text-red-400 hover:text-red-600 transition bg-red-50 p-2 rounded-full" title="Hapus">
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
                    Hal. <?= $currentPage ?> / <?= $totalPages ?> (Total <?= $totalData ?> pelanggaran)
                </div>
                <div class="flex gap-1">
                    <?php 
                        $queryString = "&limit=$limit&search=" . urlencode($search) . "&date=$dateFilter"; 
                    ?>
                    <?php if($currentPage > 1): ?>
                        <a href="?page=<?= $currentPage - 1 . $queryString ?>" class="px-3 py-1 bg-white border rounded text-xs hover:bg-gray-100 transition">Prev</a>
                    <?php endif; ?>
                    
                    <?php 
                    $start = max(1, $currentPage - 2);
                    $end = min($totalPages, $currentPage + 2);
                    for($i = $start; $i <= $end; $i++): 
                    ?>
                        <a href="?page=<?= $i . $queryString ?>" class="px-3 py-1 border rounded text-xs transition <?= $i == $currentPage ? 'bg-red-600 text-white border-red-600 font-bold' : 'bg-white hover:bg-gray-100' ?>">
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
</main>

<div id="addModal" class="hidden fixed inset-0 z-50 overflow-auto bg-black bg-opacity-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden animate__animated animate__zoomIn animate__faster">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center bg-gray-50">
            <h3 class="font-bold text-gray-700 flex items-center">
                <i class="fa-solid fa-triangle-exclamation mr-2 text-red-600"></i> Catat Pelanggaran Baru
            </h3>
            <button onclick="document.getElementById('addModal').classList.add('hidden')" class="text-gray-400 hover:text-gray-600">
                <i class="fa-solid fa-xmark text-xl"></i>
            </button>
        </div>
        <form action="/student-affairs/discipline/store" method="POST" class="p-6">
            <?= \App\Core\Csrf::input() ?>
            
            <div class="mb-4">
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Pilih Siswa</label>
                <select name="student_id" class="w-full p-2.5 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-red-500 outline-none" required>
                    <option value="">-- Cari Nama Siswa --</option>
                    <?php foreach($students as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= $s['full_name'] ?> (<?= $s['nis'] ?>)</option>
                    <?php endforeach; ?>
                </select>
                <p class="text-[10px] text-gray-400 mt-1">*Notifikasi WhatsApp akan otomatis dikirim ke Wali Murid.</p>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Jenis Pelanggaran</label>
                    <select name="violation_type_id" class="w-full p-2.5 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-red-500 outline-none" required>
                        <?php foreach($types as $t): ?>
                            <option value="<?= $t['id'] ?>"><?= $t['name'] ?> (+<?= $t['points'] ?> Poin)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Tanggal Kejadian</label>
                    <input type="date" name="date" value="<?= date('Y-m-d') ?>" class="w-full p-2.5 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-red-500 outline-none" required>
                </div>
            </div>

            <div class="mb-6">
                <label class="block text-xs font-bold text-gray-600 uppercase mb-1">Catatan / Kronologi (Opsional)</label>
                <textarea name="note" rows="3" placeholder="Jelaskan detail kejadian..." class="w-full p-2.5 border border-gray-300 rounded-lg text-sm bg-white focus:ring-2 focus:ring-red-500 outline-none"></textarea>
            </div>

            <div class="flex gap-3">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="flex-1 bg-gray-100 text-gray-600 py-3 rounded-lg font-bold hover:bg-gray-200 transition">Batal</button>
                <button type="submit" class="flex-1 bg-red-600 text-white py-3 rounded-lg font-bold hover:bg-red-700 shadow-lg transition">Simpan Data</button>
            </div>
        </form>
    </div>
</div>

<script>
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
    }
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
