<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Absensi Ekstrakurikuler</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Pencatatan kehadiran anggota per kegiatan ekstrakurikuler.</p>
            <div class="mt-3 flex items-center gap-2">
                <?php if ($selectedEkskul && !empty($members)): ?>
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
        <div class="text-sm text-slate-500 font-medium">
            Tanggal: <strong class="text-slate-700"><?= date('d F Y', strtotime($date)) ?></strong>
        </div>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <div class="flex flex-col gap-6">

        <!-- Filter -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
            <form method="GET" action="/extracurricular/attendance" class="flex flex-wrap items-center gap-3">
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
                <input type="date" name="date" value="<?= $date ?>" onchange="this.form.submit()"
                    class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
            </form>
        </div>

        <?php if (!$selectedEkskul): ?>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm py-16 text-center text-slate-400 text-sm font-medium">
            <i class="fa-solid fa-hand-pointer text-3xl mb-3 block text-slate-300"></i>
            Pilih ekstrakurikuler dan tanggal untuk mencatat absensi.
        </div>
        <?php elseif (empty($members)): ?>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm py-16 text-center text-slate-400 text-sm font-medium">
            <i class="fa-solid fa-user-slash text-3xl mb-3 block text-slate-300"></i>
            Belum ada anggota terdaftar di ekstrakurikuler ini.
        </div>
        <?php else: ?>

        <!-- Table Card -->
        <form action="/extracurricular/attendance/save" method="POST">
            <input type="hidden" name="extracurricular_id" value="<?= $selectedEkskul ?>">
            <input type="hidden" name="date" value="<?= $date ?>">

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
                <div class="overflow-x-auto">
                    <table class="min-w-full whitespace-nowrap text-left">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">No</th>
                                <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Santri</th>
                                <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Kelas</th>
                                <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Status Kehadiran</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            <?php $no = 1; foreach ($members as $m):
                                $status = $existingAttendance[$m['student_id']] ?? 'HADIR';
                            ?>
                            <tr class="hover:bg-slate-50/80 transition-colors text-sm">
                                <td class="px-5 py-4 text-slate-500 font-semibold"><?= $no++ ?></td>
                                <td class="px-5 py-4">
                                    <div class="font-extrabold text-slate-800"><?= htmlspecialchars($m['full_name']) ?></div>
                                    <div class="text-[10px] text-slate-400 font-mono mt-0.5">NIS: <?= $m['nis'] ?></div>
                                </td>
                                <td class="px-5 py-4 text-slate-600"><?= $m['class_name'] ?? '-' ?></td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-center gap-2 flex-wrap">
                                        <?php foreach (['HADIR' => 'green', 'IZIN' => 'blue', 'SAKIT' => 'yellow', 'ALPA' => 'red'] as $val => $color): ?>
                                        <label class="cursor-pointer flex items-center gap-1 px-2.5 py-1 rounded-lg border text-xs font-semibold
                                            <?= $status === $val
                                                ? "bg-$color-100 border-$color-400 text-$color-700"
                                                : "bg-slate-50 border-slate-200 text-slate-500 hover:bg-$color-50" ?>">
                                            <input type="radio" name="status[<?= $m['student_id'] ?>]" value="<?= $val ?>"
                                                <?= $status === $val ? 'checked' : '' ?> class="hidden">
                                            <?= ucfirst(strtolower($val)) ?>
                                        </label>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Footer -->
                <div class="p-4 bg-slate-50 border-t border-slate-200 flex justify-end">
                    <button type="submit" class="bg-blue-600 text-white px-5 py-2.5 rounded-xl text-sm font-bold shadow-md shadow-blue-500/20 hover:bg-blue-700 transition-all flex items-center gap-2">
                        <i class="fa-solid fa-floppy-disk"></i> Simpan Absensi
                    </button>
                </div>
            </div>
        </form>

        <?php endif; ?>
    </div>
</main>

<!-- Modal Info -->
<div id="infoModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-blue-50 flex justify-between items-center">
            <h3 class="font-bold text-blue-800 flex items-center gap-2"><i class="fa-solid fa-circle-info text-blue-500"></i> Panduan Absensi Ekskul</h3>
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-blue-100 text-blue-500 hover:bg-blue-200 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="p-6 space-y-5 text-sm text-slate-600">
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">1</span> Cara Penggunaan</h4>
                <ol class="list-decimal list-inside space-y-1.5 text-slate-500">
                    <li>Pilih <strong class="text-slate-700">Ekstrakurikuler</strong> dan <strong class="text-slate-700">Tanggal</strong> kegiatan.</li>
                    <li>Pilih status kehadiran untuk setiap anggota: <strong class="text-slate-700">Hadir, Izin, Sakit, atau Alpa</strong>.</li>
                    <li>Klik <strong class="text-slate-700">Simpan Absensi</strong> untuk menyimpan semua data.</li>
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
                        <i class="fa-solid fa-users text-purple-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Anggota Ekskul</div><div class="text-[11px] text-slate-400">Daftar anggota diambil dari <strong>Ekstrakurikuler → Anggota</strong>.</div></div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-chart-bar text-green-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Rekap Kehadiran</div><div class="text-[11px] text-slate-400">Laporan kehadiran tersedia di menu <strong>Laporan → Rekap Ekskul</strong>.</div></div>
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
    // Radio button visual toggle
    document.querySelectorAll('input[type="radio"]').forEach(function(radio) {
        radio.addEventListener('change', function() {
            var name = this.name;
            document.querySelectorAll('input[name="' + name + '"]').forEach(function(r) {
                var label = r.closest('label');
                var colors = { HADIR: 'green', IZIN: 'blue', SAKIT: 'yellow', ALPA: 'red' };
                var c = colors[r.value];
                label.className = label.className.replace(/bg-\w+-\d+\s|border-\w+-\d+\s|text-\w+-\d+/g, '').trim();
                if (r.checked) {
                    label.classList.add('bg-' + c + '-100', 'border-' + c + '-400', 'text-' + c + '-700');
                } else {
                    label.classList.add('bg-slate-50', 'border-slate-200', 'text-slate-500');
                }
            });
        });
    });

    window.onclick = function(e) {
        if (e.target == document.getElementById('infoModal')) document.getElementById('infoModal').classList.add('hidden');
    }
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
