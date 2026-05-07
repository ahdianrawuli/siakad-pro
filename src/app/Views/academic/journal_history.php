<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight"><?= $schedule['subject_name'] ?> — <?= $schedule['class_name'] ?></h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">
                <i class="fa-regular fa-clock mr-1"></i><?= $schedule['day'] ?>, <?= substr($schedule['start_time'],0,5) ?> – <?= substr($schedule['end_time'],0,5) ?>
            </p>
            <div class="mt-3 flex items-center gap-2">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold border border-blue-100">
                    <i class="fa-solid fa-list-check"></i> Total Pertemuan: <?= $totalData ?>
                </div>
                <button onclick="document.getElementById('infoModal').classList.remove('hidden')"
                    class="w-7 h-7 rounded-full bg-slate-100 text-slate-500 hover:bg-blue-100 hover:text-blue-600 flex items-center justify-center transition-colors border border-slate-200" title="Panduan Penggunaan">
                    <i class="fa-solid fa-circle-info text-sm"></i>
                </button>
            </div>
        </div>
        <div class="flex gap-2">
            <a href="/academic/journals" class="px-4 py-2.5 bg-white text-slate-700 border border-slate-300 rounded-xl text-sm font-semibold hover:bg-slate-50 transition-all flex items-center gap-2">
                <i class="fa-solid fa-arrow-left text-slate-400"></i> Kembali
            </a>
            <button onclick="openModal('add')"
                class="px-4 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold shadow-md shadow-blue-500/20 hover:bg-blue-700 transition-all flex items-center gap-2">
                <i class="fa-solid fa-plus"></i> Isi Jurnal Baru
            </button>
        </div>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <div class="flex flex-col gap-6">

        <!-- Filter -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
            <form method="GET" class="flex flex-wrap items-center gap-3">
                <input type="hidden" name="schedule_id" value="<?= $schedule['id'] ?>">
                <input type="hidden" name="limit" value="<?= $limit ?>">
                <div class="flex-1 min-w-[220px] relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari Materi / Topik..."
                        class="w-full pl-10 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
                </div>
                <input type="date" name="date" value="<?= $dateFilter ?>"
                    class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                <button type="submit" class="bg-slate-800 text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-slate-900 transition-colors">Terapkan</button>
                <?php if (!empty($search) || !empty($dateFilter)): ?>
                    <a href="/academic/journals/history?schedule_id=<?= $schedule['id'] ?>" class="flex items-center justify-center w-10 h-10 bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition" title="Reset">
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
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Tanggal</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Materi / Topik</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Catatan</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Kehadiran</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <?php if (empty($journals)): ?>
                        <tr><td colspan="5" class="px-5 py-16 text-center text-slate-400 text-sm font-medium">Belum ada jurnal yang tercatat.</td></tr>
                        <?php endif; ?>

                        <?php foreach ($journals as $row):
                            $sakit = 0; $izin = 0; $alpa = 0;
                            if (isset($attendanceData[$row['id']])) {
                                foreach ($attendanceData[$row['id']] as $status) {
                                    if ($status == 'S') $sakit++;
                                    if ($status == 'I') $izin++;
                                    if ($status == 'A') $alpa++;
                                }
                            }
                        ?>
                        <tr class="hover:bg-slate-50/80 transition-colors text-sm">
                            <td class="px-5 py-4 font-mono text-blue-600 font-bold text-xs"><?= date('d M Y', strtotime($row['date'])) ?></td>
                            <td class="px-5 py-4 font-extrabold text-slate-800"><?= $row['topic'] ?></td>
                            <td class="px-5 py-4 text-slate-500 italic text-xs max-w-xs truncate"><?= $row['notes'] ?: '-' ?></td>
                            <td class="px-5 py-4 text-center">
                                <?php if ($sakit + $izin + $alpa > 0): ?>
                                    <div class="flex justify-center gap-1 text-[10px] font-bold">
                                        <?php if ($sakit): ?><span class="bg-yellow-50 text-yellow-700 px-2 py-0.5 rounded-lg border border-yellow-200">S:<?= $sakit ?></span><?php endif; ?>
                                        <?php if ($izin): ?><span class="bg-blue-50 text-blue-700 px-2 py-0.5 rounded-lg border border-blue-200">I:<?= $izin ?></span><?php endif; ?>
                                        <?php if ($alpa): ?><span class="bg-red-50 text-red-700 px-2 py-0.5 rounded-lg border border-red-200">A:<?= $alpa ?></span><?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <span class="inline-flex items-center gap-1 text-[10px] font-bold text-green-600"><i class="fa-solid fa-circle-check"></i> Nihil</span>
                                <?php endif; ?>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button onclick='openModal("edit", <?= json_encode($row) ?>, <?= json_encode($attendanceData[$row['id']] ?? []) ?>)'
                                        class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white inline-flex items-center justify-center transition-colors shadow-sm" title="Edit">
                                        <i class="fa-solid fa-pen-to-square text-sm"></i>
                                    </button>
                                    <a href="/academic/journals/delete?id=<?= $row['id'] ?>&schedule_id=<?= $schedule['id'] ?>"
                                        onclick="return confirm('Hapus jurnal ini?')"
                                        class="w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white inline-flex items-center justify-center transition-colors shadow-sm" title="Hapus">
                                        <i class="fa-solid fa-trash-can text-sm"></i>
                                    </a>
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
                    <select onchange="window.location.href=updateQS(window.location.href, 'limit', this.value)"
                        class="border border-slate-300 rounded-lg px-2 py-1 text-sm outline-none bg-white font-medium">
                        <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10 entries</option>
                        <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50 entries</option>
                    </select>
                </div>
                <?php if ($totalPages > 1): ?>
                <div class="flex items-center gap-1.5">
                    <?php $qs = "&schedule_id={$schedule['id']}&limit=$limit&search=" . urlencode($search) . "&date=$dateFilter"; ?>
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
            <h3 class="font-bold text-blue-800 flex items-center gap-2"><i class="fa-solid fa-circle-info text-blue-500"></i> Panduan Riwayat Jurnal</h3>
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-blue-100 text-blue-500 hover:bg-blue-200 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="p-6 space-y-5 text-sm text-slate-600 overflow-y-auto max-h-[70vh]">
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">1</span> Cara Penggunaan</h4>
                <ol class="list-decimal list-inside space-y-1.5 text-slate-500">
                    <li>Klik <strong class="text-slate-700">Isi Jurnal Baru</strong> untuk menambah catatan pertemuan.</li>
                    <li>Isi tanggal, topik materi, catatan evaluasi, dan absensi tiap siswa.</li>
                    <li>Gunakan filter <strong class="text-slate-700">Topik</strong> atau <strong class="text-slate-700">Tanggal</strong> untuk mencari pertemuan tertentu.</li>
                    <li>Klik ikon edit untuk mengubah jurnal yang sudah ada.</li>
                </ol>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">2</span> Kode Absensi</h4>
                <div class="flex gap-3 flex-wrap">
                    <span class="px-3 py-1.5 bg-green-50 text-green-700 rounded-lg border border-green-200 text-xs font-bold">H — Hadir</span>
                    <span class="px-3 py-1.5 bg-yellow-50 text-yellow-700 rounded-lg border border-yellow-200 text-xs font-bold">S — Sakit</span>
                    <span class="px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg border border-blue-200 text-xs font-bold">I — Izin</span>
                    <span class="px-3 py-1.5 bg-red-50 text-red-700 rounded-lg border border-red-200 text-xs font-bold">A — Alpa</span>
                </div>
                <p class="text-slate-400 text-xs mt-2">Kolom <strong>Kehadiran</strong> di tabel menampilkan ringkasan siswa yang tidak hadir (S/I/A). Jika nihil berarti semua hadir.</p>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">3</span> Relasi ke Menu Lain</h4>
                <div class="space-y-2">
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-book-open text-blue-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Jurnal Guru (Daftar Jadwal)</div><div class="text-[11px] text-slate-400">Halaman ini adalah detail dari kartu jadwal di <strong>Akademik → Jurnal Guru</strong>.</div></div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-star text-yellow-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Penilaian / Nilai</div><div class="text-[11px] text-slate-400">Data kehadiran di jurnal ini dapat dijadikan komponen nilai keaktifan di menu <strong>Akademik → Nilai</strong>.</div></div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-file-lines text-orange-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Laporan Wali Kelas</div><div class="text-[11px] text-slate-400">Rekap pertemuan ini menjadi bagian dari laporan di menu <strong>Wali Kelas → Laporan</strong>.</div></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="p-4 bg-slate-50 border-t border-slate-100 flex justify-end">
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="px-5 py-2 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700 transition">Mengerti</button>
        </div>
    </div>
