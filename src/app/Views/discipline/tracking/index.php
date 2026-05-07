<?php require __DIR__ . '/../../layouts/header.php'; ?>
<?php require __DIR__ . '/../../layouts/sidebar.php'; ?>

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
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Pelacakan Santri</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Monitoring lokasi dan aktivitas harian santri.</p>
            <div class="mt-3 flex items-center gap-2">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold border border-blue-100">
                    <i class="fa-solid fa-map-location-dot"></i> Log Hari Ini: <?= $totalData ?>
                </div>
                <button onclick="document.getElementById('infoModal').classList.remove('hidden')"
                    class="w-7 h-7 rounded-full bg-slate-100 text-slate-500 hover:bg-blue-100 hover:text-blue-600 flex items-center justify-center transition-colors border border-slate-200" title="Panduan Penggunaan">
                    <i class="fa-solid fa-circle-info text-sm"></i>
                </button>
            </div>
        </div>
        <button onclick="document.getElementById('addModal').classList.remove('hidden')"
            class="px-4 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold shadow-md shadow-blue-500/20 hover:bg-blue-700 transition-all flex items-center gap-2 w-fit">
            <i class="fa-solid fa-map-location-dot"></i> Catat Aktivitas
        </button>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <div class="flex flex-col gap-6">

        <!-- Filter -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
            <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                <input type="hidden" name="limit" value="<?= $limit ?>">
                <input type="date" name="date" value="<?= $date ?>"
                    class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                <div class="md:col-span-2 relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari Nama Santri atau Lokasi..."
                        class="w-full pl-10 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-slate-800 text-white py-2.5 rounded-xl text-sm font-bold hover:bg-slate-900 transition-colors">Terapkan</button>
                    <?php if (!empty($search) || $date != date('Y-m-d')): ?>
                        <a href="/discipline/tracking" class="flex items-center justify-center w-10 h-10 bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition" title="Reset">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
            <div class="p-5 border-b border-slate-100 bg-slate-50">
                <h4 class="font-bold text-slate-700 flex items-center gap-2">
                    <i class="fa-solid fa-list-check text-slate-400"></i>
                    Log Aktivitas — <?= date('d M Y', strtotime($date)) ?>
                </h4>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full whitespace-nowrap text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Jam</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Santri</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Jenis</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Lokasi</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Keterangan</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Pelapor</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <?php if (empty($logs)): ?>
                            <tr><td colspan="7" class="px-5 py-16 text-center text-slate-400 text-sm font-medium">Tidak ada data aktivitas.</td></tr>
                        <?php endif; ?>

                        <?php foreach ($logs as $log): ?>
                        <tr class="hover:bg-slate-50/80 transition-colors text-sm">
                            <td class="px-5 py-4 font-mono text-blue-600 font-bold text-xs"><?= date('H:i', strtotime($log['logged_at'])) ?></td>
                            <td class="px-5 py-4">
                                <div class="font-extrabold text-slate-800"><?= $log['full_name'] ?></div>
                                <div class="text-[10px] text-slate-400 mt-0.5">NIS: <?= $log['nis'] ?></div>
                            </td>
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg border
                                    <?= $log['activity_type'] == 'LOCATION' ? 'bg-green-50 text-green-700 border-green-200' :
                                       ($log['activity_type'] == 'INCIDENT' ? 'bg-red-50 text-red-700 border-red-200' : 'bg-blue-50 text-blue-700 border-blue-200') ?>">
                                    <?= $log['activity_type'] ?>
                                </span>
                            </td>
                            <td class="px-5 py-4 font-semibold text-slate-700"><?= $log['location'] ?></td>
                            <td class="px-5 py-4 text-xs text-slate-500 italic max-w-xs truncate"><?= $log['description'] ?: '-' ?></td>
                            <td class="px-5 py-4 text-xs text-slate-500"><?= $log['reporter_name'] ?></td>
                            <td class="px-5 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button onclick="openEditModal(this)"
                                        data-id="<?= $log['id'] ?>" data-student="<?= $log['student_id'] ?>"
                                        data-type="<?= $log['activity_type'] ?>" data-location="<?= htmlspecialchars($log['location']) ?>"
                                        data-desc="<?= htmlspecialchars($log['description'] ?? '') ?>"
                                        data-date="<?= date('Y-m-d', strtotime($log['logged_at'])) ?>"
                                        data-time="<?= date('H:i', strtotime($log['logged_at'])) ?>"
                                        class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white inline-flex items-center justify-center transition-colors shadow-sm" title="Edit">
                                        <i class="fa-solid fa-pen-to-square text-sm"></i>
                                    </button>
                                    <a href="/discipline/tracking/delete?id=<?= $log['id'] ?>"
                                        onclick="return confirm('Hapus log ini?')"
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
                    <select onchange="window.location.href=updateQueryStringParameter(window.location.href, 'limit', this.value)"
                        class="border border-slate-300 rounded-lg px-2 py-1 text-sm outline-none focus:ring-2 focus:ring-blue-500/50 bg-white font-medium">
                        <option value="10" <?= $limit == 10 ? 'selected' : '' ?>>10 entries</option>
                        <option value="50" <?= $limit == 50 ? 'selected' : '' ?>>50 entries</option>
                        <option value="100" <?= $limit == 100 ? 'selected' : '' ?>>100 entries</option>
                    </select>
                </div>
                <?php if ($totalPages > 1): ?>
                <div class="flex items-center gap-1.5">
                    <?php $qs = "&limit=$limit&search=" . urlencode($search) . "&date=$date"; ?>
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

