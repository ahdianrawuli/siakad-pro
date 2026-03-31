<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<style>
    .custom-scrollbar::-webkit-scrollbar { height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .whitespace-nowrap { white-space: nowrap; }
</style>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <div class="mb-6 flex justify-between items-end">
        <div>
            <h3 class="text-3xl font-medium text-gray-700">Absensi Siswa</h3>
            <p class="text-gray-500 text-sm">Riwayat kehadiran siswa.</p>
        </div>
        
        <a href="/student-affairs/attendance/create" 
           class="bg-blue-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-blue-700 shadow-lg transition text-sm flex items-center">
            <i class="fa-solid fa-calendar-check mr-2"></i> Input Absensi
        </a>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <div class="space-y-4">
        
        <div class="bg-white p-4 rounded shadow-sm border border-gray-200">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-3">
                <input type="hidden" name="limit" value="<?= $limit ?>">
                
                <div class="relative">
                    <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                           placeholder="Cari Nama / NIS..." 
                           class="w-full pl-10 pr-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none">
                </div>

                <select name="class_id" class="w-full p-2 border rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500 outline-none">
                    <option value="">- Semua Kelas -</option>
                    <?php foreach ($classrooms as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $classFilter == $c['id'] ? 'selected' : '' ?>>
                            <?= $c['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <input type="date" name="date" value="<?= $dateFilter ?>" 
                       class="w-full p-2 border rounded-lg text-sm bg-white focus:ring-2 focus:ring-blue-500 outline-none">

                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-gray-800 text-white px-4 py-2 rounded-lg text-sm font-bold hover:bg-black transition">
                        Filter
                    </button>
                    <?php if(!empty($search) || !empty($dateFilter) || !empty($classFilter)): ?>
                        <a href="/student-affairs/attendance" class="px-3 py-2 bg-red-100 text-red-600 rounded-lg text-sm font-bold hover:bg-red-200 text-center">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <div class="bg-white rounded shadow-sm border border-gray-200 overflow-hidden flex flex-col">
            <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                <h4 class="font-bold text-gray-700 text-xs uppercase">Data Log Kehadiran</h4>
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
                            <th class="px-5 py-4 border-b text-center">Status</th>
                            <th class="px-5 py-4 border-b">Keterangan</th>
                            <th class="px-5 py-4 border-b">Pencatat</th>
                            <th class="px-5 py-4 border-b text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if (empty($logs)): ?>
                        <tr>
                            <td colspan="7" class="px-5 py-12 text-center text-gray-400 italic text-sm">Belum ada data absensi yang sesuai filter.</td>
                        </tr>
                        <?php endif; ?>

                        <?php foreach ($logs as $row): ?>
                        <tr class="hover:bg-blue-50/30 transition text-sm">
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
                            <td class="px-5 py-4 text-center">
                                <?php
                                    $badges = [
                                        'H' => 'bg-green-100 text-green-700 border-green-200',
                                        'S' => 'bg-yellow-100 text-yellow-700 border-yellow-200',
                                        'I' => 'bg-blue-100 text-blue-700 border-blue-200',
                                        'A' => 'bg-red-100 text-red-700 border-red-200'
                                    ];
                                    $labels = [
                                        'H' => 'Hadir', 'S' => 'Sakit', 'I' => 'Izin', 'A' => 'Alpa'
                                    ];
                                ?>
                                <span class="px-2 py-0.5 text-[10px] font-extrabold rounded border <?= $badges[$row['status']] ?? 'bg-gray-100 text-gray-600' ?>">
                                    <?= strtoupper($labels[$row['status']] ?? $row['status']) ?>
                                </span>
                            </td>
                            <td class="px-5 py-4 text-xs text-gray-500 italic">
                                <?= $row['notes'] ?: '-' ?>
                            </td>
                            <td class="px-5 py-4 text-xs text-gray-500">
                                <?= $row['recorder_name'] ?? 'System' ?>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <a href="/student-affairs/attendance/delete?id=<?= $row['id'] ?>" 
                                   onclick="return confirm('Hapus log absensi ini?')"
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
                        $queryString = "&limit=$limit&search=" . urlencode($search) . "&date=$dateFilter&class_id=$classFilter"; 
                    ?>
                    <?php if($currentPage > 1): ?>
                        <a href="?page=<?= $currentPage - 1 . $queryString ?>" class="px-3 py-1 bg-white border rounded text-xs hover:bg-gray-100 transition">Prev</a>
                    <?php endif; ?>
                    
                    <?php 
                    $start = max(1, $currentPage - 2);
                    $end = min($totalPages, $currentPage + 2);
                    for($i = $start; $i <= $end; $i++): 
                    ?>
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

<?php require __DIR__ . '/../layouts/footer.php'; ?>
