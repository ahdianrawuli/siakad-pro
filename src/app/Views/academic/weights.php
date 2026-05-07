<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6">

    <!-- Header -->
    <div class="mb-8 flex flex-col md:flex-row md:justify-between md:items-end gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <h3 class="text-2xl md:text-3xl font-extrabold text-slate-800 tracking-tight">Bobot Penilaian</h3>
            <p class="text-slate-500 text-sm mt-1 font-medium">
                Tahun Ajaran: <strong class="text-blue-600"><?= $year['name'] ?> (<?= $year['semester'] ?>)</strong>
            </p>
            <div class="mt-3 flex items-center gap-2">
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-blue-50 text-blue-700 rounded-lg text-xs font-bold border border-blue-100">
                    <i class="fa-solid fa-sliders"></i> Proporsi Nilai Rapor
                </div>
                <button onclick="document.getElementById('infoModal').classList.remove('hidden')"
                    class="w-7 h-7 rounded-full bg-slate-100 text-slate-500 hover:bg-blue-100 hover:text-blue-600 flex items-center justify-center transition-colors border border-slate-200" title="Panduan Penggunaan">
                    <i class="fa-solid fa-circle-info text-sm"></i>
                </button>
            </div>
        </div>
        <button form="form-weights" type="submit" class="px-5 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-semibold shadow-md shadow-blue-500/20 hover:bg-blue-700 transition-all flex items-center gap-2 w-fit">
            <i class="fa-solid fa-calculator"></i> Simpan Rumus
        </button>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <form id="form-weights" action="/academic/weights/store" method="POST">
        <?= \App\Core\Csrf::input() ?>
        <input type="hidden" name="academic_year_id" value="<?= $year['id'] ?>">

        <div class="max-w-xl flex flex-col gap-4">

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-4 bg-blue-50 border-b border-blue-100">
                    <h4 class="font-bold text-blue-800 flex items-center gap-2">
                        <i class="fa-solid fa-pen-to-square text-blue-400"></i> Bobot Harian (UH + Tugas)
                    </h4>
                    <p class="text-xs text-blue-600 mt-1">Diambil dari rata-rata UH1, UH2, dan Tugas.</p>
                </div>
                <div class="p-5 flex items-center gap-3">
                    <input type="number" name="weight_daily" value="<?= $weight['weight_daily'] ?>"
                        class="w-24 px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-center font-bold text-lg focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition-all" min="0" max="100" required>
                    <span class="font-bold text-2xl text-slate-400">%</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-4 bg-yellow-50 border-b border-yellow-100">
                    <h4 class="font-bold text-yellow-800 flex items-center gap-2">
                        <i class="fa-solid fa-file-pen text-yellow-400"></i> Bobot UTS (Tengah Semester)
                    </h4>
                </div>
                <div class="p-5 flex items-center gap-3">
                    <input type="number" name="weight_uts" value="<?= $weight['weight_uts'] ?>"
                        class="w-24 px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-center font-bold text-lg focus:bg-white focus:ring-2 focus:ring-yellow-400/50 outline-none transition-all" min="0" max="100" required>
                    <span class="font-bold text-2xl text-slate-400">%</span>
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-4 bg-green-50 border-b border-green-100">
                    <h4 class="font-bold text-green-800 flex items-center gap-2">
                        <i class="fa-solid fa-file-circle-check text-green-400"></i> Bobot UAS (Akhir Semester)
                    </h4>
                </div>
                <div class="p-5 flex items-center gap-3">
                    <input type="number" name="weight_uas" value="<?= $weight['weight_uas'] ?>"
                        class="w-24 px-3 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-center font-bold text-lg focus:bg-white focus:ring-2 focus:ring-green-400/50 outline-none transition-all" min="0" max="100" required>
                    <span class="font-bold text-2xl text-slate-400">%</span>
                </div>
            </div>

            <!-- Info total -->
            <div class="bg-slate-50 rounded-2xl border border-slate-200 p-4 text-xs text-slate-500 flex items-start gap-2">
                <i class="fa-solid fa-circle-info text-blue-400 mt-0.5"></i>
                <span>Total ketiga bobot harus berjumlah <strong class="text-slate-700">100%</strong>. Perubahan akan langsung mempengaruhi perhitungan nilai akhir di rapor.</span>
            </div>
        </div>
    </form>
</main>

<!-- Modal Info -->
<div id="infoModal" class="hidden fixed inset-0 z-50 bg-black/50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="p-5 border-b border-slate-100 bg-blue-50 flex justify-between items-center">
            <h3 class="font-bold text-blue-800 flex items-center gap-2"><i class="fa-solid fa-circle-info text-blue-500"></i> Panduan Bobot Penilaian</h3>
            <button onclick="document.getElementById('infoModal').classList.add('hidden')" class="w-8 h-8 rounded-lg bg-blue-100 text-blue-500 hover:bg-blue-200 flex items-center justify-center transition"><i class="fa-solid fa-xmark"></i></button>
        </div>
        <div class="p-6 space-y-4 text-sm text-slate-600">
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">1</span> Cara Penggunaan</h4>
                <ol class="list-decimal list-inside space-y-1.5 text-slate-500">
                    <li>Isi persentase untuk masing-masing komponen penilaian.</li>
                    <li>Pastikan total ketiga bobot berjumlah <strong class="text-slate-700">100%</strong>.</li>
                    <li>Klik <strong class="text-slate-700">Simpan Rumus</strong> — perubahan langsung berlaku.</li>
                </ol>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">2</span> Rumus Nilai Akhir</h4>
                <div class="bg-slate-50 rounded-xl p-3 border border-slate-200 font-mono text-xs text-slate-700">
                    Nilai Akhir = (Avg Harian × Bobot%) + (UTS × Bobot%) + (UAS × Bobot%)
                </div>
            </div>
            <div>
                <h4 class="font-bold text-slate-800 mb-2 flex items-center gap-2"><span class="w-6 h-6 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold">3</span> Relasi ke Menu Lain</h4>
                <div class="space-y-2">
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-star text-yellow-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Input Nilai</div><div class="text-[11px] text-slate-400">Bobot ini digunakan saat menghitung nilai akhir di <strong>Akademik → Input Nilai</strong>.</div></div>
                    </div>
                    <div class="flex items-center gap-3 p-2.5 bg-slate-50 rounded-xl border border-slate-200">
                        <i class="fa-solid fa-file-invoice text-orange-400 w-5 text-center"></i>
                        <div><div class="font-semibold text-slate-700 text-xs">Rapor Siswa</div><div class="text-[11px] text-slate-400">Nilai akhir yang tampil di rapor dihitung berdasarkan bobot ini.</div></div>
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

<?php require __DIR__ . '/../layouts/footer.php'; ?>
