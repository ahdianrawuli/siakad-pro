<?php require __DIR__ . '/../../layouts/header.php'; ?>
<?php require __DIR__ . '/../../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <!-- Header Section -->
    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Data Kelas</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Kelola rombongan belajar per jenjang dan tingkat.</p>
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

    <?php \App\Core\Session::flash(); ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Form Tambah -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 h-fit">
            <h4 class="font-bold text-slate-700 flex items-center gap-2 border-b border-slate-100 pb-4 mb-5">
                <i class="fa-solid fa-plus-circle text-slate-400"></i> Buat Kelas Baru
            </h4>
            <form action="/academic/classrooms/store" method="POST">
                <?= \App\Core\Csrf::input() ?>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Jenjang</label>
                    <select name="major" class="w-full py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none" required>
                        <option value="MTS">MTS</option>
                        <option value="MA">MA</option>
                        <option value="PDF">PDF</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Tingkat</label>
                    <input type="number" name="level" placeholder="7"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" required>
                </div>
                <div class="mb-4">
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nama Kelas</label>
                    <input type="text" name="name" placeholder="7-A"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" required>
                </div>
                <div class="mb-6">
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Wali Kelas</label>
                    <select name="homeroom_teacher_id" class="w-full py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                        <option value="">-- Pilih Guru --</option>
                        <?php foreach ($teachers as $t): ?>
                            <option value="<?= $t['id'] ?>"><?= $t['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2.5 rounded-xl font-bold hover:bg-blue-700 shadow-md shadow-blue-500/20 transition-all text-sm">
                    <i class="fa-solid fa-save mr-2"></i> Simpan Data Kelas
                </button>
            </form>
        </div>

        <!-- Tabel Data -->
        <div class="lg:col-span-2 flex flex-col gap-6">

            <!-- Filter -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                <form method="GET" class="flex flex-wrap items-center gap-3">
                    <input type="hidden" name="limit" value="<?= $limit ?>">
                    <div class="flex-1 min-w-[200px] relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>"
                            placeholder="Cari nama kelas..."
                            class="w-full pl-10 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
                    </div>
                    <select name="major" class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                        <option value="">Semua Jenjang</option>
                        <option value="MTS" <?= $selectedMajor == 'MTS' ? 'selected' : '' ?>>MTS</option>
                        <option value="MA" <?= $selectedMajor == 'MA' ? 'selected' : '' ?>>MA</option>
                        <option value="PDF" <?= $selectedMajor == 'PDF' ? 'selected' : '' ?>>PDF</option>
                    </select>
                    <button type="submit" class="bg-slate-800 text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-slate-900 transition-colors">
                        Terapkan
                    </button>
                    <?php if (!empty($search) || !empty($selectedMajor)): ?>
                        <a href="/academic/classrooms" class="flex items-center justify-center w-10 h-10 bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition" title="Reset">
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
                                <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Unit</th>
                                <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Tingkat</th>
                                <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Kelas</th>
                                <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Wali Kelas</th>
                                <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            <?php if (empty($classrooms)): ?>
                            <tr>
                                <td colspan="5" class="px-5 py-16 text-center text-slate-400 text-sm font-medium">Data tidak ditemukan.</td>
                            </tr>
                            <?php endif; ?>

                            <?php foreach ($classrooms as $row): ?>
                            <tr class="hover:bg-slate-50/80 transition-colors text-sm">
                                <td class="px-5 py-4">
                                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg border
                                        <?= $row['major'] == 'MTS' ? 'bg-blue-50 text-blue-700 border-blue-200' : ($row['major'] == 'MA' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-purple-50 text-purple-700 border-purple-200') ?>">
                                        <?= $row['major'] ?>
                                    </span>
                                </td>
                                <td class="px-5 py-4 font-bold text-slate-700"><?= $row['level'] ?></td>
                                <td class="px-5 py-4 font-extrabold text-slate-800"><?= $row['name'] ?></td>
                                <td class="px-5 py-4 text-slate-600">
                                    <?= $row['teacher_name'] ?? '<span class="text-red-300 text-xs italic">Belum ditentukan</span>' ?>
                                </td>
                                <td class="px-5 py-4 text-center">
                                    <a href="/academic/classrooms/delete?id=<?= $row['id'] ?>"
                                        class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white inline-flex items-center justify-center transition-colors shadow-sm"
                                        onclick="return confirm('Hapus kelas ini?')" title="Hapus">
                                        <i class="fa-solid fa-trash-can text-sm"></i>
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
                        <?php $qs = "&limit=$limit&search=" . urlencode($search) . "&major=" . urlencode($selectedMajor); ?>
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

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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
            <h3 class="font-bold text-blue-800 flex items-center gap-2"><i class="fa-solid fa-circle-info text-blue-500"></i> Panduan Data Kelas</h3>
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-blue-100 text-blue-500 hover:bg-blue-200 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="p-6 space-y-5 text-sm text-slate-600">
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">1</span> Cara Penggunaan</h4>
                <ol class="list-decimal list-inside space-y-1.5 text-slate-500">
                    <li>Klik <strong class="text-slate-700">Tambah Kelas</strong> untuk membuat rombongan belajar baru.</li>
                    <li>Pilih <strong class="text-slate-700">Unit</strong> (MTS/MA/PDF) dan isi tingkat kelas.</li>
                    <li>Gunakan filter <strong class="text-slate-700">Unit</strong> untuk menyaring kelas per jenjang.</li>
                </ol>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">2</span> Relasi ke Menu Lain</h4>
                <div class="space-y-2">
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-graduation-cap text-blue-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Data Santri</div><div class="text-[11px] text-slate-400">Santri ditempatkan ke kelas di <strong>Kesiswaan → Data Santri</strong>.</div></div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-calendar-check text-green-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Jadwal Pelajaran</div><div class="text-[11px] text-slate-400">Kelas digunakan sebagai acuan di <strong>Akademik → Jadwal Pelajaran</strong>.</div></div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-chalkboard-user text-purple-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Set Wali Kelas</div><div class="text-[11px] text-slate-400">Penugasan wali kelas diatur di <strong>Akademik → Set Wali Kelas</strong>.</div></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end">
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="px-5 py-2 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700 transition">Mengerti</button>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../../layouts/footer.php'; ?>
