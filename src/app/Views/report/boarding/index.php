<?php require __DIR__ . '/../../layouts/header.php'; ?>
<?php require __DIR__ . '/../../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Rapor Asrama</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Input nilai kepesantrenan — Tahfidz, Bahasa, Akhlaq.</p>
            <div class="mt-3 flex items-center gap-2">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold border border-blue-100">
                    <i class="fa-solid fa-star-and-crescent"></i> Penilaian Kepesantrenan
                </div>
                <button onclick="document.getElementById('infoModal').classList.remove('hidden')"
                    class="w-7 h-7 rounded-full bg-slate-100 text-slate-500 hover:bg-blue-100 hover:text-blue-600 flex items-center justify-center transition-colors border border-slate-200" title="Panduan Penggunaan">
                    <i class="fa-solid fa-circle-info text-sm"></i>
                </button>
            </div>
        </div>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <!-- Filter Kelas -->
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 mb-6">
        <form class="flex flex-wrap items-end gap-3">
            <div class="flex-1 min-w-[220px]">
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Pilih Kelas</label>
                <select name="classroom_id" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none" onchange="this.form.submit()">
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach ($classrooms as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $selectedClass == $c['id'] ? 'selected' : '' ?>><?= $c['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </form>
    </div>

    <!-- Cards Siswa -->
    <?php if ($selectedClass && !empty($students)): ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <?php foreach ($students as $s): ?>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-5 hover:shadow-md transition-all group">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <h4 class="font-extrabold text-slate-800"><?= $s['full_name'] ?></h4>
                    <span class="text-[10px] text-slate-400 font-mono"><?= $s['nis'] ?></span>
                </div>
                <button onclick='inputScore(<?= json_encode($s) ?>)'
                    class="px-3 py-1.5 bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white rounded-lg text-xs font-bold border border-blue-200 transition-colors">
                    <i class="fa-solid fa-pen-to-square mr-1"></i> Input
                </button>
            </div>

            <div class="space-y-1.5 text-xs">
                <?php foreach (['tahfidz_grade' => 'Tahfidz', 'language_grade' => 'Bahasa', 'character_grade' => 'Akhlaq'] as $key => $label): ?>
                <div class="flex justify-between items-center py-1 border-b border-slate-100 last:border-0">
                    <span class="text-slate-500"><?= $label ?></span>
                    <span class="font-bold <?= $s[$key] ? 'text-green-600' : 'text-slate-300' ?>">
                        <?= $s[$key] ?? '—' ?>
                    </span>
                </div>
                <?php endforeach; ?>
            </div>

            <?php if ($s['tahfidz_grade']): ?>
            <div class="mt-3 pt-3 border-t border-slate-100 flex justify-end">
                <a href="/report/boarding/print?student_id=<?= $s['id'] ?>&year_id=<?= $activeYear['id'] ?>" target="_blank"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 text-slate-600 hover:bg-slate-800 hover:text-white rounded-lg text-xs font-bold border border-slate-200 transition-colors">
                    <i class="fa-solid fa-print"></i> Cetak
                </a>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>

    <?php elseif ($selectedClass): ?>
    <div class="text-center py-16 bg-white rounded-2xl border border-dashed border-slate-300">
        <i class="fa-solid fa-users-slash text-3xl text-slate-300 mb-3"></i>
        <p class="text-slate-400 font-medium">Tidak ada siswa di kelas ini.</p>
    </div>
    <?php else: ?>
    <div class="text-center py-16 bg-white rounded-2xl border border-dashed border-slate-300">
        <i class="fa-solid fa-hand-pointer text-3xl text-slate-300 mb-3"></i>
        <p class="text-slate-400 font-medium">Pilih kelas terlebih dahulu untuk menampilkan data siswa.</p>
    </div>
    <?php endif; ?>
</main>

<!-- Modal Info -->
<div id="infoModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-blue-50 flex justify-between items-center">
            <h3 class="font-bold text-blue-800 flex items-center gap-2"><i class="fa-solid fa-circle-info text-blue-500"></i> Panduan Rapor Asrama</h3>
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-blue-100 text-blue-500 hover:bg-blue-200 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="p-6 space-y-5 text-sm text-slate-600">
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">1</span> Cara Penggunaan</h4>
                <ol class="list-decimal list-inside space-y-1.5 text-slate-500">
                    <li>Pilih <strong class="text-slate-700">Kelas</strong> untuk menampilkan daftar siswa.</li>
                    <li>Klik <strong class="text-slate-700">Input</strong> pada kartu siswa untuk mengisi nilai.</li>
                    <li>Isi nilai Tahfidz, Bahasa, Akhlaq beserta deskripsinya.</li>
                    <li>Klik <strong class="text-slate-700">Cetak</strong> untuk mencetak rapor asrama siswa.</li>
                </ol>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">2</span> Skala Nilai</h4>
                <div class="flex flex-wrap gap-2 text-xs font-bold">
                    <span class="px-2.5 py-1 bg-green-50 text-green-700 rounded-lg border border-green-200">A — Mumtaz</span>
                    <span class="px-2.5 py-1 bg-blue-50 text-blue-700 rounded-lg border border-blue-200">B — Jayyid Jiddan</span>
                    <span class="px-2.5 py-1 bg-yellow-50 text-yellow-700 rounded-lg border border-yellow-200">C — Jayyid</span>
                    <span class="px-2.5 py-1 bg-slate-100 text-slate-600 rounded-lg border border-slate-200">D — Maqbul</span>
                </div>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">3</span> Relasi ke Menu Lain</h4>
                <div class="space-y-2">
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-chalkboard text-blue-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Data Kelas</div><div class="text-[11px] text-slate-400">Daftar kelas diambil dari <strong>Master → Data Kelas</strong>.</div></div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-file-invoice text-orange-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Rapor Akademik</div><div class="text-[11px] text-slate-400">Nilai asrama ini melengkapi rapor akademik di menu <strong>Laporan → Rapor Siswa</strong>.</div></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end">
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="px-5 py-2 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700 transition">Mengerti</button>
        </div>
    </div>
</div>

<!-- Modal Input Nilai -->
<div id="scoreModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden max-h-[90vh] flex flex-col">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-700 flex items-center gap-2">
                <i class="fa-solid fa-star-and-crescent text-slate-400"></i>
                Input Rapor: <span id="modalStudentName" class="text-blue-600 ml-1"></span>
            </h3>
            <button onclick="document.getElementById('scoreModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-slate-200 text-slate-500 hover:bg-slate-300 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="/report/boarding/store" method="POST" class="p-6 overflow-y-auto space-y-4">
            <?= \App\Core\Csrf::input() ?>
            <input type="hidden" name="student_id" id="inpStudentId">
            <input type="hidden" name="academic_year_id" value="<?= $activeYear['id'] ?? '' ?>">
            <input type="hidden" name="classroom_id" value="<?= $selectedClass ?>">

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <?php foreach ([
                    ['key' => 'tahfidz',   'label' => 'TAHFIDZ',          'color' => 'green',  'placeholder' => 'cth: Hafalan lancar, perlu perbaikan makhroj...'],
                    ['key' => 'language',  'label' => 'BAHASA',            'color' => 'blue',   'placeholder' => 'cth: Aktif berbahasa Arab di asrama...'],
                    ['key' => 'character', 'label' => 'AKHLAQ / DISIPLIN', 'color' => 'purple', 'placeholder' => 'cth: Sopan, disiplin sholat berjamaah...'],
                ] as $comp): ?>
                <div class="bg-slate-50 rounded-2xl border border-slate-200 p-4">
                    <div class="text-xs font-bold text-slate-600 uppercase tracking-wider mb-3 text-center"><?= $comp['label'] ?></div>
                    <select name="<?= $comp['key'] ?>_grade" class="w-full px-3 py-2.5 bg-white border border-slate-200 rounded-xl text-sm font-bold text-center outline-none mb-3">
                        <option value="">— Nilai —</option>
                        <option value="A">A (Mumtaz)</option>
                        <option value="B">B (Jayyid Jiddan)</option>
                        <option value="C">C (Jayyid)</option>
                        <option value="D">D (Maqbul)</option>
                    </select>
                    <textarea name="<?= $comp['key'] ?>_desc" rows="3" placeholder="<?= $comp['placeholder'] ?>"
                        class="w-full px-3 py-2 bg-white border border-slate-200 rounded-xl text-xs focus:ring-2 focus:ring-blue-500/50 outline-none transition-all resize-none"></textarea>
                </div>
                <?php endforeach; ?>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Catatan Wali Asrama (Musyrif)</label>
                <textarea name="homeroom_note" rows="2" placeholder="cth: Santri menunjukkan perkembangan yang baik..."
                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all resize-none"></textarea>
            </div>

            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('scoreModal').classList.add('hidden')" class="flex-1 bg-slate-100 text-slate-600 py-2.5 rounded-xl font-bold hover:bg-slate-200 transition text-sm">Batal</button>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2.5 rounded-xl font-bold hover:bg-blue-700 shadow-md shadow-blue-500/20 transition text-sm">Simpan Rapor</button>
            </div>
        </form>
    </div>
</div>

<script>
    function inputScore(student) {
        document.getElementById('scoreModal').classList.remove('hidden');
        document.getElementById('modalStudentName').innerText = student.full_name;
        document.getElementById('inpStudentId').value = student.id;
    }
    window.onclick = function(e) {
        ['scoreModal','infoModal'].forEach(function(id) {
            if (e.target == document.getElementById(id)) document.getElementById(id).classList.add('hidden');
        });
    }
</script>

<?php require __DIR__ . '/../../layouts/footer.php'; ?>
