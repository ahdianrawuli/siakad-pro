<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6" x-data="{ tab: '<?= $tab ?>' }">

    <!-- Header Section -->
    <div class="mb-8 bg-white p-6 rounded-2xl shadow-sm border border-slate-200 flex flex-col md:flex-row md:items-end md:justify-between gap-4">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Konfigurasi PPDB</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Kelola periode pendaftaran dan jalur masuk santri baru.</p>
            <div class="mt-3">
                <button onclick="document.getElementById('infoModal').classList.remove('hidden')"
                    class="w-7 h-7 rounded-full bg-slate-100 text-slate-500 hover:bg-blue-100 hover:text-blue-600 flex items-center justify-center transition-colors border border-slate-200" title="Panduan Penggunaan">
                    <i class="fa-solid fa-circle-info text-sm"></i>
                </button>
            </div>
        </div>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <!-- Tabs -->
    <div class="flex bg-white rounded-2xl shadow-sm border border-slate-200 mb-6 overflow-hidden">
        <button @click="tab = 'periode'; window.history.replaceState(null, '', '?tab=periode')"
            :class="tab === 'periode' ? 'border-b-2 border-blue-600 text-blue-600 bg-blue-50' : 'text-slate-500 hover:text-slate-700'"
            class="px-8 py-4 font-bold text-xs uppercase tracking-wider transition-all flex items-center gap-2">
            <i class="fa-solid fa-calendar-days"></i> Periode & Gelombang
        </button>
        <button @click="tab = 'jalur'; window.history.replaceState(null, '', '?tab=jalur')"
            :class="tab === 'jalur' ? 'border-b-2 border-blue-600 text-blue-600 bg-blue-50' : 'text-slate-500 hover:text-slate-700'"
            class="px-8 py-4 font-bold text-xs uppercase tracking-wider transition-all flex items-center gap-2">
            <i class="fa-solid fa-road"></i> Jalur & Kuota
        </button>
    </div>

    <!-- Tab: Periode -->
    <div x-show="tab === 'periode'" class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 h-fit">
            <h4 class="font-bold text-slate-700 flex items-center gap-2 border-b border-slate-100 pb-4 mb-5">
                <i class="fa-solid fa-plus-circle text-slate-400"></i> Buat Periode
            </h4>
            <form action="/ppdb/settings/period/store" method="POST" class="space-y-4">
                <?= \App\Core\Csrf::input() ?>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nama Gelombang</label>
                    <input type="text" name="name" placeholder="Contoh: Gelombang 1 2025"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" required>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1.5">Tgl Buka</label>
                        <input type="date" name="start_date" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none" required>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1.5">Tgl Tutup</label>
                        <input type="date" name="end_date" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none" required>
                    </div>
                </div>
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" class="rounded text-blue-600 focus:ring-blue-500">
                    <span class="text-sm font-semibold text-slate-600">Set sebagai Aktif</span>
                </label>
                <button type="submit" class="w-full bg-blue-600 text-white py-2.5 rounded-xl font-bold hover:bg-blue-700 shadow-md shadow-blue-500/20 transition-all text-sm">
                    <i class="fa-solid fa-save mr-2"></i> Simpan Periode
                </button>
            </form>
        </div>

        <div class="lg:col-span-2 flex flex-col gap-6">
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                <form method="GET" class="flex gap-3">
                    <input type="hidden" name="tab" value="periode">
                    <div class="flex-1 relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="search_p" value="<?= htmlspecialchars($searchP) ?>" placeholder="Cari periode..."
                            class="w-full pl-10 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
                    </div>
                    <button type="submit" class="bg-slate-800 text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-slate-900 transition-colors">Terapkan</button>
                </form>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
                <div class="overflow-x-auto">
                    <table class="min-w-full whitespace-nowrap text-left">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Periode</th>
                                <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Masa Berlaku</th>
                                <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Status</th>
                                <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            <?php if (empty($periods)): ?>
                                <tr><td colspan="4" class="px-5 py-16 text-center text-slate-400 text-sm font-medium">Belum ada data periode.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($periods as $p): ?>
                            <tr class="hover:bg-slate-50/80 transition-colors text-sm">
                                <td class="px-5 py-4 font-extrabold text-slate-800"><?= $p['name'] ?></td>
                                <td class="px-5 py-4 text-center text-xs text-slate-600">
                                    <?= date('d/m/Y', strtotime($p['start_date'])) ?> &ndash; <?= date('d/m/Y', strtotime($p['end_date'])) ?>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <?php if ($p['is_active']): ?>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-50 text-green-700 rounded-full text-[10px] font-bold border border-green-200">
                                            <i class="fa-solid fa-circle-check"></i> AKTIF
                                        </span>
                                    <?php else: ?>
                                        <a href="/ppdb/settings/period/activate?id=<?= $p['id'] ?>"
                                            class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-100 text-slate-500 rounded-full text-[10px] font-bold border border-slate-200 hover:bg-blue-50 hover:text-blue-600 transition-colors">
                                            Set Aktif
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <button onclick="openEditPeriod(this)"
                                        data-id="<?= $p['id'] ?>" data-name="<?= $p['name'] ?>"
                                        data-start="<?= $p['start_date'] ?>" data-end="<?= $p['end_date'] ?>"
                                        class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white inline-flex items-center justify-center transition-colors shadow-sm" title="Edit">
                                        <i class="fa-solid fa-pen-to-square text-sm"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php if ($totalPagesP > 1): ?>
                <div class="p-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-600">Hal <?= $currentPageP ?> / <?= $totalPagesP ?></span>
                    <div class="flex gap-1">
                        <?php $qsP = "&tab=periode&limit_p=$limitP&search_p=" . urlencode($searchP); ?>
                        <?php for ($i = 1; $i <= $totalPagesP; $i++): ?>
                            <a href="?page_p=<?= $i . $qsP ?>" class="w-8 h-8 flex items-center justify-center border rounded-lg text-xs font-bold <?= $i == $currentPageP ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' ?>"><?= $i ?></a>
                        <?php endfor; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Tab: Jalur -->
    <div x-show="tab === 'jalur'" class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 h-fit">
            <h4 class="font-bold text-slate-700 flex items-center gap-2 border-b border-slate-100 pb-4 mb-5">
                <i class="fa-solid fa-plus-circle text-slate-400"></i> Tambah Jalur
            </h4>
            <form action="/ppdb/settings/track/store" method="POST" class="space-y-4">
                <?= \App\Core\Csrf::input() ?>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nama Jalur</label>
                    <input type="text" name="name" placeholder="Reguler / Prestasi"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" required>
                </div>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1.5">Jenjang</label>
                        <select name="level" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none" required>
                            <option value="MTS">MTS</option>
                            <option value="MA">MA</option>
                            <option value="PDF">PDF</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-slate-600 mb-1.5">Kode</label>
                        <input type="text" name="code" placeholder="REG-MTS"
                            class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" required>
                    </div>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Kuota (Siswa)</label>
                    <input type="number" name="quota" placeholder="100"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" required>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2.5 rounded-xl font-bold hover:bg-blue-700 shadow-md shadow-blue-500/20 transition-all text-sm">
                    <i class="fa-solid fa-save mr-2"></i> Simpan Jalur
                </button>
            </form>
        </div>

        <div class="lg:col-span-2 flex flex-col gap-6">
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                <form method="GET" class="flex gap-3">
                    <input type="hidden" name="tab" value="jalur">
                    <div class="flex-1 relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="search_t" value="<?= htmlspecialchars($searchT) ?>" placeholder="Cari jalur..."
                            class="w-full pl-10 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
                    </div>
                    <button type="submit" class="bg-slate-800 text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-slate-900 transition-colors">Terapkan</button>
                </form>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
                <div class="overflow-x-auto">
                    <table class="min-w-full whitespace-nowrap text-left">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Jenjang</th>
                                <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Jalur</th>
                                <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Kode</th>
                                <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Kuota</th>
                                <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            <?php if (empty($tracks)): ?>
                                <tr><td colspan="5" class="px-5 py-16 text-center text-slate-400 text-sm font-medium">Belum ada data jalur.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($tracks as $t): ?>
                            <tr class="hover:bg-slate-50/80 transition-colors text-sm">
                                <td class="px-5 py-4">
                                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg border bg-blue-50 text-blue-700 border-blue-200"><?= $t['level'] ?></span>
                                </td>
                                <td class="px-5 py-4 font-extrabold text-slate-800"><?= $t['name'] ?></td>
                                <td class="px-5 py-4 font-mono text-xs text-slate-400"><?= $t['code'] ?></td>
                                <td class="px-5 py-4 text-center font-bold text-slate-700"><?= $t['quota'] ?></td>
                                <td class="px-5 py-4 text-center">
                                    <button onclick="openEditTrack(this)"
                                        data-id="<?= $t['id'] ?>" data-name="<?= $t['name'] ?>"
                                        data-level="<?= $t['level'] ?>" data-code="<?= $t['code'] ?>" data-quota="<?= $t['quota'] ?>"
                                        class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white inline-flex items-center justify-center transition-colors shadow-sm" title="Edit">
                                        <i class="fa-solid fa-pen-to-square text-sm"></i>
                                    </button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php if ($totalPagesT > 1): ?>
                <div class="p-4 bg-slate-50 border-t border-slate-200 flex items-center justify-between">
                    <span class="text-xs font-bold text-slate-600">Hal <?= $currentPageT ?> / <?= $totalPagesT ?></span>
                    <div class="flex gap-1">
                        <?php $qsT = "&tab=jalur&limit_t=$limitT&search_t=" . urlencode($searchT); ?>
                        <?php for ($i = 1; $i <= $totalPagesT; $i++): ?>
                            <a href="?page_t=<?= $i . $qsT ?>" class="w-8 h-8 flex items-center justify-center border rounded-lg text-xs font-bold <?= $i == $currentPageT ? 'bg-blue-600 text-white border-blue-600' : 'bg-white text-slate-600 border-slate-200 hover:bg-slate-50' ?>"><?= $i ?></a>
                        <?php endfor; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<!-- Modal Edit Periode -->
