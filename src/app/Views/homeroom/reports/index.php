<?php require __DIR__ . '/../../layouts/header.php'; ?>
<?php require __DIR__ . '/../../layouts/sidebar.php'; ?>

<style>
    .custom-scrollbar::-webkit-scrollbar { height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .whitespace-nowrap { white-space: nowrap; }
</style>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <div class="mb-6">
        <h3 class="text-3xl font-medium text-gray-700">Laporan Wali Kelas</h3>
        <p class="text-gray-500 text-sm">Pilih kelas untuk mencetak rekapitulasi siswa.</p>
    </div>

    <div class="space-y-4">
        
        <div class="bg-white p-4 rounded shadow-sm border border-gray-200">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <input type="hidden" name="limit" value="<?= $limit ?>">
                
                <select name="level" class="w-full p-2 border rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">-- Semua Tingkat --</option>
                    <?php foreach ($levels as $lvl): ?>
                        <option value="<?= $lvl['level'] ?>" <?= $levelFilter == $lvl['level'] ? 'selected' : '' ?>>
                            Level <?= $lvl['level'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <div class="md:col-span-2 relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                           placeholder="Cari Nama Kelas atau Wali Kelas..." 
                           class="w-full pl-10 pr-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-black transition">
                        Filter
                    </button>
                    <?php if(!empty($search) || !empty($levelFilter)): ?>
                        <a href="/homeroom/report-all" class="px-3 py-2 bg-red-100 text-red-600 rounded-lg text-sm font-bold hover:bg-red-200 text-center">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="bg-white rounded shadow-sm border border-gray-200 overflow-hidden flex flex-col">
            <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                <h4 class="font-bold text-gray-700 text-xs uppercase">Daftar Kelas</h4>
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
                            <th class="px-5 py-4 border-b text-center">Tingkat</th>
                            <th class="px-5 py-4 border-b">Nama Kelas</th>
                            <th class="px-5 py-4 border-b">Wali Kelas</th>
                            <th class="px-5 py-4 border-b text-center">Jumlah Siswa</th>
                            <th class="px-5 py-4 border-b text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if (empty($classrooms)): ?>
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-gray-400 italic text-sm">Data kelas tidak ditemukan.</td>
                        </tr>
                        <?php endif; ?>

                        <?php foreach ($classrooms as $row): ?>
                        <tr class="hover:bg-blue-50/30 transition text-sm">
                            <td class="px-5 py-4 text-center">
                                <span class="bg-gray-200 text-gray-700 text-xs font-bold px-2 py-1 rounded border border-gray-300">
                                    <?= $row['level'] ?>
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-bold text-gray-800 text-sm"><?= $row['name'] ?></div>
                            </td>
                            <td class="px-5 py-4 text-gray-600 text-sm">
                                <i class="fa-solid fa-user-tie text-blue-500 mr-2"></i>
                                <?= $row['teacher_name'] ?? '<span class="italic text-gray-400">Belum ada wali kelas</span>' ?>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded-full border border-blue-200">
                                    <?= $row['student_count'] ?> Siswa
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <a href="/homeroom/print-recap?classroom_id=<?= $row['id'] ?>" target="_blank" 
                                   class="bg-gray-800 text-white px-3 py-1.5 rounded text-xs font-bold hover:bg-black transition shadow-sm inline-flex items-center">
                                    <i class="fa-solid fa-print mr-2"></i> Cetak Rekap
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
                        $queryString = "&limit=$limit&search=" . urlencode($search) . "&level=$levelFilter"; 
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

<script>
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

<?php require __DIR__ . '/../../layouts/footer.php'; ?>
