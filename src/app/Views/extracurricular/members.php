<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Anggota Ekstrakurikuler</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Kelola daftar anggota santri per kegiatan ekstrakurikuler.</p>
            <div class="mt-3 flex items-center gap-2">
                <?php if ($selectedEkskul): ?>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold border border-blue-100">
                    <i class="fa-solid fa-users"></i> Total Anggota: <?= count($members) ?>
                </div>
                <?php endif; ?>
                <?php if (($scope ?? 'GLOBAL') !== 'GLOBAL'): ?>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-amber-50 text-amber-700 rounded-lg text-xs font-bold border border-amber-200">
                    <i class="fa-solid fa-filter"></i> Unit: <?= $scope ?>
                </div>
                <?php endif; ?>
                <button onclick="document.getElementById('infoModal').classList.remove('hidden')"
                    class="w-7 h-7 rounded-full bg-slate-100 text-slate-500 hover:bg-blue-100 hover:text-blue-600 flex items-center justify-center transition-colors border border-slate-200" title="Panduan Penggunaan">
                    <i class="fa-solid fa-circle-info text-sm"></i>
                </button>
            </div>
        </div>
        <?php if ($selectedEkskul): ?>
        <button onclick="document.getElementById('addModal').classList.remove('hidden')"
            class="px-4 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold shadow-md shadow-blue-500/20 hover:bg-blue-700 transition-all flex items-center gap-2 w-fit">
            <i class="fa-solid fa-plus"></i> Tambah Anggota
        </button>
        <?php endif; ?>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <div class="flex flex-col gap-6">

        <!-- Filter -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
            <form method="GET" action="/extracurricular/members" class="flex flex-wrap items-center gap-3">
                <div class="flex-1 min-w-[200px]">
                    <select name="id" onchange="this.form.submit()"
                        class="w-full py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                        <option value="">-- Pilih Ekstrakurikuler --</option>
                        <?php foreach ($ekskuls as $e): ?>
                            <option value="<?= $e['id'] ?>" <?= $selectedEkskul == $e['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($e['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </form>
        </div>

        <?php if (!$selectedEkskul): ?>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm py-16 text-center text-slate-400 text-sm font-medium">
            <i class="fa-solid fa-hand-pointer text-3xl mb-3 block text-slate-300"></i>
            Pilih ekstrakurikuler untuk melihat daftar anggota.
        </div>
        <?php else: ?>

        <!-- Table Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
            <div class="overflow-x-auto">
                <table class="min-w-full whitespace-nowrap text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Santri</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">NIS</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Kelas</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <?php if (empty($members)): ?>
                            <tr><td colspan="4" class="px-5 py-16 text-center text-slate-400 text-sm font-medium">Belum ada anggota terdaftar.</td></tr>
                        <?php endif; ?>
                        <?php foreach ($members as $m): ?>
                        <tr class="hover:bg-slate-50/80 transition-colors text-sm">
                            <td class="px-5 py-4 font-extrabold text-slate-800"><?= htmlspecialchars($m['full_name']) ?></td>
                            <td class="px-5 py-4 text-slate-500 font-mono text-xs"><?= $m['nis'] ?></td>
                            <td class="px-5 py-4 text-slate-600"><?= $m['class_name'] ?? '-' ?></td>
                            <td class="px-5 py-4 text-center">
                                <form action="/extracurricular/members/delete" method="POST"
                                    onsubmit="return confirm('Hapus santri ini dari ekskul?')" class="inline">
                                    <input type="hidden" name="record_id" value="<?= $m['record_id'] ?>">
                                    <input type="hidden" name="extracurricular_id" value="<?= $selectedEkskul ?>">
                                    <button class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white inline-flex items-center justify-center transition-colors shadow-sm" title="Hapus">
                                        <i class="fa-solid fa-trash-can text-sm"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php endif; ?>
    </div>
</main>

<!-- Modal Info -->
<div id="infoModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-blue-50 flex justify-between items-center">
            <h3 class="font-bold text-blue-800 flex items-center gap-2"><i class="fa-solid fa-circle-info text-blue-500"></i> Panduan Anggota Ekskul</h3>
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-blue-100 text-blue-500 hover:bg-blue-200 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="p-6 space-y-5 text-sm text-slate-600">
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">1</span> Cara Penggunaan</h4>
                <ol class="list-decimal list-inside space-y-1.5 text-slate-500">
                    <li>Pilih <strong class="text-slate-700">Ekstrakurikuler</strong> dari dropdown untuk melihat anggotanya.</li>
                    <li>Klik <strong class="text-slate-700">Tambah Anggota</strong> untuk mendaftarkan santri ke ekskul tersebut.</li>
                    <li>Klik ikon <strong class="text-slate-700">hapus</strong> pada baris santri untuk mengeluarkan dari ekskul.</li>
                </ol>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">2</span> Relasi ke Menu Lain</h4>
                <div class="space-y-2">
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-star text-blue-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Master Ekskul</div><div class="text-[11px] text-slate-400">Daftar ekskul dikelola di menu <strong>Ekstrakurikuler → Data & Jadwal</strong>.</div></div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-calendar-check text-green-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Absensi Ekskul</div><div class="text-[11px] text-slate-400">Hanya anggota terdaftar yang muncul di menu <strong>Ekstrakurikuler → Absensi</strong>.</div></div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-graduation-cap text-purple-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Data Santri</div><div class="text-[11px] text-slate-400">Daftar santri diambil dari <strong>Kesiswaan → Data Santri</strong> (status aktif).</div></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end">
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="px-5 py-2 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700 transition">Mengerti</button>
        </div>
    </div>
</div>

<!-- Modal Tambah Anggota -->
<?php if ($selectedEkskul): ?>
<div id="addModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-700 flex items-center gap-2"><i class="fa-solid fa-user-plus text-slate-400"></i> Tambah Anggota</h3>
            <button onclick="document.getElementById('addModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-slate-200 text-slate-500 hover:bg-slate-300 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="/extracurricular/members/add" method="POST" class="p-6 space-y-4">
            <input type="hidden" name="extracurricular_id" value="<?= $selectedEkskul ?>">
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Pilih Santri</label>
                <?php if (empty($students)): ?>
                    <p class="text-sm text-slate-400 italic">Semua santri aktif sudah terdaftar di ekskul ini.</p>
                <?php else: ?>
                <select name="student_id" id="studentSelect" class="w-full" required>
                    <option value="">-- Cari santri... --</option>
                    <?php foreach ($students as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= $s['nis'] ?> — <?= htmlspecialchars($s['full_name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php endif; ?>
            </div>
            <?php if (!empty($students)): ?>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="flex-1 bg-slate-100 text-slate-600 py-2.5 rounded-xl font-bold hover:bg-slate-200 transition text-sm">Batal</button>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2.5 rounded-xl font-bold hover:bg-blue-700 shadow-md shadow-blue-500/20 transition text-sm">Tambahkan</button>
            </div>
            <?php else: ?>
            <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="w-full bg-slate-100 text-slate-600 py-2.5 rounded-xl font-bold hover:bg-slate-200 transition text-sm">Tutup</button>
            <?php endif; ?>
        </form>
    </div>
</div>
<?php endif; ?>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet"/>
<style>
    .select2-container--default .select2-selection--single { border-color: #e2e8f0; height: 42px; padding-top: 6px; border-radius: 0.75rem; background-color: #f8fafc; }
    .select2-container--default .select2-selection--single .select2-selection__arrow { height: 40px; }
    .select2-container--default.select2-container--focus .select2-selection--single { border-color: #93c5fd; outline: none; }
    .select2-dropdown { border-color: #e2e8f0; border-radius: 0.75rem; overflow: hidden; }
    .select2-search--dropdown .select2-search__field { border-radius: 0.5rem; border-color: #e2e8f0; padding: 6px 10px; }
    .select2-container { width: 100% !important; }
</style>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    <?php if ($selectedEkskul && !empty($students)): ?>
    $('#studentSelect').select2({
        placeholder: '-- Cari santri...',
        allowClear: true,
        dropdownParent: $('#addModal')
    });
    <?php endif; ?>
    window.onclick = function(e) {
        ['infoModal','addModal'].forEach(function(id) {
            var el = document.getElementById(id);
            if (el && e.target == el) el.classList.add('hidden');
        });
    }
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
