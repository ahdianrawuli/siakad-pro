<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<style>
    .custom-scrollbar::-webkit-scrollbar { height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .whitespace-nowrap { white-space: nowrap; }
</style>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <div class="mb-6">
        <h3 class="text-3xl font-medium text-gray-700">Data Orang Tua & Wali</h3>
        <p class="text-gray-500 text-sm">Mengelola data kontak orang tua siswa aktif.</p>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <div class="space-y-4">
        
        <div class="bg-white p-4 rounded shadow-sm border border-gray-200">
            <form method="GET" class="flex flex-wrap items-center gap-3">
                <input type="hidden" name="limit" value="<?= $limit ?>">
                
                <div class="flex-1 min-w-[300px]">
                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-gray-400"></i>
                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                               placeholder="Cari Nama Siswa, Nama Ayah, atau Nama Ibu..." 
                               class="w-full pl-10 pr-4 py-2 border rounded-lg text-sm focus:ring-2 focus:ring-blue-500 outline-none border-gray-300">
                    </div>
                </div>

                <button type="submit" class="bg-gray-800 text-white px-6 py-2 rounded-lg text-sm font-bold hover:bg-black transition">
                    Filter
                </button>
                
                <?php if(!empty($search)): ?>
                    <a href="/student-affairs/parents" class="text-red-500 text-xs font-bold underline">Reset</a>
                <?php endif; ?>
            </form>
        </div>

        <div class="bg-white rounded shadow-sm border border-gray-200 overflow-hidden flex flex-col">
            <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                <h4 class="font-bold text-gray-700 text-xs uppercase">Daftar Orang Tua</h4>
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
                            <th class="px-5 py-4 border-b">Identitas Siswa</th>
                            <th class="px-5 py-4 border-b">Data Ayah</th>
                            <th class="px-5 py-4 border-b">Data Ibu</th>
                            <th class="px-5 py-4 border-b">Data Wali</th>
                            <th class="px-5 py-4 border-b text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <?php if (empty($parents)): ?>
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-gray-400 italic text-sm">Data tidak ditemukan.</td>
                        </tr>
                        <?php endif; ?>

                        <?php foreach ($parents as $row): ?>
                        <tr class="hover:bg-blue-50/30 transition text-sm">
                            <td class="px-5 py-4">
                                <div class="font-bold text-gray-800"><?= htmlspecialchars($row['full_name']) ?></div>
                                <div class="text-xs text-blue-600 font-mono">NIS: <?= $row['nis'] ?></div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-bold text-gray-700 text-xs mb-1">
                                    <i class="fa-solid fa-user-tie text-blue-400 mr-1"></i> <?= htmlspecialchars($row['father_name'] ?? '-') ?>
                                </div>
                                <?php if(!empty($row['father_phone'])): ?>
                                    <a href="https://wa.me/<?= preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $row['father_phone'])) ?>" target="_blank" class="text-[10px] bg-green-100 text-green-700 px-1.5 py-0.5 rounded border border-green-200 hover:bg-green-200">
                                        <i class="fa-brands fa-whatsapp"></i> <?= $row['father_phone'] ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-[10px] text-gray-400 italic">No Phone</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-4">
                                <div class="font-bold text-gray-700 text-xs mb-1">
                                    <i class="fa-solid fa-user text-pink-400 mr-1"></i> <?= htmlspecialchars($row['mother_name'] ?? '-') ?>
                                </div>
                                <?php if(!empty($row['mother_phone'])): ?>
                                    <a href="https://wa.me/<?= preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $row['mother_phone'])) ?>" target="_blank" class="text-[10px] bg-green-100 text-green-700 px-1.5 py-0.5 rounded border border-green-200 hover:bg-green-200">
                                        <i class="fa-brands fa-whatsapp"></i> <?= $row['mother_phone'] ?>
                                    </a>
                                <?php else: ?>
                                    <span class="text-[10px] text-gray-400 italic">No Phone</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-4 text-xs text-gray-600">
                                <?php if(!empty($row['guardian_name'])): ?>
                                    <div class="font-bold"><?= htmlspecialchars($row['guardian_name']) ?></div>
                                    <div class="text-[10px] text-gray-500">Hub: <?= $row['guardian_relation'] ?? '-' ?></div>
                                <?php else: ?>
                                    <span class="text-gray-400">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <a href="/student-affairs/parents/edit?id=<?= $row['id'] ?>" class="text-blue-600 hover:text-blue-800 bg-blue-50 px-3 py-1.5 rounded text-xs font-bold border border-blue-200 transition">
                                    <i class="fa-solid fa-pen-to-square mr-1"></i> Edit
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
                    Hal. <?= $currentPage ?> / <?= $totalPages ?> (Total <?= $totalData ?> Data)
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
