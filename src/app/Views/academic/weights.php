<?php require __DIR__ . '/../layouts/header.php'; ?>
<?php require __DIR__ . '/../layouts/sidebar.php'; ?>

<main class="flex-1 overflow-y-auto bg-slate-50 p-4 md:p-6" x-data="weightsApp()">

    <!-- Header -->
    <div class="mb-6 flex flex-col md:flex-row md:justify-between md:items-center gap-4 bg-white p-6 rounded-2xl shadow-sm border border-slate-200">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <h3 class="text-2xl font-extrabold text-slate-800">Bobot Penilaian</h3>
                <button onclick="document.getElementById('infoModal').classList.remove('hidden')"
                    class="w-7 h-7 rounded-full bg-slate-100 text-slate-500 hover:bg-blue-100 hover:text-blue-600 flex items-center justify-center transition-colors border border-slate-200" title="Panduan Penggunaan">
                    <i class="fa-solid fa-circle-info text-sm"></i>
                </button>
            </div>
            <p class="text-slate-500 text-sm">
                Tahun Ajaran: <strong class="text-blue-600"><?= htmlspecialchars($year['name']) ?> (<?= htmlspecialchars($year['semester']) ?>)</strong>
            </p>
        </div>
        <button form="form-weights" type="submit"
            class="flex items-center gap-2 px-5 py-2.5 bg-blue-600 text-white rounded-xl text-sm font-bold shadow-md shadow-blue-500/20 hover:bg-blue-700 transition whitespace-nowrap">
            <i class="fa-solid fa-floppy-disk"></i> Simpan Rumus
        </button>
    </div>

    <?php \App\Core\Session::flash(); ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Form -->
        <div class="lg:col-span-2">
            <form id="form-weights" action="/academic/weights/store" method="POST">
                <?= \App\Core\Csrf::input() ?>
                <input type="hidden" name="academic_year_id" value="<?= $year['id'] ?>">

                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-100">
                        <h4 class="font-bold text-slate-700">Komponen Penilaian</h4>
                        <p class="text-xs text-slate-400 mt-0.5">Total ketiga bobot harus berjumlah 100%</p>
                    </div>

                    <div class="divide-y divide-slate-100">

                        <!-- Harian -->
                        <div class="p-5 flex items-center gap-5">
                            <div class="w-12 h-12 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-pen-to-square text-lg"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-slate-800">Nilai Harian</p>
                                <p class="text-xs text-slate-400 mt-0.5">Rata-rata semua UH, Tugas, dan Quiz</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="number" name="weight_daily" x-model.number="daily"
                                    @input="updateBar()"
                                    class="w-20 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-center font-bold text-lg focus:bg-white focus:ring-2 focus:ring-blue-500/50 outline-none transition"
                                    min="0" max="100" required>
                                <span class="text-slate-400 font-bold">%</span>
                            </div>
                        </div>

                        <!-- UTS -->
                        <div class="p-5 flex items-center gap-5">
                            <div class="w-12 h-12 rounded-xl bg-yellow-100 text-yellow-600 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-file-pen text-lg"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-slate-800">UTS</p>
                                <p class="text-xs text-slate-400 mt-0.5">Ujian Tengah Semester</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="number" name="weight_uts" x-model.number="uts"
                                    @input="updateBar()"
                                    class="w-20 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-center font-bold text-lg focus:bg-white focus:ring-2 focus:ring-yellow-400/50 outline-none transition"
                                    min="0" max="100" required>
                                <span class="text-slate-400 font-bold">%</span>
                            </div>
                        </div>

                        <!-- UAS -->
                        <div class="p-5 flex items-center gap-5">
                            <div class="w-12 h-12 rounded-xl bg-green-100 text-green-600 flex items-center justify-center shrink-0">
                                <i class="fa-solid fa-file-circle-check text-lg"></i>
                            </div>
                            <div class="flex-1">
                                <p class="font-semibold text-slate-800">UAS</p>
                                <p class="text-xs text-slate-400 mt-0.5">Ujian Akhir Semester</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <input type="number" name="weight_uas" x-model.number="uas"
                                    @input="updateBar()"
                                    class="w-20 px-3 py-2 bg-slate-50 border border-slate-200 rounded-xl text-center font-bold text-lg focus:bg-white focus:ring-2 focus:ring-green-400/50 outline-none transition"
                                    min="0" max="100" required>
                                <span class="text-slate-400 font-bold">%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Total bar -->
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100">
                        <div class="flex items-center justify-between mb-2">
                            <span class="text-xs font-semibold text-slate-500">Total Bobot</span>
                            <span class="text-sm font-bold" :class="total == 100 ? 'text-green-600' : 'text-red-500'" x-text="total + '%'"></span>
                        </div>
                        <div class="h-3 bg-slate-200 rounded-full overflow-hidden flex gap-0.5">
                            <div class="bg-blue-500 h-full rounded-l-full transition-all duration-300" :style="'width:' + daily + '%'"></div>
                            <div class="bg-yellow-400 h-full transition-all duration-300" :style="'width:' + uts + '%'"></div>
                            <div class="bg-green-500 h-full rounded-r-full transition-all duration-300" :style="'width:' + uas + '%'"></div>
                        </div>
                        <div class="flex gap-4 mt-2 text-xs text-slate-400">
                            <span><span class="inline-block w-2 h-2 rounded-full bg-blue-500 mr-1"></span>Harian</span>
                            <span><span class="inline-block w-2 h-2 rounded-full bg-yellow-400 mr-1"></span>UTS</span>
                            <span><span class="inline-block w-2 h-2 rounded-full bg-green-500 mr-1"></span>UAS</span>
                        </div>
                        <p x-show="total != 100" class="mt-2 text-xs text-red-500 font-medium">
                            <i class="fa-solid fa-triangle-exclamation mr-1"></i>Total harus 100%. Saat ini <span x-text="total"></span>%.
                        </p>
                    </div>
                </div>
            </form>
        </div>

        <!-- Preview rumus -->
        <div class="space-y-4">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                <h4 class="font-bold text-slate-700 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-calculator text-slate-400"></i> Preview Rumus
                </h4>
                <div class="bg-slate-50 rounded-xl p-4 border border-slate-200 font-mono text-xs text-slate-700 leading-relaxed">
                    Nilai Akhir =<br>
                    &nbsp;&nbsp;(Harian × <span class="text-blue-600 font-bold" x-text="daily"></span>%) +<br>
                    &nbsp;&nbsp;(UTS × <span class="text-yellow-600 font-bold" x-text="uts"></span>%) +<br>
                    &nbsp;&nbsp;(UAS × <span class="text-green-600 font-bold" x-text="uas"></span>%)
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                <h4 class="font-bold text-slate-700 mb-3 flex items-center gap-2">
                    <i class="fa-solid fa-lightbulb text-yellow-400"></i> Contoh Perhitungan
                </h4>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between text-slate-500">
                        <span>Harian = 80</span>
                        <span class="font-medium text-blue-600" x-text="'+ ' + (80 * daily / 100).toFixed(1)"></span>
                    </div>
                    <div class="flex justify-between text-slate-500">
                        <span>UTS = 75</span>
                        <span class="font-medium text-yellow-600" x-text="'+ ' + (75 * uts / 100).toFixed(1)"></span>
                    </div>
                    <div class="flex justify-between text-slate-500">
                        <span>UAS = 85</span>
                        <span class="font-medium text-green-600" x-text="'+ ' + (85 * uas / 100).toFixed(1)"></span>
                    </div>
                    <div class="border-t border-slate-100 pt-2 flex justify-between font-bold text-slate-800">
                        <span>Nilai Akhir</span>
                        <span x-text="((80 * daily / 100) + (75 * uts / 100) + (85 * uas / 100)).toFixed(1)"></span>
                    </div>
                </div>
            </div>
        </div>

    </div>
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
function weightsApp() {
    return {
        daily: <?= (int)($weight['weight_daily'] ?? 40) ?>,
        uts:   <?= (int)($weight['weight_uts']   ?? 30) ?>,
        uas:   <?= (int)($weight['weight_uas']   ?? 30) ?>,
        get total() { return this.daily + this.uts + this.uas; },
        updateBar() {}
    }
}
window.onclick = function(e) { if (e.target == document.getElementById('infoModal')) document.getElementById('infoModal').classList.add('hidden'); }
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>