<div id="modalP" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-700 flex items-center gap-2"><i class="fa-solid fa-pen-to-square text-slate-400"></i> Edit Periode</h3>
            <button onclick="document.getElementById('modalP').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-slate-200 text-slate-500 hover:bg-slate-300 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="/ppdb/settings/period/update" method="POST" class="p-6 space-y-4">
            <?= \App\Core\Csrf::input() ?>
            <input type="hidden" name="id" id="edit_p_id">
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nama Gelombang</label>
                <input type="text" name="name" id="edit_p_name" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" required>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Tgl Buka</label>
                    <input type="date" name="start_date" id="edit_p_start" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Tgl Tutup</label>
                    <input type="date" name="end_date" id="edit_p_end" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm outline-none" required>
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modalP').classList.add('hidden')" class="flex-1 bg-slate-100 text-slate-600 py-2.5 rounded-xl font-bold hover:bg-slate-200 transition text-sm">Batal</button>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2.5 rounded-xl font-bold hover:bg-blue-700 shadow-md shadow-blue-500/20 transition text-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit Jalur -->
<div id="modalT" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-700 flex items-center gap-2"><i class="fa-solid fa-pen-to-square text-slate-400"></i> Edit Jalur</h3>
            <button onclick="document.getElementById('modalT').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-slate-200 text-slate-500 hover:bg-slate-300 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="/ppdb/settings/track/update" method="POST" class="p-6 space-y-4">
            <?= \App\Core\Csrf::input() ?>
            <input type="hidden" name="id" id="edit_t_id">
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nama Jalur</label>
                <input type="text" name="name" id="edit_t_name" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" required>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Jenjang</label>
                    <select name="level" id="edit_t_level" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none" required>
                        <option value="MTS">MTS</option><option value="MA">MA</option><option value="PDF">PDF</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Kode</label>
                    <input type="text" name="code" id="edit_t_code" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" required>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Kuota</label>
                <input type="number" name="quota" id="edit_t_quota" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" required>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modalT').classList.add('hidden')" class="flex-1 bg-slate-100 text-slate-600 py-2.5 rounded-xl font-bold hover:bg-slate-200 transition text-sm">Batal</button>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2.5 rounded-xl font-bold hover:bg-blue-700 shadow-md shadow-blue-500/20 transition text-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditPeriod(btn) {
        document.getElementById('edit_p_id').value = btn.dataset.id;
        document.getElementById('edit_p_name').value = btn.dataset.name;
        document.getElementById('edit_p_start').value = btn.dataset.start;
        document.getElementById('edit_p_end').value = btn.dataset.end;
        document.getElementById('modalP').classList.remove('hidden');
    }
    function openEditTrack(btn) {
        document.getElementById('edit_t_id').value = btn.dataset.id;
        document.getElementById('edit_t_name').value = btn.dataset.name;
        document.getElementById('edit_t_level').value = btn.dataset.level;
        document.getElementById('edit_t_code').value = btn.dataset.code;
        document.getElementById('edit_t_quota').value = btn.dataset.quota;
        document.getElementById('modalT').classList.remove('hidden');
    }
    function updateQueryStringParameter(uri, key, value) {
        var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        var separator = uri.indexOf('?') !== -1 ? "&" : "?";
        return uri.match(re) ? uri.replace(re, '$1' + key + "=" + value + '$2') : uri + separator + key + "=" + value;
    }
    window.onclick = function(e) {
        if (e.target == document.getElementById('infoModal')) document.getElementById('infoModal').classList.add('hidden');
    }