</div>

<!-- Modal Jurnal -->
<div id="journalModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 p-4">
    <div class="bg-white rounded-2xl w-full max-w-3xl shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-700" id="modalTitle">Form Jurnal</h3>
            <button onclick="closeModal()" class="w-8 h-8 rounded-lg bg-slate-200 text-slate-500 hover:bg-slate-300 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>

        <form id="journalForm" method="POST" class="flex-1 overflow-y-auto p-6 space-y-4">
            <?= \App\Core\Csrf::input() ?>
            <input type="hidden" name="schedule_id" value="<?= $schedule['id'] ?>">
            <input type="hidden" name="id" id="journalId">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Tanggal</label>
                    <input type="date" name="date" id="inputDate" value="<?= date('Y-m-d') ?>"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Materi / Topik</label>
                    <input type="text" name="topic" id="inputTopic" placeholder="cth: Bab 1 – Aljabar Dasar"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" required>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Catatan / Evaluasi <span class="text-slate-400 font-normal">(Opsional)</span></label>
                <textarea name="notes" id="inputNotes" rows="2" placeholder="cth: Siswa antusias, perlu remedial untuk 3 siswa..."
                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all resize-none"></textarea>
            </div>

            <div class="bg-slate-50 rounded-2xl p-4 border border-slate-200">
                <h4 class="font-bold text-slate-700 text-sm mb-3 border-b border-slate-200 pb-2 flex items-center gap-2">
                    <i class="fa-solid fa-clipboard-list text-slate-400"></i> Absensi Siswa
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-2 max-h-60 overflow-y-auto">
                    <?php foreach ($students as $s): ?>
                    <div class="flex items-center justify-between text-sm py-1.5 border-b border-slate-100 last:border-0">
                        <span class="text-slate-700 font-medium truncate w-1/2"><?= $s['full_name'] ?></span>
                        <div class="flex gap-1">
                            <?php foreach (['H' => 'green', 'S' => 'yellow', 'I' => 'blue', 'A' => 'red'] as $val => $color): ?>
                            <label class="cursor-pointer">
                                <input type="radio" name="attendance[<?= $s['id'] ?>]" value="<?= $val ?>" class="peer sr-only att-radio-<?= $s['id'] ?>" <?= $val == 'H' ? 'checked' : '' ?>>
                                <span class="px-2 py-0.5 rounded-lg text-[10px] font-bold border border-slate-200 text-slate-400 peer-checked:text-white peer-checked:border-transparent transition-all
                                    <?= $val=='H' ? 'peer-checked:bg-green-500' : ($val=='S' ? 'peer-checked:bg-yellow-400' : ($val=='I' ? 'peer-checked:bg-blue-500' : 'peer-checked:bg-red-500')) ?>">
                                    <?= $val ?>
                                </span>
                            </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </form>

        <div class="p-4 border-t border-slate-100 bg-slate-50 flex justify-end gap-2">
            <button type="button" onclick="closeModal()" class="px-4 py-2.5 bg-slate-100 text-slate-600 rounded-xl text-sm font-bold hover:bg-slate-200 transition">Batal</button>
            <button type="submit" form="journalForm" class="px-6 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-bold hover:bg-blue-700 shadow-md shadow-blue-500/20 transition">Simpan Jurnal</button>
        </div>
    </div>
