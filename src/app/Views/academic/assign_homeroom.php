<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single { border-color: #e2e8f0; height: 38px; padding-top: 4px; border-radius: 0.75rem; background-color: #f8fafc; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 36px; }
    .select2-container { width: 100% !important; }
    .select2-dropdown { border-color: #e2e8f0; border-radius: 0.75rem; overflow: hidden; }
    .select2-search--dropdown .select2-search__field { border-radius: 0.5rem; border-color: #e2e8f0; padding: 6px 10px; }
</style>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Atur Wali Kelas</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Tetapkan guru sebagai wali kelas untuk setiap rombongan belajar.</p>
            <div class="mt-3 flex items-center gap-2">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold border border-blue-100">
                    <i class="fa-solid fa-chalkboard-user"></i> Total Kelas: <?= count($classrooms) ?>
                </div>
                <button onclick="document.getElementById('infoModal').classList.remove('hidden')"
                    class="w-7 h-7 rounded-full bg-slate-100 text-slate-500 hover:bg-blue-100 hover:text-blue-600 flex items-center justify-center transition-colors border border-slate-200" title="Panduan Penggunaan">
                    <i class="fa-solid fa-circle-info text-sm"></i>
                </button>
            </div>
        </div>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <!-- Info Banner -->
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-4 mb-6 flex items-start gap-3 text-sm text-blue-800">
        <i class="fa-solid fa-circle-info text-blue-500 mt-0.5"></i>
        <span>Wali Kelas yang ditunjuk akan mendapatkan akses menu khusus <strong>"Kelas Saya"</strong> di sistem.</span>
    </div>

    <!-- Filter -->
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 mb-6">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <input type="hidden" name="limit" value="<?= $limit ?>">
            <div class="flex-1 min-w-[200px] relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nama kelas atau wali kelas..."
                    class="w-full pl-10 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
            </div>
            <select name="level" class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                <option value="">Semua Tingkat</option>
                <?php foreach ($levels as $lvl): ?>
                    <option value="<?= $lvl['level'] ?>" <?= $levelFilter == $lvl['level'] ? 'selected' : '' ?>>Level <?= $lvl['level'] ?></option>
                <?php endforeach; ?>
            </select>
            <button type="submit" class="bg-slate-800 text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-slate-900 transition-colors">Terapkan</button>
            <?php if (!empty($search) || !empty($levelFilter)): ?>
                <a href="/academic/homeroom-assign" class="flex items-center justify-center w-10 h-10 bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition" title="Reset">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full whitespace-nowrap text-left">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-200">
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Kelas</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Wali Kelas Saat Ini</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Ganti Wali Kelas</th>
                        <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 bg-white">
                    <?php if (empty($classrooms)): ?>
                        <tr><td colspan="4" class="px-5 py-16 text-center text-slate-400 text-sm font-medium">Belum ada data kelas.</td></tr>
                    <?php endif; ?>

                    <?php foreach ($classrooms as $c): ?>
                    <tr class="hover:bg-slate-50/80 transition-colors text-sm">
                        <td class="px-5 py-4 font-extrabold text-slate-800"><?= $c['name'] ?></td>
                        <td class="px-5 py-4">
                            <?php if ($c['teacher_name']): ?>
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-green-50 text-green-700 rounded-lg text-xs font-bold border border-green-200">
                                    <i class="fa-solid fa-user-tie"></i> <?= $c['teacher_name'] ?>
                                </span>
                            <?php else: ?>
                                <span class="text-slate-400 text-xs italic">Belum diset</span>
                            <?php endif; ?>
                        </td>

                        <form action="/academic/homeroom-assign/update" method="POST" class="contents">
                            <?= \App\Core\Csrf::input() ?>
                            <input type="hidden" name="classroom_id" value="<?= $c['id'] ?>">
                            <td class="px-5 py-3">
                                <select name="teacher_id" class="select2-teacher w-full">
                                    <option value="">-- Kosongkan / Pilih Guru --</option>
                                    <?php foreach ($teachers as $t): ?>
                                        <option value="<?= $t['id'] ?>" <?= ($c['homeroom_teacher_id'] == $t['id']) ? 'selected' : '' ?>>
                                            <?= $t['name'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-xl text-xs font-bold hover:bg-blue-700 shadow-sm transition-colors">
                                    Simpan
                                </button>
                            </td>
                        </form>
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
                    <option value="20" <?= $limit == 20 ? 'selected' : '' ?>>20 entries</option>
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
</main>

<!-- Modal Info -->
<div id="infoModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-blue-50 flex justify-between items-center">
            <h3 class="font-bold text-blue-800 flex items-center gap-2"><i class="fa-solid fa-circle-info text-blue-500"></i> Panduan Atur Wali Kelas</h3>
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-blue-100 text-blue-500 hover:bg-blue-200 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="p-6 space-y-5 text-sm text-slate-600 overflow-y-auto max-h-[70vh]">
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">1</span> Cara Penggunaan</h4>
                <ol class="list-decimal list-inside space-y-1.5 text-slate-500">
                    <li>Cari baris kelas yang ingin diatur wali kelasnya.</li>
                    <li>Pilih guru dari dropdown <strong class="text-slate-700">Ganti Wali Kelas</strong>.</li>
                    <li>Klik <strong class="text-slate-700">Simpan</strong> — wali kelas langsung diperbarui.</li>
                    <li>Untuk menghapus wali kelas, pilih opsi <strong class="text-slate-700">Kosongkan</strong> lalu simpan.</li>
                </ol>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">2</span> Efek Penunjukan Wali Kelas</h4>
                <ul class="space-y-1.5 text-slate-500">
                    <li class="flex items-center gap-2"><i class="fa-solid fa-check text-green-500 text-xs"></i> Guru mendapat akses menu <strong class="text-slate-700">"Kelas Saya"</strong> di sidebar.</li>
                    <li class="flex items-center gap-2"><i class="fa-solid fa-check text-green-500 text-xs"></i> Nama wali kelas tampil di rekap laporan kelas.</li>
                    <li class="flex items-center gap-2"><i class="fa-solid fa-check text-green-500 text-xs"></i> Wali kelas dapat melihat data siswa dan nilai di kelasnya.</li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">3</span> Relasi ke Menu Lain</h4>
                <div class="space-y-2">
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-chalkboard text-blue-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Data Kelas</div><div class="text-[11px] text-slate-400">Daftar kelas diambil dari <strong>Master → Data Kelas</strong>.</div></div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-chalkboard-user text-purple-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Manajemen Guru</div><div class="text-[11px] text-slate-400">Daftar guru diambil dari <strong>Kepegawaian → Manajemen Guru</strong>.</div></div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-file-lines text-orange-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Laporan Wali Kelas</div><div class="text-[11px] text-slate-400">Wali kelas yang ditetapkan di sini tampil di <strong>Wali Kelas → Laporan</strong>.</div></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end">
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="px-5 py-2 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700 transition">Mengerti</button>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2-teacher').select2({ placeholder: '-- Kosongkan / Pilih Guru --', allowClear: true });
    });
    function updateQS(uri, key, value) {
        var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        var sep = uri.indexOf('?') !== -1 ? "&" : "?";
        return uri.match(re) ? uri.replace(re, '$1' + key + "=" + value + '$2') : uri + sep + key + "=" + value;
    }
    window.onclick = function(e) { if (e.target == document.getElementById('infoModal')) document.getElementById('infoModal').classList.add('hidden'); }
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