<!-- Modal Info / Panduan -->
<div id="infoModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-blue-50 flex justify-between items-center">
            <h3 class="font-bold text-blue-800 flex items-center gap-2">
                <i class="fa-solid fa-circle-info text-blue-500"></i> Panduan Pelacakan Santri
            </h3>
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-blue-100 text-blue-500 hover:bg-blue-200 flex items-center justify-center transition">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>
        <div class="p-6 space-y-5 text-sm text-slate-600 overflow-y-auto max-h-[70vh]">

            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">1</span>
                    Apa itu Pelacakan Santri?
                </h4>
                <p class="text-slate-500 leading-relaxed">Halaman ini digunakan untuk mencatat dan memantau <strong class="text-slate-700">lokasi serta aktivitas harian santri</strong> secara real-time. Setiap log berisi informasi santri, waktu, lokasi, jenis aktivitas, dan keterangan tambahan.</p>
            </div>

            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">2</span>
                    Jenis Log Aktivitas
                </h4>
                <div class="space-y-2">
                    <div class="flex items-start gap-2">
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-lg bg-green-50 text-green-700 border border-green-200 mt-0.5 whitespace-nowrap">LOCATION</span>
                        <span class="text-slate-500">Pencatatan posisi/keberadaan santri di suatu lokasi (cth: Masjid, Kantin, Asrama).</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-lg bg-blue-50 text-blue-700 border border-blue-200 mt-0.5 whitespace-nowrap">ACTIVITY</span>
                        <span class="text-slate-500">Pencatatan kegiatan yang sedang atau telah dilakukan santri (cth: mengikuti kajian, olahraga).</span>
                    </div>
                    <div class="flex items-start gap-2">
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-lg bg-red-50 text-red-700 border border-red-200 mt-0.5 whitespace-nowrap">INCIDENT</span>
                        <span class="text-slate-500">Pencatatan kejadian atau insiden yang melibatkan santri dan perlu ditindaklanjuti.</span>
                    </div>
                </div>
            </div>

            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">3</span>
                    Cara Penggunaan
                </h4>
                <ol class="list-decimal list-inside space-y-1.5 text-slate-500">
                    <li>Klik tombol <strong class="text-slate-700">Catat Aktivitas</strong> di pojok kanan atas.</li>
                    <li>Pilih santri menggunakan kolom pencarian (Select2).</li>
                    <li>Isi tanggal, jam, jenis log, lokasi, dan keterangan.</li>
                    <li>Klik <strong class="text-slate-700">Simpan</strong> — log akan langsung muncul di tabel.</li>
                    <li>Gunakan filter <strong class="text-slate-700">Tanggal</strong> untuk melihat log hari tertentu.</li>
                </ol>
            </div>

            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2">
                    <span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">4</span>
                    Relasi ke Menu Lain
                </h4>
                <div class="space-y-2">
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-triangle-exclamation text-red-400 w-5 text-center"></i>
                        <div>
                            <div class="font-semibold text-slate-700 text-xs">Kedisiplinan Siswa</div>
                            <div class="text-[11px] text-slate-400">Log jenis INCIDENT dapat dijadikan dasar pencatatan pelanggaran di menu Kedisiplinan.</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-heart-pulse text-pink-400 w-5 text-center"></i>
                        <div>
                            <div class="font-semibold text-slate-700 text-xs">Bimbingan Konseling (BK)</div>
                            <div class="text-[11px] text-slate-400">Insiden yang berulang dapat dirujuk ke sesi konseling di menu BK.</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-bed text-indigo-400 w-5 text-center"></i>
                        <div>
                            <div class="font-semibold text-slate-700 text-xs">Manajemen Asrama</div>
                            <div class="text-[11px] text-slate-400">Log lokasi asrama berkaitan dengan data kamar dan pembina asrama di menu Asrama.</div>
                        </div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-calendar-check text-green-400 w-5 text-center"></i>
                        <div>
                            <div class="font-semibold text-slate-700 text-xs">Absensi Siswa</div>
                            <div class="text-[11px] text-slate-400">Data kehadiran di kelas dapat dikonfirmasi silang dengan log lokasi santri pada jam yang sama.</div>
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

