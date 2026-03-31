<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<style>
    .custom-scrollbar::-webkit-scrollbar { height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: #f1f1f1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
</style>

<main class="flex-1 overflow-y-auto bg-gray-100 p-6" x-data="{ tab: '<?= $tab ?>' }">
    <div class="mb-6">
        <h3 class="text-3xl font-medium text-gray-700">Konfigurasi PPDB</h3>
        <p class="text-gray-500 text-sm">Kelola periode pendaftaran dan jalur masuk santri baru.</p>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <div class="flex border-b border-gray-200 mb-6 bg-white rounded-t-lg overflow-hidden shadow-sm">
        <button @click="tab = 'periode'; window.history.replaceState(null, '', '?tab=periode')" 
                :class="tab === 'periode' ? 'border-blue-600 text-blue-600 border-b-2 bg-blue-50' : 'text-gray-500 hover:text-gray-700'" 
                class="px-8 py-4 font-bold text-xs uppercase transition-all flex items-center gap-2">
            <i class="fa-solid fa-calendar-days"></i> Periode & Gelombang
        </button>
        <button @click="tab = 'jalur'; window.history.replaceState(null, '', '?tab=jalur')" 
                :class="tab === 'jalur' ? 'border-blue-600 text-blue-600 border-b-2 bg-blue-50' : 'text-gray-500 hover:text-gray-700'" 
                class="px-8 py-4 font-bold text-xs uppercase transition-all flex items-center gap-2">
            <i class="fa-solid fa-road"></i> Jalur & Kuota
        </button>
    </div>

    <div x-show="tab === 'periode'" class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate__animated animate__fadeIn">
        
        <div class="bg-white p-6 rounded shadow-sm border border-gray-200 h-fit">
            <h4 class="font-bold mb-4 text-gray-700 border-b pb-2 text-[10px] uppercase flex items-center gap-2">
                <i class="fa-solid fa-plus-circle text-blue-600"></i> Buat Periode
            </h4>
            <form action="/ppdb/settings/period/store" method="POST" class="space-y-4">
                <?= \App\Core\Csrf::input() ?>
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Nama Gelombang</label>
                    <input type="text" name="name" class="w-full p-2.5 border rounded text-sm outline-none focus:ring-1 focus:ring-blue-500" placeholder="Contoh: Gelombang 1 2025" required>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Tgl Buka</label>
                        <input type="date" name="start_date" class="w-full p-2.5 border rounded text-sm" required>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Tgl Tutup</label>
                        <input type="date" name="end_date" class="w-full p-2.5 border rounded text-sm" required>
                    </div>
                </div>
                <div class="pt-1">
                    <label class="flex items-center gap-2 cursor-pointer">
                        <input type="checkbox" name="is_active" class="rounded text-blue-600 focus:ring-blue-500">
                        <span class="text-xs text-gray-600 font-bold">Set sebagai Aktif</span>
                    </label>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold shadow hover:bg-blue-700 transition">Simpan Periode</button>
            </form>
        </div>

        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white p-4 rounded shadow-sm border border-gray-200 flex justify-between items-center">
                <form method="GET" class="flex-1 max-w-sm flex gap-2">
                    <input type="hidden" name="tab" value="periode">
                    <input type="text" name="search_p" value="<?= htmlspecialchars($searchP) ?>" placeholder="Cari periode..." class="w-full px-4 py-2 border rounded-lg text-sm outline-none focus:ring-1 focus:ring-blue-500">
                    <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded text-sm font-bold">Cari</button>
                </form>
                <div class="flex items-center gap-2 ml-4">
                     <span class="text-[10px] text-gray-500 font-bold uppercase">Show:</span>
                     <select onchange="window.location.href=updateQueryStringParameter(window.location.href, 'limit_p', this.value)" class="border rounded p-1 text-xs bg-white outline-none">
                        <option value="10" <?= $limitP == 10 ? 'selected' : '' ?>>10</option>
                        <option value="50" <?= $limitP == 50 ? 'selected' : '' ?>>50</option>
                    </select>
                </div>
            </div>
            
            <div class="bg-white rounded shadow-sm border border-gray-200 overflow-hidden flex flex-col">
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="min-w-full whitespace-nowrap">
                        <thead>
                            <tr class="bg-gray-100 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                <th class="px-5 py-4 border-b">Nama Periode</th>
                                <th class="px-5 py-4 border-b text-center">Masa Berlaku</th>
                                <th class="px-5 py-4 border-b text-center">Status</th>
                                <th class="px-5 py-4 border-b text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php if(empty($periods)): ?>
                                <tr><td colspan="4" class="px-5 py-8 text-center text-gray-400 italic text-sm">Belum ada data periode.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($periods as $p): ?>
                            <tr class="hover:bg-blue-50/30 transition text-sm">
                                <td class="px-5 py-4 font-bold text-blue-900"><?= $p['name'] ?></td>
                                <td class="px-5 py-4 text-center text-xs text-gray-600">
                                    <?= date('d/m/Y', strtotime($p['start_date'])) ?> - <?= date('d/m/Y', strtotime($p['end_date'])) ?>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <?php if($p['is_active']): ?>
                                        <span class="bg-green-100 text-green-700 px-2.5 py-1 rounded-full text-[10px] font-extrabold border border-green-200 uppercase">AKTIF</span>
                                    <?php else: ?>
                                        <a href="/ppdb/settings/period/activate?id=<?= $p['id'] ?>" class="text-[10px] text-gray-400 font-bold hover:text-blue-600 border border-gray-300 px-2 py-1 rounded">Set Aktif</a>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <button onclick="openEditPeriod(this)" 
                                            data-id="<?= $p['id'] ?>" 
                                            data-name="<?= $p['name'] ?>" 
                                            data-start="<?= $p['start_date'] ?>" 
                                            data-end="<?= $p['end_date'] ?>" 
                                            class="text-blue-500 hover:text-blue-700 transition" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php if($totalPagesP > 1): ?>
                <div class="p-4 bg-gray-50 border-t flex justify-between items-center">
                    <span class="text-[10px] text-gray-500 font-bold uppercase">Hal. <?= $currentPageP ?> / <?= $totalPagesP ?></span>
                    <div class="flex gap-1">
                        <?php $qsP = "&tab=periode&limit_p=$limitP&search_p=".urlencode($searchP); ?>
                        <?php for($i=1; $i<=$totalPagesP; $i++): ?>
                            <a href="?page_p=<?= $i . $qsP ?>" class="px-2.5 py-1 border rounded text-[10px] <?= $i == $currentPageP ? 'bg-blue-600 text-white border-blue-600 shadow' : 'bg-white hover:bg-gray-100' ?>"><?= $i ?></a>
                        <?php endfor; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div x-show="tab === 'jalur'" class="grid grid-cols-1 lg:grid-cols-3 gap-6 animate__animated animate__fadeIn">
        
        <div class="bg-white p-6 rounded shadow-sm border border-gray-200 h-fit">
            <h4 class="font-bold mb-4 text-gray-700 border-b pb-2 text-[10px] uppercase flex items-center gap-2">
                <i class="fa-solid fa-plus-circle text-blue-600"></i> Tambah Jalur
            </h4>
            <form action="/ppdb/settings/track/store" method="POST" class="space-y-4">
                <?= \App\Core\Csrf::input() ?>
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Nama Jalur</label>
                    <input type="text" name="name" class="w-full p-2.5 border rounded text-sm outline-none focus:ring-1 focus:ring-blue-500" placeholder="Reguler / Prestasi" required>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Jenjang</label>
                        <select name="level" class="w-full p-2.5 border rounded text-sm bg-white outline-none" required>
                            <option value="MTS">MTS</option>
                            <option value="MA">MA</option>
                            <option value="PDF">PDF</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Kode</label>
                        <input type="text" name="code" class="w-full p-2.5 border rounded text-sm outline-none" placeholder="REG-MTS" required>
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Kuota (Siswa)</label>
                    <input type="number" name="quota" class="w-full p-2.5 border rounded text-sm outline-none" placeholder="100" required>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold shadow hover:bg-blue-700 transition">Simpan Jalur</button>
            </form>
        </div>

        <div class="lg:col-span-2 space-y-4">
            <div class="bg-white p-4 rounded shadow-sm border border-gray-200 flex justify-between items-center">
                <form method="GET" class="flex-1 max-w-sm flex gap-2">
                    <input type="hidden" name="tab" value="jalur">
                    <input type="text" name="search_t" value="<?= htmlspecialchars($searchT) ?>" placeholder="Cari jalur..." class="w-full px-4 py-2 border rounded-lg text-sm outline-none focus:ring-1 focus:ring-blue-500">
                    <button type="submit" class="bg-gray-800 text-white px-4 py-2 rounded text-sm font-bold">Cari</button>
                </form>
                <div class="flex items-center gap-2 ml-4">
                     <span class="text-[10px] text-gray-500 font-bold uppercase">Show:</span>
                     <select onchange="window.location.href=updateQueryStringParameter(window.location.href, 'limit_t', this.value)" class="border rounded p-1 text-xs bg-white outline-none">
                        <option value="10" <?= $limitT == 10 ? 'selected' : '' ?>>10</option>
                        <option value="50" <?= $limitT == 50 ? 'selected' : '' ?>>50</option>
                    </select>
                </div>
            </div>
            
            <div class="bg-white rounded shadow-sm border border-gray-200 overflow-hidden flex flex-col">
                <div class="overflow-x-auto custom-scrollbar">
                    <table class="min-w-full whitespace-nowrap">
                        <thead>
                            <tr class="bg-gray-100 text-left text-[11px] font-bold text-gray-500 uppercase tracking-wider">
                                <th class="px-5 py-4 border-b">Jenjang</th>
                                <th class="px-5 py-4 border-b">Nama Jalur</th>
                                <th class="px-5 py-4 border-b">Kode</th>
                                <th class="px-5 py-4 border-b text-center">Kuota</th>
                                <th class="px-5 py-4 border-b text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <?php if(empty($tracks)): ?>
                                <tr><td colspan="5" class="px-5 py-8 text-center text-gray-400 italic text-sm">Belum ada data jalur.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($tracks as $t): ?>
                            <tr class="hover:bg-blue-50/30 transition text-sm">
                                <td class="px-5 py-4 font-extrabold text-blue-700"><?= $t['level'] ?></td>
                                <td class="px-5 py-4 font-bold text-gray-700"><?= $t['name'] ?></td>
                                <td class="px-5 py-4 font-mono text-xs text-gray-400"><?= $t['code'] ?></td>
                                <td class="px-5 py-4 text-center font-bold text-gray-600"><?= $t['quota'] ?></td>
                                <td class="px-5 py-4 text-center">
                                    <button onclick="openEditTrack(this)" 
                                            data-id="<?= $t['id'] ?>" 
                                            data-name="<?= $t['name'] ?>" 
                                            data-level="<?= $t['level'] ?>" 
                                            data-code="<?= $t['code'] ?>" 
                                            data-quota="<?= $t['quota'] ?>" 
                                            class="text-blue-500 hover:text-blue-700 transition" title="Edit">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php if($totalPagesT > 1): ?>
                <div class="p-4 bg-gray-50 border-t flex justify-between items-center">
                    <span class="text-[10px] text-gray-500 font-bold uppercase">Hal. <?= $currentPageT ?> / <?= $totalPagesT ?></span>
                    <div class="flex gap-1">
                        <?php $qsT = "&tab=jalur&limit_t=$limitT&search_t=".urlencode($searchT); ?>
                        <?php for($i=1; $i<=$totalPagesT; $i++): ?>
                            <a href="?page_t=<?= $i . $qsT ?>" class="px-2.5 py-1 border rounded text-[10px] <?= $i == $currentPageT ? 'bg-blue-600 text-white border-blue-600 shadow' : 'bg-white hover:bg-gray-100' ?>"><?= $i ?></a>
                        <?php endfor; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<div id="modalP" class="hidden fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden animate__animated animate__zoomIn animate__faster">
        <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
            <h3 class="font-bold text-gray-700 text-sm uppercase">Edit Periode</h3>
            <button onclick="document.getElementById('modalP').classList.add('hidden')" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="/ppdb/settings/period/update" method="POST" class="p-6 space-y-4">
            <?= \App\Core\Csrf::input() ?>
            <input type="hidden" name="id" id="edit_p_id">
            <div>
                <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Nama Gelombang</label>
                <input type="text" name="name" id="edit_p_name" class="w-full p-2.5 border rounded text-sm outline-none" required>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                     <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Tgl Buka</label>
                     <input type="date" name="start_date" id="edit_p_start" class="w-full p-2.5 border rounded text-sm outline-none" required>
                </div>
                <div>
                     <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Tgl Tutup</label>
                     <input type="date" name="end_date" id="edit_p_end" class="w-full p-2.5 border rounded text-sm outline-none" required>
                </div>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold shadow-lg hover:bg-blue-700 transition">Simpan Perubahan</button>
        </form>
    </div>
</div>

<div id="modalT" class="hidden fixed inset-0 z-50 bg-black bg-opacity-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden animate__animated animate__zoomIn animate__faster">
        <div class="p-4 border-b bg-gray-50 flex justify-between items-center">
            <h3 class="font-bold text-gray-700 text-sm uppercase">Edit Jalur</h3>
            <button onclick="document.getElementById('modalT').classList.add('hidden')" class="text-gray-400 hover:text-gray-600"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="/ppdb/settings/track/update" method="POST" class="p-6 space-y-4">
            <?= \App\Core\Csrf::input() ?>
            <input type="hidden" name="id" id="edit_t_id">
            <div>
                <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Nama Jalur</label>
                <input type="text" name="name" id="edit_t_name" class="w-full p-2.5 border rounded text-sm outline-none" required>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                     <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Jenjang</label>
                     <select name="level" id="edit_t_level" class="w-full p-2.5 border rounded text-sm bg-white outline-none" required>
                        <option value="MTS">MTS</option><option value="MA">MA</option><option value="PDF">PDF</option>
                     </select>
                </div>
                <div>
                     <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Kode</label>
                     <input type="text" name="code" id="edit_t_code" class="w-full p-2.5 border rounded text-sm outline-none" required>
                </div>
            </div>
            <div>
                 <label class="block text-[10px] font-bold text-gray-500 uppercase mb-1">Kuota</label>
                 <input type="number" name="quota" id="edit_t_quota" class="w-full p-2.5 border rounded text-sm outline-none" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-3 rounded-lg font-bold shadow-lg hover:bg-blue-700 transition">Simpan Perubahan</button>
        </form>
    </div>
</div>

<script>
    // Fungsi Edit Periode (Sesuai panggilan onclick di tabel)
    function openEditPeriod(btn) {
        document.getElementById('edit_p_id').value = btn.getAttribute('data-id');
        document.getElementById('edit_p_name').value = btn.getAttribute('data-name');
        document.getElementById('edit_p_start').value = btn.getAttribute('data-start');
        document.getElementById('edit_p_end').value = btn.getAttribute('data-end');
        document.getElementById('modalP').classList.remove('hidden');
    }

    // Fungsi Edit Jalur (Sesuai panggilan onclick di tabel)
    function openEditTrack(btn) {
        document.getElementById('edit_t_id').value = btn.getAttribute('data-id');
        document.getElementById('edit_t_name').value = btn.getAttribute('data-name');
        document.getElementById('edit_t_level').value = btn.getAttribute('data-level');
        document.getElementById('edit_t_code').value = btn.getAttribute('data-code');
        document.getElementById('edit_t_quota').value = btn.getAttribute('data-quota');
        document.getElementById('modalT').classList.remove('hidden');
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
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

