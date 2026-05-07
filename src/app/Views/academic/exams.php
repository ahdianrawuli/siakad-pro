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
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Bank Soal & Arsip Ujian</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Kelola repository soal dan dokumen ujian.</p>
            <div class="mt-3 flex items-center gap-2">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold border border-blue-100">
                    <i class="fa-solid fa-file-lines"></i> Total Soal: <?= $totalData ?>
                </div>
                <button onclick="document.getElementById('infoModal').classList.remove('hidden')"
                    class="w-7 h-7 rounded-full bg-slate-100 text-slate-500 hover:bg-blue-100 hover:text-blue-600 flex items-center justify-center transition-colors border border-slate-200" title="Panduan Penggunaan">
                    <i class="fa-solid fa-circle-info text-sm"></i>
                </button>
            </div>
        </div>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Form Upload -->
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 h-fit">
            <h4 class="font-bold text-slate-700 flex items-center gap-2 border-b border-slate-100 pb-4 mb-5">
                <i class="fa-solid fa-cloud-arrow-up text-slate-400"></i> Upload Soal Baru
            </h4>
            <form action="/academic/exams/store" method="POST" enctype="multipart/form-data" class="space-y-4">
                <?= \App\Core\Csrf::input() ?>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Mata Pelajaran</label>
                    <select name="subject_id" class="select2-subject w-full" required>
                        <option value="">-- Pilih Mata Pelajaran --</option>
                        <?php foreach ($subjects as $s): ?>
                            <option value="<?= $s['id'] ?>"><?= $s['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Judul / Materi</label>
                    <input type="text" name="title" placeholder="cth: Soal UAS Semester 1 – Matematika"
                        class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" required>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">Tipe Ujian</label>
                    <select name="type" class="w-full px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                        <option value="LATIHAN">Latihan / PR</option>
                        <option value="QUIZ">Kuis Harian</option>
                        <option value="UTS">UTS</option>
                        <option value="UAS">UAS</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-semibold text-slate-600 mb-1.5">File Dokumen</label>
                    <input type="file" name="file"
                        class="w-full text-sm text-slate-500 file:mr-3 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 transition-all" required>
                    <p class="text-xs text-slate-400 mt-1.5">Format: PDF / Word. Maks 2MB.</p>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2.5 rounded-xl font-bold hover:bg-blue-700 shadow-md shadow-blue-500/20 transition-all text-sm">
                    <i class="fa-solid fa-cloud-arrow-up mr-2"></i> Upload Soal
                </button>
            </form>
        </div>

        <!-- Tabel -->
        <div class="lg:col-span-2 flex flex-col gap-6">

            <!-- Filter -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200">
                <form method="GET" class="flex flex-wrap items-center gap-3">
                    <input type="hidden" name="limit" value="<?= $limit ?>">
                    <div class="flex-1 min-w-[200px] relative">
                        <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari judul atau mata pelajaran..."
                            class="w-full pl-10 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
                    </div>
                    <select name="type" class="py-2.5 px-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white outline-none">
                        <option value="">Semua Tipe</option>
                        <option value="LATIHAN" <?= $typeFilter == 'LATIHAN' ? 'selected' : '' ?>>Latihan / PR</option>
                        <option value="QUIZ"    <?= $typeFilter == 'QUIZ'    ? 'selected' : '' ?>>Kuis Harian</option>
                        <option value="UTS"     <?= $typeFilter == 'UTS'     ? 'selected' : '' ?>>UTS</option>
                        <option value="UAS"     <?= $typeFilter == 'UAS'     ? 'selected' : '' ?>>UAS</option>
                    </select>
                    <button type="submit" class="bg-slate-800 text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-slate-900 transition-colors">Terapkan</button>
                    <?php if (!empty($search) || !empty($typeFilter)): ?>
                        <a href="/academic/exams" class="flex items-center justify-center w-10 h-10 bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition" title="Reset">
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
                                <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Mapel & Judul</th>
                                <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Tipe</th>
                                <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider">Uploader</th>
                                <th class="px-5 py-4 text-xs font-bold text-slate-500 uppercase tracking-wider text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            <?php if (empty($exams)): ?>
                                <tr><td colspan="4" class="px-5 py-16 text-center text-slate-400 text-sm font-medium">Belum ada soal yang diupload.</td></tr>
                            <?php endif; ?>
                            <?php foreach ($exams as $e): ?>
                            <tr class="hover:bg-slate-50/80 transition-colors text-sm">
                                <td class="px-5 py-4">
                                    <div class="font-bold text-blue-600 text-xs"><?= $e['subject_name'] ?></div>
                                    <div class="font-extrabold text-slate-800 mt-0.5"><?= $e['title'] ?></div>
                                    <div class="text-[10px] text-slate-400 mt-0.5"><?= date('d M Y', strtotime($e['created_at'])) ?></div>
                                </td>
                                <td class="px-5 py-4">
                                    <?php
                                    $typeColors = [
                                        'UAS'     => 'bg-green-50 text-green-700 border-green-200',
                                        'UTS'     => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'QUIZ'    => 'bg-yellow-50 text-yellow-700 border-yellow-200',
                                        'LATIHAN' => 'bg-slate-100 text-slate-600 border-slate-200',
                                    ];
                                    ?>
                                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg border <?= $typeColors[$e['type']] ?? 'bg-slate-100 text-slate-600 border-slate-200' ?>">
                                        <?= $e['type'] ?>
                                    </span>
                                </td>
                                <td class="px-5 py-4 text-sm text-slate-600"><?= $e['teacher_name'] ?></td>
                                <td class="px-5 py-4 text-center">
                                    <a href="/uploads/exams/<?= $e['file_path'] ?>" target="_blank"
                                        class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-50 text-blue-600 rounded-lg text-xs font-bold border border-blue-200 hover:bg-blue-600 hover:text-white transition-colors">
                                        <i class="fa-solid fa-download"></i> Unduh
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
                        <?php $qs = "&limit=$limit&search=" . urlencode($search) . "&type=$typeFilter"; ?>
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
    </div>
</main>

<!-- Modal Info -->
<div id="infoModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-blue-50 flex justify-between items-center">
            <h3 class="font-bold text-blue-800 flex items-center gap-2"><i class="fa-solid fa-circle-info text-blue-500"></i> Panduan Bank Soal</h3>
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-blue-100 text-blue-500 hover:bg-blue-200 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="p-6 space-y-5 text-sm text-slate-600 overflow-y-auto max-h-[70vh]">
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">1</span> Cara Penggunaan</h4>
                <ol class="list-decimal list-inside space-y-1.5 text-slate-500">
                    <li>Pilih mata pelajaran, isi judul, pilih tipe ujian, lalu upload file PDF/Word.</li>
                    <li>Gunakan filter <strong class="text-slate-700">Tipe</strong> atau kolom pencarian untuk menemukan soal tertentu.</li>
                    <li>Klik <strong class="text-slate-700">Unduh</strong> untuk mengunduh file soal.</li>
                </ol>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">2</span> Tipe Ujian</h4>
                <div class="flex flex-wrap gap-2">
                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg bg-slate-100 text-slate-600 border border-slate-200">LATIHAN — PR / Latihan Harian</span>
                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg bg-yellow-50 text-yellow-700 border border-yellow-200">QUIZ — Kuis Harian</span>
                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg bg-blue-50 text-blue-700 border border-blue-200">UTS — Ujian Tengah Semester</span>
                    <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg bg-green-50 text-green-700 border border-green-200">UAS — Ujian Akhir Semester</span>
                </div>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">3</span> Relasi ke Menu Lain</h4>
                <div class="space-y-2">
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-book-open text-purple-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Mata Pelajaran</div><div class="text-[11px] text-slate-400">Daftar mapel diambil dari <strong>Akademik → Mata Pelajaran</strong>.</div></div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-star text-yellow-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Input Nilai</div><div class="text-[11px] text-slate-400">Soal UTS/UAS yang diupload di sini menjadi acuan saat mengisi nilai di menu <strong>Akademik → Input Nilai</strong>.</div></div>
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
        $('.select2-subject').select2({ placeholder: '-- Pilih Mata Pelajaran --', allowClear: true });
    });
    function updateQS(uri, key, value) {
        var re = new RegExp("([?&])" + key + "=.*?(&|$)", "i");
        var sep = uri.indexOf('?') !== -1 ? "&" : "?";
        return uri.match(re) ? uri.replace(re, '$1' + key + "=" + value + '$2') : uri + sep + key + "=" + value;
    }
    window.onclick = function(e) { if (e.target == document.getElementById('infoModal')) document.getElementById('infoModal').classList.add('hidden'); }
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
