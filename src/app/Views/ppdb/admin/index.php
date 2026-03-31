<?php require __DIR__ . '/../../layouts/header.php'; ?>
<?php require __DIR__ . '/../../layouts/sidebar.php'; ?>

<style>
    .custom-scrollbar::-webkit-scrollbar { height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
</style>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6">
    <div class="mb-6 flex justify-between items-end">
        <div>
            <h3 class="text-3xl font-medium text-gray-700">Data Pendaftar PPDB</h3>
            <p class="text-gray-500 text-sm">Mengelola calon santri baru. Total pendaftar: <?= $totalData ?></p>
        </div>
    </div>
    
    <?php \App\Core\Session::flash(); ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <div class="bg-white p-6 rounded shadow-sm h-fit border border-gray-200">
            <h4 class="font-bold mb-4 text-gray-700 border-b pb-2 flex items-center text-sm">
                <i class="fa-solid fa-user-plus mr-2 text-blue-600"></i> Pendaftaran Offline
            </h4>
            <form action="/ppdb/registrations/store" method="POST" class="space-y-4">
                <?= \App\Core\Csrf::input() ?>
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Nama Lengkap</label>
                    <input type="text" name="full_name" placeholder="Nama Calon Santri" class="w-full p-2 border rounded text-sm outline-none focus:ring-2 focus:ring-blue-500" required>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Gender</label>
                        <select name="gender" class="w-full p-2 border rounded text-sm bg-white" required>
                            <option value="L">Laki-laki</option>
                            <option value="P">Perempuan</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Jalur</label>
                        <select name="ppdb_track_id" class="w-full p-2 border rounded text-sm bg-white" required>
                            <?php foreach($tracks as $t): ?>
                                <option value="<?= $t['id'] ?>"><?= $t['name'] ?> (<?= $t['level'] ?>)</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">No. WhatsApp</label>
                    <input type="text" name="whatsapp_number" placeholder="08xxxx" class="w-full p-2 border rounded text-sm" required>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded font-bold hover:bg-blue-700 shadow transition">
                    Daftarkan Calon Santri
                </button>
            </form>
        </div>

        <div class="lg:col-span-2 space-y-4">
            
            <div class="bg-white p-4 rounded shadow-sm border border-gray-200">
                <form method="GET" class="flex flex-wrap items-center gap-2">
                    <input type="hidden" name="limit" value="<?= $limit ?>">
                    
                    <div class="flex-1 min-w-[200px]">
                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" 
                               placeholder="Cari Nama / No. Reg..." 
                               class="w-full px-3 py-2 border rounded text-sm focus:ring-2 focus:ring-blue-500 outline-none border-gray-300">
                    </div>

                    <select name="track_id" class="px-2 py-2 border rounded text-sm border-gray-300 bg-white">
                        <option value="">Semua Jalur</option>
                        <?php foreach($tracks as $t): ?>
                            <option value="<?= $t['id'] ?>" <?= $selectedTrack == $t['id'] ? 'selected' : '' ?>><?= $t['name'] ?></option>
                        <?php endforeach; ?>
                    </select>

                    <select name="status" class="px-2 py-2 border rounded text-sm border-gray-300 bg-white">
                        <option value="">Semua Status</option>
                        <option value="PENDING" <?= $selectedStatus == 'PENDING' ? 'selected' : '' ?>>Pending</option>
                        <option value="PAID" <?= $selectedStatus == 'PAID' ? 'selected' : '' ?>>Paid</option>
                        <option value="ACCEPTED" <?= $selectedStatus == 'ACCEPTED' ? 'selected' : '' ?>>Accepted</option>
                        <option value="REJECTED" <?= $selectedStatus == 'REJECTED' ? 'selected' : '' ?>>Rejected</option>
                    </select>

                    <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded text-sm font-bold hover:bg-black transition">
                        Filter
                    </button>
                    
                    <?php if(!empty($search) || !empty($selectedStatus) || !empty($selectedTrack)): ?>
                        <a href="/ppdb/registrations" class="text-red-500 text-[10px] font-bold underline">Reset</a>
                    <?php endif; ?>
                </form>
            </div>

            <div class="bg-white rounded shadow-sm border border-gray-200 overflow-hidden flex flex-col">
                <div class="p-4 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
                    <h4 class="font-bold text-gray-700 text-[10px] uppercase">Daftar Calon Santri</h4>
                    <div class="flex items-center gap-2">
                        <span class="text-[10px] text-gray-500 font-bold">Show:</span>
                        <select onchange="window.location.href=updateQueryStringParameter(window.location.href, 'limit', this.value)" class="border rounded p-1 text-xs outline-none">
                            <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10</option>
                            <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50</option>
                        </select>
                    </div>
                </div>
                
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="min-w-full whitespace-nowrap">
                        <thead>
                            <tr class="bg-gray-100 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                <th class="px-5 py-4 border-b">No. Reg / Tgl</th>
                                <th class="px-5 py-4 border-b">Kandidat</th>
                                <th class="px-5 py-4 border-b">Jalur</th>
                                <th class="px-5 py-4 border-b text-center">Keuangan</th>
                                <th class="px-5 py-4 border-b text-center">Status</th>
                                <th class="px-5 py-4 border-b text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php if (empty($candidates)): ?>
                            <tr><td colspan="6" class="px-5 py-12 text-center text-gray-400 italic text-sm">Data pendaftar tidak ditemukan.</td></tr>
                            <?php endif; ?>

                            <?php foreach ($candidates as $row): ?>
                            <tr class="hover:bg-blue-50/30 transition text-sm">
                                <td class="px-5 py-4">
                                    <div class="font-bold text-blue-800"><?= $row['registration_no'] ?></div>
                                    <div class="text-[10px] text-gray-400"><?= date('d/m/Y H:i', strtotime($row['created_at'])) ?></div>
                                </td>
                                <td class="px-5 py-4">
                                    <div class="font-bold text-gray-700 uppercase"><?= $row['full_name'] ?></div>
                                    <div class="text-[10px] text-gray-500"><?= $row['gender'] == 'L' ? 'Laki-laki' : 'Perempuan' ?> • <?= $row['whatsapp_number'] ?></div>
                                </td>
                                <td class="px-5 py-4">
                                    <span class="px-2 py-0.5 bg-blue-50 text-blue-700 text-[10px] font-bold rounded border border-blue-200"><?= $row['level'] ?></span>
                                    <div class="text-[10px] text-gray-500 mt-1"><?= $row['track_name'] ?></div>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <?php if($row['payment_status'] == 'VERIFIED'): ?>
                                        <span class="text-green-600 font-bold text-xs"><i class="fa-solid fa-check-double mr-1"></i>LUNAS</span>
                                    <?php elseif($row['payment_status'] == 'PENDING'): ?>
                                        <span class="text-orange-500 font-bold text-xs animate-pulse">VERIFIKASI</span>
                                    <?php else: ?>
                                        <span class="text-gray-400 text-xs">BELUM</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <?php 
                                    $statusColors = [
                                        'PENDING' => 'bg-gray-100 text-gray-500 border-gray-200',
                                        'PAID' => 'bg-blue-100 text-blue-700 border-blue-200',
                                        'ACCEPTED' => 'bg-green-100 text-green-700 border-green-200',
                                        'REJECTED' => 'bg-red-100 text-red-700 border-red-200'
                                    ];
                                    $cls = $statusColors[$row['registration_status']] ?? 'bg-gray-100 text-gray-500';
                                    ?>
                                    <span class="px-2.5 py-1 rounded-full text-[10px] font-extrabold border <?= $cls ?>">
                                        <?= $row['registration_status'] ?>
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <a href="/ppdb/registrations/detail?id=<?= $row['id'] ?>" class="inline-flex items-center justify-center w-8 h-8 bg-blue-50 text-blue-600 rounded hover:bg-blue-600 hover:text-white transition shadow-sm">
                                        <i class="fa-solid fa-arrow-right text-xs"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <?php if($totalPages > 1): ?>
                <div class="p-4 bg-gray-50 border-t border-gray-200 flex items-center justify-between">
                    <div class="text-[10px] text-gray-500 font-bold uppercase">Halaman <?= $currentPage ?> dari <?= $totalPages ?></div>
                    <div class="flex gap-1">
                        <?php $qs = "&limit=$limit&search=".urlencode($search)."&status=$selectedStatus&track_id=$selectedTrack"; ?>
                        <?php if($currentPage > 1): ?>
                            <a href="?page=<?= $currentPage - 1 . $qs ?>" class="px-2.5 py-1 bg-white border rounded text-[10px] font-bold hover:bg-gray-100">Prev</a>
                        <?php endif; ?>
                        <?php for($i=1; $i<=$totalPages; $i++): ?>
                            <a href="?page=<?= $i . $qs ?>" class="px-2.5 py-1 border rounded text-[10px] <?= $i == $currentPage ? 'bg-blue-600 text-white border-blue-600 font-bold shadow' : 'bg-white hover:bg-gray-100' ?>"><?= $i ?></a>
                        <?php endfor; ?>
                        <?php if($currentPage < $totalPages): ?>
                            <a href="?page=<?= $currentPage + 1 . $qs ?>" class="px-2.5 py-1 bg-white border rounded text-[10px] font-bold hover:bg-gray-100">Next</a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
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
