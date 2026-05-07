<?php require __DIR__ . '/../../layouts/header.php'; ?>
<?php require __DIR__ . '/../../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6" x-data="{ pathModal: false, batchModal: false }">

    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Pengaturan PPDB</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Kelola jalur pendaftaran dan gelombang PPDB sekolah.</p>
            <div class="mt-3">
                <button onclick="document.getElementById('infoModal').classList.remove('hidden')"
                    class="w-7 h-7 rounded-full bg-slate-100 text-slate-500 hover:bg-blue-100 hover:text-blue-600 flex items-center justify-center transition-colors border border-slate-200" title="Panduan Penggunaan">
                    <i class="fa-solid fa-circle-info text-sm"></i>
                </button>
            </div>
        </div>
        <div class="flex gap-2">
            <button @click="pathModal = true" class="px-4 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold shadow-md shadow-blue-500/20 hover:bg-blue-700 transition-all flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Tambah Jalur
            </button>
            <button @click="batchModal = true" class="px-4 py-2.5 bg-indigo-600 text-white rounded-xl text-sm font-semibold shadow-md shadow-indigo-500/20 hover:bg-indigo-700 transition-all flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Tambah Gelombang
            </button>
        </div>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <div class="flex flex-col gap-6">

        <!-- Jalur Pendaftaran -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-5 border-b border-slate-100 bg-slate-50">
                <h4 class="font-bold text-slate-700 flex items-center gap-2">
                    <i class="fa-solid fa-code-branch text-blue-500"></i> Jalur Pendaftaran
                </h4>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full whitespace-nowrap text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Jalur</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Jenjang</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Kode</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Kuota</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Status</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <?php if (!empty($paths)): foreach ($paths as $path): ?>
                        <tr class="hover:bg-slate-50/80 transition-colors text-sm">
                            <td class="px-5 py-4 font-extrabold text-slate-800"><?= htmlspecialchars($path['name']) ?></td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg border
                                    <?= $path['level'] === 'MTS' ? 'bg-blue-50 text-blue-700 border-blue-200' : ($path['level'] === 'MA' ? 'bg-purple-50 text-purple-700 border-purple-200' : 'bg-green-50 text-green-700 border-green-200') ?>">
                                    <?= $path['level'] ?>
                                </span>
                            </td>
                            <td class="px-5 py-4 font-mono text-xs text-slate-400"><?= htmlspecialchars($path['code']) ?></td>
                            <td class="px-5 py-4 text-center font-bold text-slate-700"><?= number_format($path['quota']) ?></td>
                            <td class="px-5 py-4 text-center">
                                <?php if ($path['is_active']): ?>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-50 text-green-700 rounded-full text-[10px] font-bold border border-green-200">
                                        <i class="fa-solid fa-circle-check"></i> Aktif
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-100 text-slate-500 rounded-full text-[10px] font-bold border border-slate-200">
                                        Nonaktif
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <form action="/school/ppdb/path/toggle/<?= $path['id'] ?>" method="POST" class="inline">
                                    <button type="submit" class="px-3 py-1.5 text-[11px] font-bold rounded-lg border transition
                                        <?= $path['is_active'] ? 'border-red-200 text-red-600 bg-red-50 hover:bg-red-100' : 'border-green-200 text-green-600 bg-green-50 hover:bg-green-100' ?>">
                                        <?= $path['is_active'] ? 'Nonaktifkan' : 'Aktifkan' ?>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                            <tr><td colspan="6" class="px-5 py-16 text-center text-slate-400 text-sm font-medium">Belum ada jalur pendaftaran.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Gelombang PPDB -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-5 border-b border-slate-100 bg-slate-50">
                <h4 class="font-bold text-slate-700 flex items-center gap-2">
                    <i class="fa-solid fa-layer-group text-indigo-500"></i> Gelombang PPDB
                </h4>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full whitespace-nowrap text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Gelombang</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Periode Pendaftaran</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Status</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <?php if (!empty($batches)): foreach ($batches as $b): ?>
                        <tr class="hover:bg-slate-50/80 transition-colors text-sm <?= $b['is_active'] ? 'bg-green-50/40' : '' ?>">
                            <td class="px-5 py-4 font-extrabold text-slate-800"><?= htmlspecialchars($b['name']) ?></td>
                            <td class="px-5 py-4 text-slate-600 text-xs">
                                <?= date('d M Y', strtotime($b['start_date'])) ?> &mdash; <?= date('d M Y', strtotime($b['end_date'])) ?>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <?php if ($b['is_active']): ?>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-50 text-green-700 rounded-full text-[10px] font-bold border border-green-200">
                                        <i class="fa-solid fa-circle-check"></i> AKTIF
                                    </span>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-100 text-slate-500 rounded-full text-[10px] font-bold border border-slate-200">
                                        NONAKTIF
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <?php if (!$b['is_active']): ?>
                                <form action="/school/ppdb/batch/activate/<?= $b['id'] ?>" method="POST" class="inline">
                                    <button type="submit" class="px-3 py-1.5 text-[11px] font-bold rounded-lg border border-green-200 text-green-600 bg-green-50 hover:bg-green-100 transition">
                                        <i class="fa-solid fa-power-off mr-1"></i> Aktifkan
                                    </button>
                                </form>
                                <?php else: ?>
                                    <span class="text-xs text-green-600 font-semibold"><i class="fa-solid fa-check mr-1"></i>Sedang Aktif</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; else: ?>
                            <tr><td colspan="4" class="px-5 py-16 text-center text-slate-400 text-sm font-medium">Belum ada gelombang PPDB.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<!-- Modal: Tambah Jalur -->
