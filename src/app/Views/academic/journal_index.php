<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Jurnal Guru</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">Pilih mata pelajaran untuk mengisi jurnal mengajar.</p>
            <div class="mt-3 flex items-center gap-2">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold border border-blue-100">
                    <i class="fa-solid fa-book-open"></i> Total Jadwal: <?= count($schedules) ?>
                </div>
                <button onclick="document.getElementById('infoModal').classList.remove('hidden')"
                    class="w-7 h-7 rounded-full bg-slate-100 text-slate-500 hover:bg-blue-100 hover:text-blue-600 flex items-center justify-center transition-colors border border-slate-200" title="Panduan Penggunaan">
                    <i class="fa-solid fa-circle-info text-sm"></i>
                </button>
            </div>
        </div>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <!-- Filter -->
    <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-200 mb-6">
        <form method="GET" class="flex flex-wrap items-center gap-3">
            <div class="flex-1 min-w-[220px] relative">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400"><i class="fa-solid fa-magnifying-glass"></i></span>
                <input type="text" name="search" value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" placeholder="Cari Mata Pelajaran atau Kelas..."
                    class="w-full pl-10 pr-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all">
            </div>
            <button type="submit" class="bg-slate-800 text-white px-5 py-2.5 rounded-xl text-sm font-bold hover:bg-slate-900 transition-colors">Terapkan</button>
            <?php if (!empty($_GET['search'])): ?>
                <a href="/academic/journals" class="flex items-center justify-center w-10 h-10 bg-red-50 text-red-500 rounded-xl hover:bg-red-100 transition" title="Reset">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            <?php endif; ?>
        </form>
    </div>

    <!-- Cards Grid -->
    <?php if (empty($schedules)): ?>
        <div class="text-center py-16 bg-white rounded-2xl border border-dashed border-slate-300">
            <i class="fa-solid fa-book-open text-3xl text-slate-300 mb-3"></i>
            <p class="text-slate-400 font-medium">Tidak ada jadwal yang ditemukan.</p>
        </div>
    <?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <?php foreach ($schedules as $s): ?>
        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 hover:shadow-md transition-all overflow-hidden group">
            <div class="p-5 text-white" style="background:linear-gradient(to right, var(--sc-g1,#1d4ed8), var(--sc-g2,#2563eb))">
                <div class="flex justify-between items-start">
                    <div>
                        <h4 class="font-extrabold text-lg leading-tight mb-1"><?= $s['subject_name'] ?></h4>
                        <span class="text-xs bg-white/20 px-2.5 py-1 rounded-lg font-semibold"><?= $s['class_name'] ?></span>
                    </div>
                    <div class="w-10 h-10 bg-white/20 rounded-xl flex items-center justify-center">
                        <i class="fa-solid fa-book-open text-lg"></i>
                    </div>
                </div>
            </div>
            <div class="p-5">
                <div class="flex items-center text-sm text-slate-600 mb-1.5">
                    <i class="fa-regular fa-clock w-5 text-center mr-2 text-slate-400"></i>
                    <?= $s['day'] ?>, <?= substr($s['start_time'],0,5) ?> – <?= substr($s['end_time'],0,5) ?>
                </div>
                <div class="flex items-center text-sm text-slate-600 mb-4">
                    <i class="fa-solid fa-chalkboard-user w-5 text-center mr-2 text-slate-400"></i>
                    <?= $s['total_entries'] ?> Pertemuan Tercatat
                </div>
                <a href="/academic/journals/history?schedule_id=<?= $s['id'] ?>"
                    class="block w-full bg-slate-50 text-blue-600 font-bold text-center py-2.5 rounded-xl border border-blue-100 hover:bg-blue-50 hover:border-blue-300 transition text-sm">
                    Buka Jurnal <i class="fa-solid fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</main>

<?php require __DIR__ . '/../layouts/footer.php'; ?>

<!-- Modal Info -->
<div id="infoModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-blue-50 flex justify-between items-center">
            <h3 class="font-bold text-blue-800 flex items-center gap-2"><i class="fa-solid fa-circle-info text-blue-500"></i> Panduan Jurnal Guru</h3>
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-blue-100 text-blue-500 hover:bg-blue-200 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="p-6 space-y-5 text-sm text-slate-600 overflow-y-auto max-h-[70vh]">
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">1</span> Apa itu Jurnal Guru?</h4>
                <p class="text-slate-500 leading-relaxed">Halaman ini menampilkan daftar jadwal mengajar aktif. Setiap kartu mewakili satu sesi pelajaran. Klik <strong class="text-slate-700">Buka Jurnal</strong> untuk mencatat pertemuan, materi, dan absensi siswa.</p>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">2</span> Cara Penggunaan</h4>
                <ol class="list-decimal list-inside space-y-1.5 text-slate-500">
                    <li>Gunakan kolom pencarian untuk menemukan jadwal tertentu.</li>
                    <li>Klik <strong class="text-slate-700">Buka Jurnal</strong> pada kartu jadwal yang diinginkan.</li>
                    <li>Di halaman jurnal, klik <strong class="text-slate-700">Isi Jurnal Baru</strong> untuk mencatat pertemuan.</li>
                    <li>Isi tanggal, topik materi, catatan, dan absensi tiap siswa.</li>
                </ol>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">3</span> Relasi ke Menu Lain</h4>
                <div class="space-y-2">
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-calendar-days text-blue-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Jadwal Pelajaran</div><div class="text-[11px] text-slate-400">Kartu di halaman ini berasal dari jadwal aktif di <strong>Akademik → Jadwal Pelajaran</strong>.</div></div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-calendar-check text-green-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Absensi Siswa</div><div class="text-[11px] text-slate-400">Absensi yang diisi di jurnal ini terpisah dari absensi harian di menu <strong>Kesiswaan → Absensi</strong>.</div></div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-star text-yellow-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Penilaian / Nilai</div><div class="text-[11px] text-slate-400">Jurnal ini menjadi referensi keaktifan siswa saat pengisian nilai di menu <strong>Akademik → Nilai</strong>.</div></div>
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
    window.onclick = function(e) { if (e.target == document.getElementById('infoModal')) document.getElementById('infoModal').classList.add('hidden'); }
</script>
