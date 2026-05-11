<?php require __DIR__ . '/../../layouts/header.php'; ?>
<?php require __DIR__ . '/../../layouts/sidebar.php'; ?>

<?php $days = ['SETIAP HARI','SENIN','SELASA','RABU','KAMIS','JUMAT','SABTU','MINGGU']; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Jadwal Kegiatan Asrama</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Kelola jadwal harian kegiatan santri di asrama.</p>
            <div class="mt-3 flex items-center gap-2">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold border border-blue-100">
                    <i class="fa-solid fa-calendar-days"></i> Total Kegiatan: <?= $totalData ?>
                </div>
                <button onclick="document.getElementById('infoModal').classList.remove('hidden')"
                    class="w-7 h-7 rounded-full bg-slate-100 text-slate-500 hover:bg-blue-100 hover:text-blue-600 flex items-center justify-center transition-colors border border-slate-200" title="Panduan Penggunaan">
                    <i class="fa-solid fa-circle-info text-sm"></i>
                </button>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="/asrama/activities/print?day=<?= urlencode($selectedDay) ?>" target="_blank"
                class="px-4 py-2.5 bg-slate-600 text-white rounded-xl text-sm font-semibold hover:bg-slate-700 transition flex items-center gap-2 w-fit">
                <i class="fa-solid fa-print"></i> Cetak
            </a>
            <button onclick="document.getElementById('addModal').classList.remove('hidden')"
                class="px-4 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold shadow-md shadow-blue-500/20 hover:bg-blue-700 transition-all flex items-center gap-2 w-fit">
                <i class="fa-solid fa-plus"></i> Tambah Kegiatan
            </button>
        </div>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <div class="flex flex-col gap-6">

        <!-- Filter -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
            <form method="GET" class="flex flex-wrap items-center gap-3">
                <input type="hidden" name="limit" value="<?= $limit ?>">
                <div class="flex-1 min-w-[200px] relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nama kegiatan..."
                        class="w-full pl-10 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
                </div>
                <select name="day" class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                    <option value="">Semua Hari</option>
                    <?php foreach ($days as $d): ?>
                        <option value="<?= $d ?>" <?= $selectedDay == $d ? 'selected' : '' ?>><?= $d ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="bg-slate-800 text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-slate-900 transition-colors">Terapkan</button>
                <?php if (!empty($search) || !empty($selectedDay)): ?>
                    <a href="/boarding/activities" class="flex items-center justify-center w-10 h-10 bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition" title="Reset">
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
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Hari</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Jam</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Kegiatan</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Keterangan</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <?php if (empty($activities)): ?>
                            <tr><td colspan="5" class="px-5 py-16 text-center text-slate-400 text-sm font-medium">Belum ada jadwal kegiatan.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($activities as $act): ?>
                        <tr class="hover:bg-slate-50/80 transition-colors text-sm">
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg border <?= $act['day'] == 'SETIAP HARI' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-slate-100 text-slate-600 border-slate-200' ?>">
                                    <?= $act['day'] ?>
                                </span>
                            </td>
                            <td class="px-5 py-4 font-mono text-xs text-slate-600">
                                <?= substr($act['start_time'],0,5) ?> – <?= substr($act['end_time'],0,5) ?>
                            </td>
                            <td class="px-5 py-4 font-extrabold text-slate-800"><?= $act['name'] ?></td>
                            <td class="px-5 py-4 text-slate-500 text-xs italic max-w-xs truncate"><?= $act['description'] ?: '-' ?></td>
                            <td class="px-5 py-4 text-center">
                                <a href="/asrama/activities/delete?id=<?= $act['id'] ?>"
                                    onclick="return confirm('Hapus kegiatan ini?')"
                                    class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white inline-flex items-center justify-center transition-colors shadow-sm" title="Hapus">
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
                    <select onchange="window.location.href=updateQS(window.location.href,'limit',this.value)"
                        class="border border-slate-300 rounded-lg px-2 py-1 text-sm outline-none bg-white font-medium">
                        <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10 entries</option>
                        <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50 entries</option>
                    </select>
                </div>
                <?php if ($totalPages > 1): ?>
                <div class="flex items-center gap-1.5">
                    <?php $qs = "&limit=$limit&search=" . urlencode($search) . "&day=" . urlencode($selectedDay); ?>
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
            <h3 class="font-bold text-blue-800 flex items-center gap-2"><i class="fa-solid fa-circle-info text-blue-500"></i> Panduan Kegiatan Asrama</h3>
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-blue-100 text-blue-500 hover:bg-blue-200 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="p-6 space-y-5 text-sm text-slate-600 overflow-y-auto max-h-[70vh]">
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">1</span> Cara Penggunaan</h4>
                <ol class="list-decimal list-inside space-y-1.5 text-slate-500">
                    <li>Klik <strong class="text-slate-700">Tambah Kegiatan</strong> untuk menambah jadwal baru.</li>
                    <li>Isi nama kegiatan, hari, jam mulai–selesai, dan keterangan.</li>
                    <li>Gunakan filter <strong class="text-slate-700">Hari</strong> untuk melihat jadwal hari tertentu.</li>
                    <li>Pilih <strong class="text-slate-700">SETIAP HARI</strong> untuk kegiatan rutin harian.</li>
                </ol>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">2</span> Relasi ke Menu Lain</h4>
                <div class="space-y-2">
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-user-shield text-purple-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Wali Asrama</div><div class="text-[11px] text-slate-400">Wali asrama bertanggung jawab memastikan kegiatan ini berjalan sesuai jadwal.</div></div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-book-quran text-green-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Absensi Tilawah</div><div class="text-[11px] text-slate-400">Kegiatan tilawah/tahfidz berkaitan dengan absensi di menu <strong>Asrama → Tilawah</strong>.</div></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end">
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="px-5 py-2 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700 transition">Mengerti</button>
        </div>
    </div>
