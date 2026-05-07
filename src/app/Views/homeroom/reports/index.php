<?php require __DIR__ . '/../../layouts/header.php'; ?>
<?php require __DIR__ . '/../../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Laporan Wali Kelas</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Pilih kelas untuk mencetak rekapitulasi siswa.</p>
            <div class="mt-3 flex items-center gap-2">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold border border-blue-100">
                    <i class="fa-solid fa-chalkboard"></i> Total Kelas: <?= $totalData ?>
                </div>
                <button onclick="document.getElementById('infoModal').classList.remove('hidden')"
                    class="w-7 h-7 rounded-full bg-slate-100 text-slate-500 hover:bg-blue-100 hover:text-blue-600 flex items-center justify-center transition-colors border border-slate-200" title="Panduan Penggunaan">
                    <i class="fa-solid fa-circle-info text-sm"></i>
                </button>
            </div>
        </div>
    </div>

    <div class="flex flex-col gap-6">

        <!-- Filter -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
            <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                <input type="hidden" name="limit" value="<?= $limit ?>">
                <select name="level" class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                    <option value="">Semua Tingkat</option>
                    <?php foreach ($levels as $lvl): ?>
                        <option value="<?= $lvl['level'] ?>" <?= $levelFilter == $lvl['level'] ? 'selected' : '' ?>>Level <?= $lvl['level'] ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="md:col-span-2 relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari Nama Kelas atau Wali Kelas..."
                        class="w-full pl-10 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-slate-800 text-white py-2.5 rounded-xl text-sm font-bold hover:bg-slate-900 transition-colors">Terapkan</button>
                    <?php if (!empty($search) || !empty($levelFilter)): ?>
                        <a href="/homeroom/report-all" class="flex items-center justify-center w-10 h-10 bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition" title="Reset">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
            <div class="overflow-x-auto">
                <table class="min-w-full whitespace-nowrap text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Tingkat</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Kelas</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Wali Kelas</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Jumlah Siswa</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <?php if (empty($classrooms)): ?>
                        <tr><td colspan="5" class="px-5 py-16 text-center text-slate-400 text-sm font-medium">Data kelas tidak ditemukan.</td></tr>
                        <?php endif; ?>

                        <?php foreach ($classrooms as $row): ?>
                        <tr class="hover:bg-slate-50/80 transition-colors text-sm">
                            <td class="px-5 py-4 text-center">
                                <span class="px-2.5 py-1 bg-slate-100 text-slate-700 text-xs font-bold rounded-lg border border-slate-200"><?= $row['level'] ?></span>
                            </td>
                            <td class="px-5 py-4 font-extrabold text-slate-800"><?= $row['name'] ?></td>
                            <td class="px-5 py-4 text-slate-600">
                                <i class="fa-solid fa-user-tie text-blue-400 mr-1.5"></i>
                                <?= $row['teacher_name'] ?? '<span class="italic text-slate-400 text-xs">Belum ada wali kelas</span>' ?>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <span class="inline-flex items-center gap-1 px-2.5 py-1 bg-blue-50 text-blue-700 text-xs font-bold rounded-full border border-blue-200">
                                    <?= $row['student_count'] ?> Siswa
                                </span>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <a href="/homeroom/print-recap?classroom_id=<?= $row['id'] ?>" target="_blank"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-800 text-white rounded-lg text-xs font-bold hover:bg-slate-900 transition-colors shadow-sm">
                                    <i class="fa-solid fa-print"></i> Cetak Rekap
                                </a>
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
                    <?php $qs = "&limit=$limit&search=" . urlencode($search) . "&level=$levelFilter"; ?>
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
</main>