<!-- Modal Tambah -->
<div id="addModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-700 flex items-center gap-2"><i class="fa-solid fa-map-location-dot text-blue-500"></i> Catat Aktivitas Baru</h3>
            <button onclick="document.getElementById('addModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-slate-200 text-slate-500 hover:bg-slate-300 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="/discipline/tracking/store" method="POST" class="p-6 space-y-4">
            <?= \App\Core\Csrf::input() ?>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Pilih Santri</label>
                <select name="student_id" class="select2-add w-full" required>
                    <option value="">-- Cari & Pilih Nama Santri --</option>
                    <?php foreach ($students as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= $s['full_name'] ?> (<?= $s['nis'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Tanggal</label>
                    <input type="date" name="date" value="<?= date('Y-m-d') ?>"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Jam</label>
                    <input type="time" name="time" value="<?= date('H:i') ?>"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Jenis Log</label>
                    <select name="activity_type" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                        <option value="LOCATION">Cek Lokasi</option>
                        <option value="ACTIVITY">Kegiatan</option>
                        <option value="INCIDENT">Insiden/Kejadian</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Lokasi</label>
                    <input type="text" name="location" placeholder="cth: Kantin, Masjid, Asrama"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" required>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Keterangan <span class="text-slate-400 font-normal">(Opsional)</span></label>
                <textarea name="description" rows="3" placeholder="cth: Sedang makan siang bersama teman-teman..."
                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all resize-none"></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="flex-1 bg-slate-100 text-slate-600 py-2.5 rounded-xl font-bold hover:bg-slate-200 transition text-sm">Batal</button>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2.5 rounded-xl font-bold hover:bg-blue-700 shadow-md shadow-blue-500/20 transition text-sm">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="editModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-700 flex items-center gap-2"><i class="fa-solid fa-pen-to-square text-slate-400"></i> Edit Log Aktivitas</h3>
            <button onclick="document.getElementById('editModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-slate-200 text-slate-500 hover:bg-slate-300 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="/discipline/tracking/update" method="POST" class="p-6 space-y-4">
            <?= \App\Core\Csrf::input() ?>
            <input type="hidden" name="id" id="edit_id">
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Pilih Santri</label>
                <select name="student_id" id="edit_student" class="select2-edit w-full" required>
                    <?php foreach ($students as $s): ?>
                        <option value="<?= $s['id'] ?>"><?= $s['full_name'] ?> (<?= $s['nis'] ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Tanggal</label>
                    <input type="date" name="date" id="edit_date"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Jam</label>
                    <input type="time" name="time" id="edit_time"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Jenis Log</label>
                    <select name="activity_type" id="edit_type" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                        <option value="LOCATION">Cek Lokasi</option>
                        <option value="ACTIVITY">Kegiatan</option>
                        <option value="INCIDENT">Insiden/Kejadian</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Lokasi</label>
                    <input type="text" name="location" id="edit_location" placeholder="cth: Kantin, Masjid, Asrama"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" required>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Keterangan</label>
                <textarea name="description" id="edit_desc" rows="3" placeholder="cth: Sedang makan siang bersama teman-teman..."
                    class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all resize-none"></textarea>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="flex-1 bg-slate-100 text-slate-600 py-2.5 rounded-xl font-bold hover:bg-slate-200 transition text-sm">Batal</button>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2.5 rounded-xl font-bold hover:bg-blue-700 shadow-md shadow-blue-500/20 transition text-sm">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2-add').select2({ placeholder: '-- Cari & Pilih Nama Santri --', allowClear: true, dropdownParent: $('#addModal') });
        $('.select2-edit').select2({ placeholder: '-- Cari & Pilih Nama Santri --', allowClear: true, dropdownParent: $('#editModal') });
    });

    function openEditModal(btn) {
        document.getElementById('edit_id').value = btn.dataset.id;
        document.getElementById('edit_date').value = btn.dataset.date;
        document.getElementById('edit_time').value = btn.dataset.time;
        document.getElementById('edit_type').value = btn.dataset.type;
        document.getElementById('edit_location').value = btn.dataset.location;
        document.getElementById('edit_desc').value = btn.dataset.desc;
        $('#edit_student').val(btn.dataset.student).trigger('change');
        document.getElementById('editModal').classList.remove('hidden');
    }

    function updateQueryStringParameter(uri, key, value) {
        var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        var separator = uri.indexOf('?') !== -1 ? "&" : "?";
        return uri.match(re) ? uri.replace(re, '$1' + key + "=" + value + '$2') : uri + separator + key + "=" + value;
    }

    window.onclick = function(e) {
        if (e.target == document.getElementById('addModal')) document.getElementById('addModal').classList.add('hidden');
        if (e.target == document.getElementById('editModal')) document.getElementById('editModal').classList.add('hidden');
    }
</script>

<?php require __DIR__ . '/../../layouts/footer.php'; ?>