<div x-cloak x-show="pathModal" class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div @click.away="pathModal = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-code-branch text-slate-400"></i> Tambah Jalur Pendaftaran
            </h3>
            <button @click="pathModal = false" class="w-8 h-8 rounded-lg bg-slate-200 text-slate-500 hover:bg-slate-300 flex items-center justify-center transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="/school/ppdb/path/store" method="POST" class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nama Jalur</label>
                <input type="text" name="name" required placeholder="cth: Reguler, Prestasi, Beasiswa"
                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Jenjang</label>
                    <select name="level" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                        <option value="MTS">MTS</option>
                        <option value="MA">MA</option>
                        <option value="PDF">PDF</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Kode</label>
                    <input type="text" name="code" required placeholder="cth: REG-MTS"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Kuota (Siswa)</label>
                <input type="number" name="quota" required placeholder="cth: 100"
                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" @click="pathModal = false" class="flex-1 bg-slate-100 text-slate-600 py-2.5 rounded-xl font-bold hover:bg-slate-200 transition text-sm">Batal</button>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2.5 rounded-xl font-bold hover:bg-blue-700 shadow-md shadow-blue-500/20 transition text-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Tambah Gelombang -->
<div x-cloak x-show="batchModal" class="fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div @click.away="batchModal = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-layer-group text-slate-400"></i> Tambah Gelombang PPDB
            </h3>
            <button @click="batchModal = false" class="w-8 h-8 rounded-lg bg-slate-200 text-slate-500 hover:bg-slate-300 flex items-center justify-center transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <form action="/school/ppdb/batch/store" method="POST" class="p-6 space-y-4">
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nama Gelombang</label>
                <input type="text" name="name" required placeholder="cth: Gelombang 1 (2026/2027)"
                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-indigo-500/50 outline-none transition-all">
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Tanggal Mulai</label>
                    <input type="date" name="start_date" required
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-indigo-500/50 outline-none transition-all">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Tanggal Selesai</label>
                    <input type="date" name="end_date" required
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-indigo-500/50 outline-none transition-all">
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" @click="batchModal = false" class="flex-1 bg-slate-100 text-slate-600 py-2.5 rounded-xl font-bold hover:bg-slate-200 transition text-sm">Batal</button>
                <button type="submit" class="flex-1 bg-indigo-600 text-white py-2.5 rounded-xl font-bold hover:bg-indigo-700 shadow-md shadow-indigo-500/20 transition text-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Info -->
<div id="infoModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-blue-50 flex justify-between items-center">
            <h3 class="font-bold text-blue-800 flex items-center gap-2"><i class="fa-solid fa-circle-info text-blue-500"></i> Panduan Pengaturan PPDB</h3>
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-blue-100 text-blue-500 hover:bg-blue-200 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="p-6 space-y-5 text-sm text-slate-600">
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">1</span> Cara Penggunaan</h4>
                <ol class="list-decimal list-inside space-y-1.5 text-slate-500">
                    <li>Klik <strong class="text-slate-700">Tambah Jalur</strong> untuk membuat jalur pendaftaran (Reguler, Tahfidz, Prestasi).</li>
                    <li>Klik <strong class="text-slate-700">Tambah Gelombang</strong> untuk mengatur periode waktu pendaftaran.</li>
                    <li>Aktifkan jalur dan gelombang yang sedang berjalan agar pendaftar bisa mendaftar online.</li>
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

<script>
    window.onclick = function(e) {
        if (e.target == document.getElementById('infoModal')) document.getElementById('infoModal').classList.add('hidden');
    }
</script>

<?php require __DIR__ . '/../../layouts/footer.php'; ?>