</div>

<!-- Modal Tambah -->
<div id="addModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-700 flex items-center gap-2"><i class="fa-solid fa-calendar-plus text-slate-400"></i> Tambah Kegiatan Baru</h3>
            <button onclick="document.getElementById('addModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-slate-200 text-slate-500 hover:bg-slate-300 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="/asrama/activities/store" method="POST" class="p-6 space-y-4">
            <?= \App\Core\Csrf::input() ?>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nama Kegiatan</label>
                <input type="text" name="name" placeholder="cth: Sholat Subuh Berjamaah"
                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Hari</label>
                <select name="day" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                    <?php foreach ($days as $d): ?>
                        <option value="<?= $d ?>"><?= $d ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Jam Mulai</label>
                    <input type="time" name="start_time" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Jam Selesai</label>
                    <input type="time" name="end_time" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none" required>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Keterangan <span class="text-slate-400 font-normal">(Opsional)</span></label>
                <textarea name="description" rows="2" placeholder="cth: Wajib diikuti seluruh santri putra"
                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all resize-none"></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="flex-1 bg-slate-100 text-slate-600 py-2.5 rounded-xl font-bold hover:bg-slate-200 transition text-sm">Batal</button>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2.5 rounded-xl font-bold hover:bg-blue-700 shadow-md shadow-blue-500/20 transition text-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
    function updateQS(uri, key, value) {
        var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        var sep = uri.indexOf('?') !== -1 ? "&" : "?";
        return uri.match(re) ? uri.replace(re, '$1' + key + "=" + value + '$2') : uri + sep + key + "=" + value;
    }
    window.onclick = function(e) {
        ['addModal','infoModal'].forEach(function(id) {
            if (e.target == document.getElementById(id)) document.getElementById(id).classList.add('hidden');
        });
    }
</script>

<?php require __DIR__ . '/../../layouts/footer.php'; ?>