</div>

<script>
    function openModal(mode, data = null, attendance = null) {
        const form = document.getElementById('journalForm');
        document.getElementById('modalTitle').innerText = mode === 'add' ? 'Tambah Jurnal Pertemuan' : 'Edit Jurnal Pertemuan';
        form.action = mode === 'add' ? '/academic/journals/store' : '/academic/journals/update';
        document.getElementById('journalId').value = data?.id ?? '';
        document.getElementById('inputDate').value = data?.date ?? '<?= date('Y-m-d') ?>';
        document.getElementById('inputTopic').value = data?.topic ?? '';
        document.getElementById('inputNotes').value = data?.notes ?? '';

        document.querySelectorAll('input[type=radio][value=H]').forEach(r => r.checked = true);
        if (attendance) {
            for (const [sid, status] of Object.entries(attendance)) {
                const r = document.querySelector(`.att-radio-${sid}[value="${status}"]`);
                if (r) r.checked = true;
            }
        }
        document.getElementById('journalModal').classList.remove('hidden');
    }

    function closeModal() { document.getElementById('journalModal').classList.add('hidden'); }

    function updateQS(uri, key, value) {
        var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        var sep = uri.indexOf('?') !== -1 ? "&" : "?";
        return uri.match(re) ? uri.replace(re, '$1' + key + "=" + value + '$2') : uri + sep + key + "=" + value;
    }

    window.onclick = function(e) {
        if (e.target == document.getElementById('journalModal')) closeModal();
        if (e.target == document.getElementById('infoModal')) document.getElementById('infoModal').classList.add('hidden');
    }
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
