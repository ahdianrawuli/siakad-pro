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
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Jadwal Pelajaran</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Kelola jadwal aktif tahun ajaran ini.</p>
            <div class="mt-3 flex items-center gap-2">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold border border-blue-100">
                    <i class="fa-solid fa-calendar-days"></i> Total Jadwal: <?= $totalData ?>
                </div>
                <button onclick="document.getElementById('infoModal').classList.remove('hidden')"
                    class="w-7 h-7 rounded-full bg-slate-100 text-slate-500 hover:bg-blue-100 hover:text-blue-600 flex items-center justify-center transition-colors border border-slate-200" title="Panduan Penggunaan">
                    <i class="fa-solid fa-circle-info text-sm"></i>
                </button>
            </div>
        </div>
        <div class="flex gap-2">
            <?php if ($filterClass || $filterDay): ?>
            <a href="/academic/schedules/print?class_id=<?= urlencode($filterClass) ?>&day=<?= urlencode($filterDay) ?>" target="_blank"
                class="px-4 py-2.5 bg-slate-600 text-white rounded-xl text-sm font-semibold hover:bg-slate-700 transition-all flex items-center gap-2 w-fit">
                <i class="fa-solid fa-print"></i> Cetak Jadwal
            </a>
            <?php endif; ?>
            <button onclick="document.getElementById('addModal').classList.remove('hidden')"
                class="px-4 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold shadow-md shadow-blue-500/20 hover:bg-blue-700 transition-all flex items-center gap-2 w-fit">
                <i class="fa-solid fa-calendar-plus"></i> Buat Jadwal Baru
            </button>
        </div>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <div class="flex flex-col gap-6">

        <!-- Filter -->
        <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
            <form method="GET" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-3">
                <input type="hidden" name="limit" value="<?= $limit ?>">
                <select name="class_id" class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                    <option value="">Semua Kelas</option>
                    <?php foreach ($classrooms as $c): ?>
                        <option value="<?= $c['id'] ?>" <?= $filterClass == $c['id'] ? 'selected' : '' ?>><?= $c['name'] ?></option>
                    <?php endforeach; ?>
                </select>
                <select name="day" class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                    <option value="">Semua Hari</option>
                    <?php foreach (['SENIN','SELASA','RABU','KAMIS','JUMAT','SABTU','AHAD'] as $d): ?>
                        <option value="<?= $d ?>" <?= $filterDay == $d ? 'selected' : '' ?>><?= $d ?></option>
                    <?php endforeach; ?>
                </select>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari Mapel / Guru..."
                        class="w-full pl-10 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 bg-slate-800 text-white py-2.5 rounded-xl text-sm font-bold hover:bg-slate-900 transition-colors">Terapkan</button>
                    <?php if (!empty($search) || !empty($filterClass) || !empty($filterDay)): ?>
                        <a href="/academic/schedules" class="flex items-center justify-center w-10 h-10 bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition" title="Reset">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    <?php endif; ?>
                </div>
            </form>
        </div>

        <!-- Table Card -->
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden flex flex-col">
            <div class="overflow-x-auto">
                <table class="min-w-full whitespace-nowrap text-left">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Hari / Waktu</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Kelas</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Mata Pelajaran</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Guru Pengampu</th>
                            <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 bg-white">
                        <?php if (empty($schedules)): ?>
                        <tr><td colspan="5" class="px-5 py-16 text-center text-slate-400 text-sm font-medium">Tidak ada jadwal yang ditemukan.</td></tr>
                        <?php endif; ?>

                        <?php foreach ($schedules as $row): ?>
                        <tr class="hover:bg-slate-50/80 transition-colors text-sm">
                            <td class="px-5 py-4">
                                <span class="px-2.5 py-1 bg-blue-50 text-blue-700 text-[10px] font-bold rounded-lg border border-blue-200"><?= $row['day'] ?></span>
                                <div class="text-xs text-slate-500 mt-1 font-mono"><?= date('H:i', strtotime($row['start_time'])) ?> – <?= date('H:i', strtotime($row['end_time'])) ?></div>
                            </td>
                            <td class="px-5 py-4 font-extrabold text-slate-800"><?= $row['class_name'] ?></td>
                            <td class="px-5 py-4 font-bold text-blue-600"><?= $row['subject_name'] ?></td>
                            <td class="px-5 py-4 text-slate-600 text-sm">
                                <i class="fa-solid fa-user-tie mr-1 text-slate-400"></i><?= $row['teacher_name'] ?>
                            </td>
                            <td class="px-5 py-4 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <button onclick="openEditModal(this)"
                                        data-id="<?= $row['id'] ?>" data-class="<?= $row['classroom_id'] ?>"
                                        data-subject="<?= $row['subject_id'] ?>" data-teacher="<?= $row['teacher_id'] ?>"
                                        data-day="<?= $row['day'] ?>" data-start="<?= $row['start_time'] ?>" data-end="<?= $row['end_time'] ?>"
                                        class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-600 hover:text-white inline-flex items-center justify-center transition-colors shadow-sm" title="Edit">
                                        <i class="fa-solid fa-pen-to-square text-sm"></i>
                                    </button>
                                    <a href="/academic/schedules/delete?id=<?= $row['id'] ?>"
                                        onclick="return confirm('Hapus jadwal ini?')"
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
                    <?php $qs = "&limit=$limit&search=" . urlencode($search) . "&class_id=$filterClass&day=$filterDay"; ?>
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
            <h3 class="font-bold text-blue-800 flex items-center gap-2"><i class="fa-solid fa-circle-info text-blue-500"></i> Panduan Jadwal Pelajaran</h3>
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-blue-100 text-blue-500 hover:bg-blue-200 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="p-6 space-y-5 text-sm text-slate-600 overflow-y-auto max-h-[70vh]">
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">1</span> Cara Penggunaan</h4>
                <ol class="list-decimal list-inside space-y-1.5 text-slate-500">
                    <li>Klik <strong class="text-slate-700">Buat Jadwal Baru</strong> untuk menambah sesi pelajaran.</li>
                    <li>Pilih kelas, hari, mata pelajaran, guru, dan jam mulai–selesai.</li>
                    <li>Gunakan filter <strong class="text-slate-700">Kelas</strong> atau <strong class="text-slate-700">Hari</strong> untuk mempersempit tampilan.</li>
                    <li>Klik ikon edit untuk mengubah jadwal yang sudah ada.</li>
                </ol>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">2</span> Relasi ke Menu Lain</h4>
                <div class="space-y-2">
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-chalkboard text-blue-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Data Kelas</div><div class="text-[11px] text-slate-400">Kelas yang tersedia diambil dari <strong>Master → Data Kelas</strong>.</div></div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-book-open text-purple-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Mata Pelajaran</div><div class="text-[11px] text-slate-400">Daftar mapel diambil dari <strong>Akademik → Mata Pelajaran</strong>.</div></div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-chalkboard-user text-green-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Manajemen Guru</div><div class="text-[11px] text-slate-400">Guru pengampu diambil dari <strong>Kepegawaian → Manajemen Guru</strong>.</div></div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-calendar-check text-orange-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Absensi & Jurnal KBM</div><div class="text-[11px] text-slate-400">Jadwal ini menjadi acuan sesi di menu <strong>Absensi Siswa</strong> dan <strong>Jurnal KBM</strong>.</div></div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-calendar-days text-slate-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Tahun Ajaran</div><div class="text-[11px] text-slate-400">Jadwal terikat pada tahun ajaran aktif di <strong>Akademik → Tahun Ajaran</strong>.</div></div>
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
            <h3 class="font-bold text-slate-700 flex items-center gap-2"><i class="fa-solid fa-calendar-plus text-slate-400"></i> Buat Jadwal Baru</h3>
            <button onclick="document.getElementById('addModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-slate-200 text-slate-500 hover:bg-slate-300 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="/academic/schedules/store" method="POST" class="p-6 space-y-4">
            <?= \App\Core\Csrf::input() ?>
            <input type="hidden" name="academic_year_id" value="<?= $academic_year_id ?>">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Kelas</label>
                    <select name="classroom_id" class="select2-add-class w-full" required>
                        <?php foreach ($classrooms as $c): ?><option value="<?= $c['id'] ?>"><?= $c['name'] ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Hari</label>
                    <select name="day" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none" required>
                        <?php foreach (['SENIN','SELASA','RABU','KAMIS','JUMAT','SABTU'] as $d): ?><option value="<?= $d ?>"><?= $d ?></option><?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Mata Pelajaran</label>
                <select name="subject_id" class="select2-add-subject w-full" required>
                    <?php foreach ($subjects as $s): ?><option value="<?= $s['id'] ?>"><?= $s['name'] ?></option><?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Guru Pengampu</label>
                <select name="teacher_id" class="select2-add-teacher w-full" required>
                    <?php foreach ($teachers as $t): ?><option value="<?= $t['id'] ?>"><?= $t['name'] ?></option><?php endforeach; ?>
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
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="flex-1 bg-slate-100 text-slate-600 py-2.5 rounded-xl font-bold hover:bg-slate-200 transition text-sm">Batal</button>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2.5 rounded-xl font-bold hover:bg-blue-700 shadow-md shadow-blue-500/20 transition text-sm">Simpan Jadwal</button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Edit -->
<div id="editModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
            <h3 class="font-bold text-slate-700 flex items-center gap-2"><i class="fa-solid fa-pen-to-square text-slate-400"></i> Edit Jadwal</h3>
            <button onclick="document.getElementById('editModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-slate-200 text-slate-500 hover:bg-slate-300 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <form action="/academic/schedules/update" method="POST" class="p-6 space-y-4">
            <?= \App\Core\Csrf::input() ?>
            <input type="hidden" name="id" id="edit_id">
            <input type="hidden" name="academic_year_id" value="<?= $academic_year_id ?>">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Kelas</label>
                    <select name="classroom_id" id="edit_class" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none" required>
                        <?php foreach ($classrooms as $c): ?><option value="<?= $c['id'] ?>"><?= $c['name'] ?></option><?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Hari</label>
                    <select name="day" id="edit_day" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none" required>
                        <?php foreach (['SENIN','SELASA','RABU','KAMIS','JUMAT','SABTU'] as $d): ?><option value="<?= $d ?>"><?= $d ?></option><?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Mata Pelajaran</label>
                <select name="subject_id" id="edit_subject" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none" required>
                    <?php foreach ($subjects as $s): ?><option value="<?= $s['id'] ?>"><?= $s['name'] ?></option><?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="block text-sm font-semibold text-slate-600 mb-1.5">Guru Pengampu</label>
                <select name="teacher_id" id="edit_teacher" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none" required>
                    <?php foreach ($teachers as $t): ?><option value="<?= $t['id'] ?>"><?= $t['name'] ?></option><?php endforeach; ?>
                </select>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Jam Mulai</label>
                    <input type="time" name="start_time" id="edit_start" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Jam Selesai</label>
                    <input type="time" name="end_time" id="edit_end" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none" required>
                </div>
            </div>
            <div class="flex gap-3 pt-2">
                <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="flex-1 bg-slate-100 text-slate-600 py-2.5 rounded-xl font-bold hover:bg-slate-200 transition text-sm">Batal</button>
                <button type="submit" class="flex-1 bg-blue-600 text-white py-2.5 rounded-xl font-bold hover:bg-blue-700 shadow-md shadow-blue-500/20 transition text-sm">Update Jadwal</button>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    $(document).ready(function() {
        $('.select2-add-class').select2({ placeholder: '-- Pilih Kelas --', dropdownParent: $('#addModal') });
        $('.select2-add-subject').select2({ placeholder: '-- Pilih Mata Pelajaran --', dropdownParent: $('#addModal') });
        $('.select2-add-teacher').select2({ placeholder: '-- Pilih Guru --', dropdownParent: $('#addModal') });
    });

    function openEditModal(btn) {
        document.getElementById('edit_id').value = btn.dataset.id;
        document.getElementById('edit_class').value = btn.dataset.class;
        document.getElementById('edit_subject').value = btn.dataset.subject;
        document.getElementById('edit_teacher').value = btn.dataset.teacher;
        document.getElementById('edit_day').value = btn.dataset.day;
        document.getElementById('edit_start').value = btn.dataset.start;
        document.getElementById('edit_end').value = btn.dataset.end;
        document.getElementById('editModal').classList.remove('hidden');
    }

    function updateQueryStringParameter(uri, key, value) {
        var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        var separator = uri.indexOf('?') !== -1 ? "&" : "?";
        return uri.match(re) ? uri.replace(re, '$1' + key + "=" + value + '$2') : uri + separator + key + "=" + value;
    }

    window.onclick = function(e) {
        ['addModal','editModal','infoModal'].forEach(function(id) {
            if (e.target == document.getElementById(id)) document.getElementById(id).classList.add('hidden');
        });
    }
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
