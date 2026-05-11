<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    .select2-container--default .select2-selection--single { border-color: #e2e8f0; height: 42px; padding-top: 6px; border-radius: 0.75rem; background-color: #f8fafc; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 40px; }
    .select2-container { width: 100% !important; }
    .select2-dropdown { border-color: #e2e8f0; border-radius: 0.75rem; overflow: hidden; }
    .select2-search--dropdown .select2-search__field { border-radius: 0.5rem; border-color: #e2e8f0; padding: 6px 10px; }
</style>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Manajemen Asrama</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Kelola gedung, kamar, dan penempatan santri.</p>
            <div class="mt-3 flex items-center gap-2">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold border border-blue-100">
                    <i class="fa-solid fa-building"></i> Total Gedung/Kamar: <?= $totalDorms ?>
                </div>
                <button onclick="document.getElementById('infoModal').classList.remove('hidden')"
                    class="w-7 h-7 rounded-full bg-slate-100 text-slate-500 hover:bg-blue-100 hover:text-blue-600 flex items-center justify-center transition-colors border border-slate-200" title="Panduan Penggunaan">
                    <i class="fa-solid fa-circle-info text-sm"></i>
                </button>
            </div>
        </div>
        <button onclick="document.getElementById('modalAddDorm').classList.remove('hidden')"
            class="px-4 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold shadow-md shadow-blue-500/20 hover:bg-blue-700 transition-all flex items-center gap-2 w-fit">
            <i class="fa-solid fa-plus"></i> Tambah Gedung
        </button>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Form Tempatkan Santri -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 h-fit">
            <h4 class="font-bold text-purple-700 flex items-center gap-2 border-b border-slate-100 pb-4 mb-5">
                <i class="fa-solid fa-users-between-lines text-purple-400"></i> Tempatkan Santri
            </h4>
            <form action="/asrama/assign" method="POST" class="space-y-4">
                <?= \App\Core\Csrf::input() ?>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Pilih Santri (Non-Asrama)</label>
                    <select name="student_id" class="select2-student w-full" required>
                        <option value="">-- Cari & Pilih Santri --</option>
                        <?php foreach ($students as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= htmlspecialchars($s['full_name']) ?> (<?= $s['nis'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                    <p class="text-[10px] text-slate-400 mt-1.5">*Hanya santri yang belum mendapat kamar.</p>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Pilih Kamar Tujuan</label>
                    <select name="dorm_id" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none" required>
                        <option value="">-- Pilih Kamar --</option>
                        <?php foreach ($dorms as $d): ?>
                            <option value="<?= $d['id'] ?>"><?= htmlspecialchars($d['name']) ?> [<?= $d['unit'] ?>] (Sisa: <?= $d['capacity'] - $d['occupied'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="w-full bg-purple-600 text-white py-2.5 rounded-xl font-bold hover:bg-purple-700 shadow-md shadow-purple-500/20 transition-all text-sm">
                    <i class="fa-solid fa-check mr-2"></i> Simpan Penempatan
                </button>
            </form>
        </div>

        <!-- Daftar Kamar -->
        <div class="lg:col-span-2 flex flex-col gap-6">

            <!-- Filter -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                <form method="GET" class="flex flex-wrap items-center gap-3">
                    <div class="flex-1 min-w-[200px] relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari nama gedung / kamar..."
                            class="w-full pl-10 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
                    </div>
                    <select name="gender" class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                        <option value="">Semua</option>
                        <option value="L" <?= $genderFilter == 'L' ? 'selected' : '' ?>>Putra (Ikhwan)</option>
                        <option value="P" <?= $genderFilter == 'P' ? 'selected' : '' ?>>Putri (Akhwat)</option>
                    </select>
                    <?php if ($scope === 'GLOBAL'): ?>
                    <select name="unit" class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                        <option value="">Semua Unit</option>
                        <option value="MTS" <?= ($unitFilter??'')==='MTS' ? 'selected' : '' ?>>MTS</option>
                        <option value="MA"  <?= ($unitFilter??'')==='MA'  ? 'selected' : '' ?>>MA</option>
                        <option value="PDF" <?= ($unitFilter??'')==='PDF' ? 'selected' : '' ?>>PDF</option>
                    </select>
                    <?php else: ?>
                    <span class="px-3 py-2.5 bg-blue-50 border border-blue-200 rounded-xl text-sm font-bold text-blue-700">
                        <i class="fa-solid fa-filter mr-1"></i> Unit: <?= $scope ?>
                    </span>
                    <?php endif; ?>
                    <button type="submit" class="bg-slate-800 text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-slate-900 transition-colors">Terapkan</button>
                    <?php if (!empty($search) || !empty($genderFilter) || !empty($unitFilter??'')): ?>
                        <a href="/asrama/dorms" class="flex items-center justify-center w-10 h-10 bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition" title="Reset">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Cards Grid -->
            <?php if (empty($dorms)): ?>
                <div class="text-center py-16 bg-white rounded-2xl border border-dashed border-slate-300">
                    <i class="fa-solid fa-building text-3xl text-slate-300 mb-3"></i>
                    <p class="text-slate-400 font-medium">Belum ada gedung asrama. Klik "Tambah Gedung".</p>
                </div>
            <?php else: ?>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <?php foreach ($dorms as $d):
                    $percent = ($d['capacity'] > 0) ? ($d['occupied'] / $d['capacity']) * 100 : 0;
                    $barColor = $percent >= 100 ? 'bg-red-500' : ($percent > 80 ? 'bg-yellow-500' : 'bg-green-500');
                    $borderColor = $d['gender'] == 'L' ? 'border-blue-400' : 'border-pink-400';
                ?>
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 border-l-4 <?= $borderColor ?> p-5 relative group">
                    <!-- Tombol Hapus -->
                    <div class="absolute top-3 right-3 opacity-0 group-hover:opacity-100 transition">
                        <form action="/asrama/dorms/delete" method="POST" onsubmit="return confirm('Hapus kamar <?= htmlspecialchars($d['name']) ?>?')">
                            <?= \App\Core\Csrf::input() ?>
                            <input type="hidden" name="id" value="<?= $d['id'] ?>">
                            <button class="w-7 h-7 rounded-lg bg-red-50 text-red-400 hover:bg-red-500 hover:text-white flex items-center justify-center transition-colors">
                                <i class="fa-solid fa-trash-can text-xs"></i>
                            </button>
                        </form>
                    </div>

                    <div class="flex justify-between items-start mb-3 pr-8">
                        <h4 class="font-extrabold text-slate-800"><?= $d['name'] ?></h4>
                        <div class="flex flex-col items-end gap-1">
                            <span class="text-[10px] font-bold px-2.5 py-1 rounded-lg border <?= $d['gender']=='L' ? 'bg-blue-50 text-blue-700 border-blue-200' : 'bg-pink-50 text-pink-700 border-pink-200' ?>">
                                <?= $d['gender'] == 'L' ? 'IKHWAN' : 'AKHWAT' ?>
                            </span>
                            <?php if (!empty($d['unit'])): ?>
                            <span class="text-[10px] font-bold px-2.5 py-1 rounded-lg border <?= $d['unit']==='MTS' ? 'bg-green-50 text-green-700 border-green-200' : ($d['unit']==='MA' ? 'bg-purple-50 text-purple-700 border-purple-200' : 'bg-amber-50 text-amber-700 border-amber-200') ?>">
                                <?= $d['unit'] ?>
                            </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="w-full bg-slate-100 rounded-full h-2.5 mb-3">
                        <div class="<?= $barColor ?> h-2.5 rounded-full transition-all duration-500" style="width: <?= min($percent, 100) ?>%"></div>
                    </div>

                    <div class="flex justify-between text-xs text-slate-600 bg-slate-50 px-3 py-2 rounded-xl border border-slate-100 mb-3">
                        <span>Terisi: <strong><?= $d['occupied'] ?></strong></span>
                        <span class="text-slate-400">|</span>
                        <span>Kapasitas: <strong><?= $d['capacity'] ?></strong></span>
                        <span class="text-slate-400">|</span>
                        <span class="<?= ($d['capacity'] - $d['occupied']) <= 0 ? 'text-red-600 font-bold' : 'text-green-600 font-bold' ?>">
                            Sisa: <?= max(0, $d['capacity'] - $d['occupied']) ?>
                        </span>
                    </div>

                    <a href="/asrama/dorms/students?id=<?= $d['id'] ?>"
                        class="block w-full text-center text-xs font-bold text-blue-600 bg-blue-50 hover:bg-blue-600 hover:text-white py-2 rounded-xl border border-blue-200 transition-colors">
                        <i class="fa-solid fa-users mr-1"></i> Lihat Daftar Santri
                    </a>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<!-- Modal Tambah Gedung -->
<div id="modalAddDorm" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-700 flex items-center gap-2"><i class="fa-solid fa-building text-slate-400"></i> Tambah Gedung / Kamar</h3>
            <button onclick="document.getElementById('modalAddDorm').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-slate-200 text-slate-500 hover:bg-slate-300 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="/asrama/dorms/store" method="POST" class="p-6 space-y-4">
            <?= \App\Core\Csrf::input() ?>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Nama Gedung / Kamar</label>
                <input type="text" name="name" placeholder="cth: Gedung A – Lantai 1, Kamar 101"
                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Kapasitas (Orang)</label>
                <input type="number" name="capacity" placeholder="cth: 10"
                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" required>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Peruntukan</label>
                <select name="gender" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                    <option value="L">Putra (Ikhwan)</option>
                    <option value="P">Putri (Akhwat)</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Unit</label>
                <?php if ($scope !== 'GLOBAL'): ?>
                    <input type="hidden" name="unit" value="<?= $scope ?>">
                    <div class="w-full px-3 py-2.5 bg-slate-100 border border-slate-200 rounded-xl text-sm text-slate-600 font-bold">
                        <?= $scope ?> <span class="text-xs font-normal text-slate-400">(mengikuti scope aktif)</span>
                    </div>
                <?php else: ?>
                <select name="unit" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                    <option value="MTS">MTS (Madrasah Tsanawiyah)</option>
                    <option value="MA">MA (Madrasah Aliyah)</option>
                    <option value="PDF">PDF (Diniyah Formal)</option>
                </select>
                <?php endif; ?>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modalAddDorm').classList.add('hidden')" class="flex-1 bg-slate-100 text-slate-600 py-2.5 rounded-xl font-bold hover:bg-slate-200 transition text-sm">Batal</button>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2.5 rounded-xl font-bold hover:bg-blue-700 shadow-md shadow-blue-500/20 transition text-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Info -->
<div id="infoModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-blue-50 flex justify-between items-center">
            <h3 class="font-bold text-blue-800 flex items-center gap-2"><i class="fa-solid fa-circle-info text-blue-500"></i> Panduan Manajemen Asrama</h3>
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-blue-100 text-blue-500 hover:bg-blue-200 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="p-6 space-y-5 text-sm text-slate-600 overflow-y-auto max-h-[70vh]">
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">1</span> Cara Penggunaan</h4>
                <ol class="list-decimal list-inside space-y-1.5 text-slate-500">
                    <li>Klik <strong class="text-slate-700">Tambah Gedung</strong> untuk mendaftarkan gedung/kamar baru.</li>
                    <li>Gunakan form <strong class="text-slate-700">Tempatkan Santri</strong> untuk assign santri ke kamar.</li>
                    <li>Klik <strong class="text-slate-700">Lihat Daftar Santri</strong> pada kartu kamar untuk melihat penghuninya.</li>
                    <li>Progress bar menunjukkan tingkat hunian — merah jika penuh.</li>
                </ol>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">2</span> Relasi ke Menu Lain</h4>
                <div class="space-y-2">
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-users text-blue-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Data Siswa</div><div class="text-[11px] text-slate-400">Santri yang ditempatkan diambil dari <strong>Kesiswaan → Data Siswa</strong>.</div></div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-person-walking-arrow-right text-orange-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Mutasi Asrama</div><div class="text-[11px] text-slate-400">Perpindahan kamar santri dicatat di menu <strong>Asrama → Mutasi</strong>.</div></div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-user-shield text-purple-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Pembina Asrama</div><div class="text-[11px] text-slate-400">Penugasan pembina per gedung dikelola di menu <strong>Asrama → Pembina</strong>.</div></div>
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
        $('.select2-student').select2({
            placeholder: '-- Cari & Pilih Santri --',
            allowClear: true,
            width: '100%',
            dropdownParent: $('.select2-student').parent()
        });
    });
    // Cegah layout shift: kunci lebar body saat dropdown terbuka
    $(document).on('select2:open', function() {
        var w = document.body.offsetWidth;
        document.body.style.overflowY = 'scroll';
    });
    $(document).on('select2:close', function() {
        document.body.style.overflowY = '';
    });
    window.onclick = function(e) {
        ['modalAddDorm','infoModal'].forEach(function(id) {
            if (e.target == document.getElementById(id)) document.getElementById(id).classList.add('hidden');
        });
    }
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