<!-- Modal Info -->
<div id="infoModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-blue-50 flex justify-between items-center">
            <h3 class="font-bold text-blue-800 flex items-center gap-2">
                <i class="fa-solid fa-circle-info text-blue-500"></i> Panduan Laporan Wali Kelas
            </h3>
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-blue-100 text-blue-500 hover:bg-blue-200 flex items-center justify-center transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="p-6 space-y-5 text-sm text-slate-600 overflow-y-auto max-h-[70vh]">

            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">1</span>
                    Apa itu Laporan Wali Kelas?
                </h4>
                <p class="text-slate-500 leading-relaxed">Halaman ini menampilkan daftar seluruh kelas beserta wali kelasnya. Setiap kelas dapat dicetak <strong class="text-slate-700">Rekap Siswa</strong> yang berisi data lengkap siswa aktif di kelas tersebut.</p>
            </div>

            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">2</span>
                    Cara Penggunaan
                </h4>
                <ol class="list-decimal list-inside space-y-1.5 text-slate-500">
                    <li>Gunakan filter <strong class="text-slate-700">Tingkat</strong> atau <strong class="text-slate-700">Nama Kelas</strong> untuk mempersempit pencarian.</li>
                    <li>Klik tombol <strong class="text-slate-700">Cetak Rekap</strong> pada baris kelas yang diinginkan.</li>
                    <li>Halaman cetak akan terbuka di tab baru — gunakan <kbd class="px-1.5 py-0.5 bg-slate-100 rounded text-xs font-mono">Ctrl+P</kbd> untuk mencetak.</li>
                </ol>
            </div>

            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">3</span>
                    Isi Rekap yang Dicetak
                </h4>
                <ul class="space-y-1.5 text-slate-500">
                    <li class="flex items-center gap-2"><i class="fa-solid fa-check text-green-500 text-xs"></i> Daftar nama siswa aktif beserta NIS</li>
                    <li class="flex items-center gap-2"><i class="fa-solid fa-check text-green-500 text-xs"></i> Data orang tua / wali siswa</li>
                    <li class="flex items-center gap-2"><i class="fa-solid fa-check text-green-500 text-xs"></i> Informasi kelas dan wali kelas</li>
                    <li class="flex items-center gap-2"><i class="fa-solid fa-check text-green-500 text-xs"></i> Jumlah total siswa di kelas</li>
                </ul>
            </div>

            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">4</span>
                    Relasi ke Menu Lain
                </h4>
                <div class="space-y-2">
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-chalkboard text-blue-400 w-5 text-center"></i>
                        <div>
                            <div class="font-semibold text-slate-700 text-xs">Data Kelas</div>
                            <div class="text-[11px] text-slate-400">Kelas yang tampil di sini diambil dari menu <strong>Master → Data Kelas</strong>. Tambah atau edit kelas di sana.</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-users text-green-400 w-5 text-center"></i>
                        <div>
                            <div class="font-semibold text-slate-700 text-xs">Data Siswa</div>
                            <div class="text-[11px] text-slate-400">Jumlah siswa per kelas diambil dari menu <strong>Kesiswaan → Data Siswa</strong>.</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-chalkboard-user text-purple-400 w-5 text-center"></i>
                        <div>
                            <div class="font-semibold text-slate-700 text-xs">Manajemen Guru</div>
                            <div class="text-[11px] text-slate-400">Wali kelas ditentukan di menu <strong>Kepegawaian → Manajemen Guru</strong> atau saat membuat kelas baru.</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-file-lines text-orange-400 w-5 text-center"></i>
                        <div>
                            <div class="font-semibold text-slate-700 text-xs">Laporan Nilai / Rapor</div>
                            <div class="text-[11px] text-slate-400">Rekap wali kelas ini melengkapi laporan nilai dan rapor yang ada di menu <strong>Akademik → Nilai</strong>.</div>
                        </div>
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
    function updateQueryStringParameter(uri, key, value) {
        var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        var separator = uri.indexOf('?') !== -1 ? "&" : "?";
        return uri.match(re) ? uri.replace(re, '$1' + key + "=" + value + '$2') : uri + separator + key + "=" + value;
    }
    window.onclick = function(e) { if (e.target == document.getElementById('infoModal')) document.getElementById('infoModal').classList.add('hidden'); }
</script>

<?php require __DIR__ . '/../../layouts/footer.php'; ?>
