<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <!-- Header Section -->
    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Tahun Ajaran</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Kelola periode akademik aktif dan arsip.</p>
            <div class="mt-3 flex items-center gap-2">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold border border-blue-100">
                    <i class="fa-solid fa-calendar-days"></i> Total Periode: <?= $totalData ?>
                </div>
                <button onclick="document.getElementById('infoModal').classList.remove('hidden')"
                    class="w-7 h-7 rounded-full bg-slate-100 text-slate-500 hover:bg-blue-100 hover:text-blue-600 flex items-center justify-center transition-colors border border-slate-200" title="Panduan Penggunaan">
                    <i class="fa-solid fa-circle-info text-sm"></i>
                </button>
            </div>
        </div>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Form Tambah -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 h-fit">
            <h4 class="font-bold text-slate-700 flex items-center gap-2 border-b border-slate-100 pb-4 mb-5">
                <i class="fa-solid fa-plus-circle text-slate-400"></i> Buat Periode Baru
            </h4>
            <form action="/academic/years/store" method="POST">
                <?= \App\Core\Csrf::input() ?>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nama Tahun</label>
                    <input type="text" name="name" placeholder="Contoh: 2025/2026"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" required>
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Semester</label>
                    <select name="semester"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" required>
                        <option value="Ganjil">Ganjil</option>
                        <option value="Genap">Genap</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2.5 rounded-xl font-bold hover:bg-blue-700 shadow-md shadow-blue-500/20 transition-all text-sm">
                    <i class="fa-solid fa-save mr-2"></i> Simpan Periode
                </button>
            </form>
        </div>

        <!-- Tabel Data -->
        <div class="lg:col-span-2 flex flex-col gap-6">

            <!-- Filter -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                <form method="GET" class="flex flex-wrap items-center gap-3">
                    <input type="hidden" name="limit" value="<?= $limit ?>">
                    <div class="flex-1 min-w-[220px] relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                            placeholder="Cari tahun ajaran..."
                            class="w-full pl-10 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
                    </div>
                    <button type="submit" class="bg-slate-800 text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-slate-900 transition-colors">
                        Terapkan
                    </button>
                    <?php if (!empty($search)): ?>
                        <a href="/academic/years" class="flex items-center justify-center w-10 h-10 bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition" title="Reset">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Table Card -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
                <div class="overflow-x-auto">
                    <table class="min-w-full whitespace-nowrap text-left">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Tahun Pelajaran</th>
                                <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Semester</th>
                                <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Status</th>
                                <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Aksi</th>
                                <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Edit/Hapus</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            <?php if (empty($years)): ?>
                            <tr>
                                <td colspan="4" class="px-5 py-16 text-center text-slate-400 text-sm font-medium">Data tidak ditemukan.</td>
                            </tr>
                            <?php endif; ?>

                            <?php foreach ($years as $row): ?>
                            <tr class="hover:bg-slate-50/80 transition-colors text-sm">
                                <td class="px-5 py-4 font-extrabold text-slate-800"><?= $row['name'] ?></td>
                                <td class="px-5 py-4">
                                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg border <?= $row['semester'] == 'Ganjil' ? 'bg-orange-50 text-orange-700 border-orange-200' : 'bg-blue-50 text-blue-700 border-blue-200' ?>">
                                        <?= strtoupper($row['semester']) ?>
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <?php if ($row['is_active']): ?>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-50 text-green-700 rounded-full text-[10px] font-bold border border-green-200">
                                            <i class="fa-solid fa-circle-check"></i> AKTIF
                                        </span>
                                    <?php else: ?>
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-slate-100 text-slate-500 rounded-full text-[10px] font-bold border border-slate-200">
                                            ARSIP
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <?php if (!$row['is_active']): ?>
                                        <a href="/academic/years/activate?id=<?= $row['id'] ?>"
                                            class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg text-[11px] font-bold border border-blue-200 hover:bg-blue-600 hover:text-white transition-colors"
                                            onclick="return confirm('Aktifkan periode ini? Periode lain akan dinonaktifkan secara otomatis.')">
                                            <i class="fa-solid fa-toggle-on"></i> SET AKTIF
                                        </a>
                                    <?php else: ?>
                                        <div class="w-7 h-7 bg-green-500 rounded-full inline-flex items-center justify-center shadow-sm">
                                            <i class="fa-solid fa-check text-white text-[10px]"></i>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button onclick='openEditYear(<?= json_encode($row) ?>)'
                                            class="w-8 h-8 rounded-lg bg-blue-50 text-blue-500 hover:bg-blue-500 hover:text-white flex items-center justify-center transition" title="Edit">
                                            <i class="fa-solid fa-pen-to-square text-xs"></i>
                                        </button>
                                        <?php if (!$row['is_active']): ?>
                                        <a href="/academic/years/delete?id=<?= $row['id'] ?>"
                                            onclick="return confirm('Hapus tahun ajaran ini?')"
                                            class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white flex items-center justify-center transition" title="Hapus">
                                            <i class="fa-solid fa-trash-can text-xs"></i>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Footer -->
                <div class="p-4 bg-slate-50 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <span class="text-xs text-slate-500 font-semibold uppercase tracking-wider">Show:</span>
                        <select onchange="window.location.href=updateQueryStringParameter(window.location.href, 'limit', this.value)"
                            class="border border-slate-300 rounded-lg px-2 py-1 text-sm outline-none focus:ring-2 focus:ring-blue-500/50 bg-white font-medium">
                            <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10 entries</option>
                            <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50 entries</option>
                            <option value="100" <?= $limit == 100 ? 'selected' : '' ?>>100 entries</option>
                        </select>
                    </div>

                    <?php if ($totalPages > 1): ?>
                    <div class="flex items-center gap-1.5">
                        <?php $qs = "&limit=$limit&search=" . urlencode($search); ?>
                        <?php if ($currentPage > 1): ?>
                            <a href="?page=<?= $currentPage - 1 . $qs ?>" class="w-8 h-8 flex items-center justify-center bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-colors shadow-sm"><i class="fa-solid fa-chevron-left"></i></a>
                        <?php endif; ?>
                        <span class="text-xs font-bold text-slate-600 px-2">Hal <?= $currentPage ?> / <?= $totalPages ?></span>
                        <?php if ($currentPage < $totalPages): ?>
                            <a href="?page=<?= $currentPage + 1 . $qs ?>" class="w-8 h-8 flex items-center justify-center bg-white border border-slate-200 rounded-lg text-xs font-bold text-slate-600 hover:bg-slate-50 hover:text-blue-600 transition-colors shadow-sm"><i class="fa-solid fa-chevron-right"></i></a>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
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
    window.onclick = function(e) {
        if (e.target == document.getElementById('infoModal')) document.getElementById('infoModal').classList.add('hidden');
    }