</script>

<!-- Modal Info -->
<div id="infoModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-blue-50 flex justify-between items-center">
            <h3 class="font-bold text-blue-800 flex items-center gap-2"><i class="fa-solid fa-circle-info text-blue-500"></i> Panduan Konfigurasi PPDB</h3>
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-blue-100 text-blue-500 hover:bg-blue-200 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="p-6 space-y-5 text-sm text-slate-600">
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">1</span> Cara Penggunaan</h4>
                <ol class="list-decimal list-inside space-y-1.5 text-slate-500">
                    <li>Tab <strong class="text-slate-700">Periode & Gelombang</strong> — atur waktu buka/tutup pendaftaran per gelombang.</li>
                    <li>Tab <strong class="text-slate-700">Jalur Masuk</strong> — buat jalur (Reguler, Tahfidz, Prestasi) beserta kuota.</li>
                    <li>Aktifkan periode dan jalur yang sedang berjalan agar pendaftar bisa mendaftar online.</li>
                </ol>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">2</span> Relasi ke Menu Lain</h4>
                <div class="space-y-2">
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-users text-blue-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Data Pendaftar</div><div class="text-[11px] text-slate-400">Pendaftar yang masuk tampil di <strong>PPDB → Data Pendaftar</strong>.</div></div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-globe text-green-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Portal PPDB Publik</div><div class="text-[11px] text-slate-400">Calon santri mendaftar melalui halaman publik PPDB online.</div></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end">
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="px-5 py-2 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700 transition">Mengerti</button>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
