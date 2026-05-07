<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Input Absensi Harian</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Pilih kelas dan tanggal untuk memulai absensi.</p>
            <div class="mt-3">
                <button onclick="document.getElementById('infoModal').classList.remove('hidden')"
                    class="w-7 h-7 rounded-full bg-slate-100 text-slate-500 hover:bg-blue-100 hover:text-blue-600 flex items-center justify-center transition-colors border border-slate-200" title="Panduan Penggunaan">
                    <i class="fa-solid fa-circle-info text-sm"></i>
                </button>
            </div>
        </div>
        <a href="/attendance/students" class="px-4 py-2.5 bg-white text-slate-700 border border-slate-300 rounded-xl text-sm font-semibold hover:bg-slate-50 transition-all flex items-center gap-2 w-fit">
            <i class="fa-solid fa-arrow-left text-slate-400"></i> Kembali ke Riwayat
        </a>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <!-- Selector Kelas & Tanggal -->
    <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 mb-6">
        <h4 class="font-bold text-slate-700 flex items-center gap-2 border-b border-slate-100 pb-4 mb-5">
            <i class="fa-solid fa-filter text-slate-400"></i> Pilih Kelas & Tanggal
        </h4>
        <form method="GET" action="/attendance/students/create" class="flex flex-col md:flex-row gap-4 items-end">
            <div class="flex-1">
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Pilih Kelas</label>
                <select name="class_id" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" required>
                    <option value="">-- Pilih Kelas --</option>
                    <?php foreach ($classrooms as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $selectedClass == $c['id'] ? 'selected' : '' ?>><?= $c['name'] ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="flex-1">
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Pilih Tanggal</label>
                <input type="date" name="date" value="<?= $selectedDate ?>"
                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" required>
            </div>
            <div>
                <button type="submit" class="px-6 py-2.5 bg-blue-600 text-white rounded-xl font-bold hover:bg-blue-700 shadow-md shadow-blue-500/20 transition-all text-sm flex items-center gap-2 whitespace-nowrap">
                    <i class="fa-solid fa-users-viewfinder"></i> Tampilkan Siswa
                </button>
            </div>
        </form>
    </div>

    <?php if ($selectedClass && !empty($students)): ?>
    <form action="/attendance/students/store" method="POST">
        <?= \App\Core\Csrf::input() ?>
        <input type="hidden" name="classroom_id" value="<?= $selectedClass ?>">
        <input type="hidden" name="date" value="<?= $selectedDate ?>">

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
            <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                <h4 class="font-bold text-slate-700 flex items-center gap-2">
                    <i class="fa-solid fa-list-check text-slate-400"></i> Daftar Siswa
                </h4>
                <span class="text-xs font-bold bg-blue-50 text-blue-700 px-3 py-1.5 rounded-lg border border-blue-100">
                    <?= date('l, d F Y', strtotime($selectedDate)) ?>
                </span>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full whitespace-nowrap text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Nama Siswa</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center w-64">Status Kehadiran</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <?php foreach ($students as $s):
                            $status = $existing[$s['id']]['status'] ?? 'H';
                            $note   = $existing[$s['id']]['notes'] ?? '';
                        ?>
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="px-5 py-3">
                                <div class="font-extrabold text-slate-800 text-sm"><?= $s['full_name'] ?></div>
                                <div class="text-[10px] text-slate-400 mt-0.5"><?= $s['nis'] ?></div>
                            </td>
                            <td class="px-5 py-3 text-center">
                                <div class="inline-flex bg-slate-100 rounded-xl p-1 gap-1">
                                    <?php foreach (['H' => ['Hadir','green'], 'S' => ['Sakit','yellow'], 'I' => ['Izin','blue'], 'A' => ['Alpa','red']] as $val => [$label, $color]): ?>
                                    <label class="cursor-pointer">
                                        <input type="radio" name="attendance[<?= $s['id'] ?>]" value="<?= $val ?>" class="peer sr-only" <?= $status == $val ? 'checked' : '' ?>>
                                        <div class="px-3 py-1.5 rounded-lg text-xs font-bold text-slate-400 peer-checked:bg-<?= $color ?>-500 peer-checked:text-white transition-all shadow-sm"><?= $val ?></div>
                                    </label>
                                    <?php endforeach; ?>
                                </div>
                            </td>
                            <td class="px-5 py-3">
                                <input type="text" name="notes[<?= $s['id'] ?>]" value="<?= htmlspecialchars($note) ?>"
                                    placeholder="cth: Sakit demam, izin acara keluarga..."
                                    class="w-full px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="p-4 bg-slate-50 border-t border-slate-200 flex justify-end">
                <button type="submit" class="px-8 py-2.5 bg-green-600 text-white rounded-xl font-bold hover:bg-green-700 shadow-md shadow-green-500/20 transition-all text-sm flex items-center gap-2">
                    <i class="fa-solid fa-save"></i> Simpan Data Absensi
                </button>
            </div>
        </div>
    </form>

    <?php elseif ($selectedClass): ?>
    <div class="text-center py-16 bg-white rounded-2xl border border-dashed border-slate-300">
        <i class="fa-solid fa-users-slash text-3xl text-slate-300 mb-3"></i>
        <p class="text-slate-400 font-medium">Tidak ada siswa aktif di kelas ini.</p>
    </div>
    <?php endif; ?>

</main>

<!-- Modal Info -->
<div id="infoModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-blue-50 flex justify-between items-center">
            <h3 class="font-bold text-blue-800 flex items-center gap-2"><i class="fa-solid fa-circle-info text-blue-500"></i> Panduan Input Absensi</h3>
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-blue-100 text-blue-500 hover:bg-blue-200 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="p-6 space-y-5 text-sm text-slate-600">
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">1</span> Cara Penggunaan</h4>
                <ol class="list-decimal list-inside space-y-1.5 text-slate-500">
                    <li>Pilih <strong class="text-slate-700">Kelas</strong> dan <strong class="text-slate-700">Tanggal</strong>, lalu klik <strong class="text-slate-700">Tampilkan Siswa</strong>.</li>
                    <li>Pilih status kehadiran: <strong class="text-slate-700">H</strong> (Hadir), <strong class="text-slate-700">S</strong> (Sakit), <strong class="text-slate-700">I</strong> (Izin), <strong class="text-slate-700">A</strong> (Alpa).</li>
                    <li>Isi kolom <strong class="text-slate-700">Keterangan</strong> jika diperlukan, lalu klik <strong class="text-slate-700">Simpan</strong>.</li>
                </ol>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">2</span> Relasi ke Menu Lain</h4>
                <div class="space-y-2">
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-clock-rotate-left text-blue-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Riwayat Absensi</div><div class="text-[11px] text-slate-400">Lihat rekap absensi di <strong>Kesiswaan → Absensi Santri</strong>.</div></div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-chalkboard text-purple-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Data Kelas</div><div class="text-[11px] text-slate-400">Daftar kelas dikelola di <strong>Master Data → Data Kelas</strong>.</div></div>
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

<?php require __DIR__ . '/../layouts/footer.php'; ?>