</script>

<!-- Modal Info -->
<div id="infoModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-blue-50 flex justify-between items-center">
            <h3 class="font-bold text-blue-800 flex items-center gap-2"><i class="fa-solid fa-circle-info text-blue-500"></i> Panduan Tahun Ajaran</h3>
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-blue-100 text-blue-500 hover:bg-blue-200 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="p-6 space-y-5 text-sm text-slate-600">
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">1</span> Cara Penggunaan</h4>
                <ol class="list-decimal list-inside space-y-1.5 text-slate-500">
                    <li>Isi nama tahun (cth: <code class="bg-slate-100 px-1 rounded">2025/2026</code>) dan pilih semester, lalu klik <strong class="text-slate-700">Simpan</strong>.</li>
                    <li>Klik <strong class="text-slate-700">Aktifkan</strong> pada periode yang ingin dijadikan tahun ajaran berjalan.</li>
                    <li>Hanya satu periode yang bisa aktif dalam satu waktu.</li>
                </ol>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">2</span> Relasi ke Menu Lain</h4>
                <div class="space-y-2">
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-calendar-check text-green-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Jadwal Pelajaran</div><div class="text-[11px] text-slate-400">Jadwal dibuat per tahun ajaran di <strong>Akademik → Jadwal Pelajaran</strong>.</div></div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-star text-blue-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Ekskul & Nilai</div><div class="text-[11px] text-slate-400">Data anggota ekskul dan nilai mengacu pada tahun ajaran aktif.</div></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end">
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="px-5 py-2 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700 transition">Mengerti</button>
        </div>
    </div>
</div>

<!-- Modal Edit Tahun Ajaran -->
<div id="editYearModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-700"><i class="fa-solid fa-pen-to-square text-slate-400 mr-2"></i>Edit Tahun Ajaran</h3>
            <button onclick="document.getElementById('editYearModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-slate-200 text-slate-500 hover:bg-slate-300 flex items-center justify-center"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="/academic/years/update" method="POST" class="p-6 space-y-4">
            <?= \App\Core\Csrf::input() ?>
            <input type="hidden" name="id" id="ey_id">
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nama Tahun Ajaran</label>
                <input type="text" name="name" id="ey_name" required placeholder="2025/2026"
                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Semester</label>
                <select name="semester" id="ey_semester" class="w-full py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                    <option value="Ganjil">Ganjil</option>
                    <option value="Genap">Genap</option>
                </select>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('editYearModal').classList.add('hidden')" class="flex-1 bg-slate-100 text-slate-600 py-2.5 rounded-xl font-bold text-sm">Batal</button>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2.5 rounded-xl font-bold hover:bg-blue-700 transition text-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
function openEditYear(r) {
    document.getElementById('ey_id').value       = r.id;
    document.getElementById('ey_name').value     = r.name;
    document.getElementById('ey_semester').value = r.semester;
    document.getElementById('editYearModal').classList.remove('hidden');
}
window.addEventListener('click', function(e){
    if (e.target == document.getElementById('editYearModal')) document.getElementById('editYearModal').classList.add('hidden');
});
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
